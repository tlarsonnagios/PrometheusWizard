#!/usr/bin/env python3
import requests
import argparse
import sys

# Default thresholds for alerts
DEFAULT_THRESHOLDS = {
    "cpu": {"warning": 80, "critical": 90},
    "mem": {"warning": 70, "critical": 90},
    "disk": {"warning": 80, "critical": 90},
    "load": {"warning": 2.0, "critical": 5.0},
}

def get_metric(prometheus_url, query):
    """Fetch a metric value using a Prometheus API query."""
    try:
        response = requests.get(f"{prometheus_url}/api/v1/query", params={"query": query})
        response.raise_for_status()
        data = response.json()
        if data and "data" in data and "result" in data["data"] and len(data["data"]["result"]) > 0:
            return float(data["data"]["result"][0]["value"][1])
        return None
    except requests.exceptions.RequestException as e:
        print(f"CRITICAL: Failed to query Prometheus - {e}")
        sys.exit(2)

def check_metrics(args):
    prometheus_url = f"http://{args.prometheus_host}:9090"

    status = 0
    messages = []
    instance_filter = f",instance='{args.instance}'" if args.instance else ""

    def add_message(condition, critical_msg, warning_msg, ok_msg, value):
        nonlocal status
        if value is not None:
            if value > condition["critical"]:
                messages.append(f"CRITICAL: {critical_msg} {value:.2f}%")
                status = 2
            elif value > condition["warning"]:
                messages.append(f"WARNING: {warning_msg} {value:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: {ok_msg} {value:.2f}%")
        else:
            messages.append(f"UNKNOWN: {ok_msg} metric not found")
            status = 3  # Update status to UNKNOWN if a metric is not found
    
    if args.cpu:
        query = f"100 - (rate(node_cpu_seconds_total{{mode='idle'{instance_filter}}}[5m]) * 100)" if args.instance else "100 - (avg by(instance) (rate(node_cpu_seconds_total{mode='idle'}[5m])) * 100)"
        add_message(DEFAULT_THRESHOLDS["cpu"], "CPU at", "CPU at", "CPU at", get_metric(prometheus_url, query))
    
    if args.mem:
        query = f"(node_memory_MemTotal_bytes{{instance='{args.instance}'}} - node_memory_MemAvailable_bytes{{instance='{args.instance}'}}) / node_memory_MemTotal_bytes{{instance='{args.instance}'}} * 100" if args.instance else "(node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes * 100"
        add_message(DEFAULT_THRESHOLDS["mem"], "Memory at", "Memory at", "Memory at", get_metric(prometheus_url, query))
    
    if args.disk:
        query = f"(node_filesystem_size_bytes{{mountpoint='/'{instance_filter}}} - node_filesystem_avail_bytes{{mountpoint='/'{instance_filter}}}) / node_filesystem_size_bytes{{mountpoint='/'{instance_filter}}} * 100"
        add_message(DEFAULT_THRESHOLDS["disk"], "Disk at", "Disk at", "Disk at", get_metric(prometheus_url, query))
    
    if args.load:
        query = f"node_load1{{instance='{args.instance}'}}" if args.instance else "avg by(instance) (node_load1)"
        add_message(DEFAULT_THRESHOLDS["load"], "Load at", "Load at", "Load at", get_metric(prometheus_url, query))
    
    if args.custom:
        custom_value = get_metric(prometheus_url, args.custom)
        if custom_value is not None:
            if custom_value > args.critical_custom:
                messages.append(f"CRITICAL: Custom metric at {custom_value:.2f}")
                status = 2
            elif custom_value > args.warning_custom:
                messages.append(f"WARNING: Custom metric at {custom_value:.2f}")
                status = max(status, 1)
            else:
                messages.append(f"OK: Custom metric at {custom_value:.2f}")
        else:
            messages.append("UNKNOWN: Custom metric not found")
            status = 3  # Ensure the status is set to UNKNOWN if custom metric is not found
    
    if not messages:
        print("UNKNOWN: No metrics selected or an unknown error occurred, use --cpu --mem --disk --load or --custom")
        sys.exit(3)
    
    print(", ".join(messages) + " |")
    sys.exit(status)

def main():
    parser = argparse.ArgumentParser(description="Nagios plugin for monitoring system metrics via Prometheus with optional per-instance filtering")
    parser.add_argument("--prometheus-host", required=True, help="Prometheus server IP or hostname")
    parser.add_argument("--instance", required=True, help="Specify a Node Exporter instance (IP:PORT) to query")
    parser.add_argument("--cpu", action="store_true", help="Check CPU usage")
    parser.add_argument("--mem", action="store_true", help="Check Memory usage")
    parser.add_argument("--disk", action="store_true", help="Check Disk usage")
    parser.add_argument("--load", action="store_true", help="Check Load Average")
    parser.add_argument("--custom", type=str, help="Custom PromQL query for a metric from the /metrics page")

    for key in DEFAULT_THRESHOLDS:
        parser.add_argument(f"--warning-{key}", type=float, default=DEFAULT_THRESHOLDS[key]["warning"], help=f"{key.capitalize()} warning threshold")
        parser.add_argument(f"--critical-{key}", type=float, default=DEFAULT_THRESHOLDS[key]["critical"], help=f"{key.capitalize()} critical threshold")
    
    parser.add_argument("--warning-custom", type=float, help="Custom metric warning threshold (requires --custom)")
    parser.add_argument("--critical-custom", type=float, help="Custom metric critical threshold (requires --custom)")
    
    args = parser.parse_args()
    
    if args.custom and (args.warning_custom is None or args.critical_custom is None):
        print("ERROR: When using --custom, both --warning-custom and --critical-custom thresholds must be provided.")
        sys.exit(3)
    
    check_metrics(args)

if __name__ == "__main__":
    main()