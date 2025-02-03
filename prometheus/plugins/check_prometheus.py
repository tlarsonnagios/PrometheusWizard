#!/usr/bin/env python3

import requests
import argparse
import sys

# Default thresholds
DEFAULT_THRESHOLDS = {
    "cpu": {"warning": 80, "critical": 90},
    "mem": {"warning": 70, "critical": 90},
    "disk": {"warning": 80, "critical": 90},
    "load": {"warning": 2.0, "critical": 5.0},
}

# Function to query Prometheus API
def get_metric(prometheus_url, query):
    """Fetch metric value using Prometheus API query."""
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

# Main function to check metrics
def check_metrics(args):
    prometheus_url = f"http://{args.prometheus_host}:9090"  # Use Prometheus, not Node Exporter

    status = 0
    messages = []

    if args.cpu:
        cpu_usage = get_metric(prometheus_url, "100 - (avg by(instance) (rate(node_cpu_seconds_total{mode='idle'}[5m])) * 100)")
        if cpu_usage is not None:
            if cpu_usage > args.critical_cpu:
                messages.append(f"CRITICAL: CPU at {cpu_usage:.2f}%")
                status = 2
            elif cpu_usage > args.warning_cpu:
                messages.append(f"WARNING: CPU at {cpu_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: CPU at {cpu_usage:.2f}%")

    if args.mem:
        mem_usage = get_metric(prometheus_url, "(node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes * 100")
        if mem_usage is not None:
            if mem_usage > args.critical_mem:
                messages.append(f"CRITICAL: Memory at {mem_usage:.2f}%")
                status = 2
            elif mem_usage > args.warning_mem:
                messages.append(f"WARNING: Memory at {mem_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: Memory at {mem_usage:.2f}%")

    if args.disk:
        disk_usage = get_metric(prometheus_url, "(node_filesystem_size_bytes{mountpoint='/'} - node_filesystem_avail_bytes{mountpoint='/'}) / node_filesystem_size_bytes{mountpoint='/'} * 100")
        if disk_usage is not None:
            if disk_usage > args.critical_disk:
                messages.append(f"CRITICAL: Disk at {disk_usage:.2f}%")
                status = 2
            elif disk_usage > args.warning_disk:
                messages.append(f"WARNING: Disk at {disk_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: Disk at {disk_usage:.2f}%")

    if args.load:
        load_avg = get_metric(prometheus_url, "node_load1")
        if load_avg is not None:
            if load_avg > args.critical_load:
                messages.append(f"CRITICAL: Load at {load_avg:.2f}")
                status = 2
            elif load_avg > args.warning_load:
                messages.append(f"WARNING: Load at {load_avg:.2f}")
                status = max(status, 1)
            else:
                messages.append(f"OK: Load at {load_avg:.2f}")

    if not messages:
        print("UNKNOWN: No metrics selected. Use --cpu, --mem, --disk, or --load")
        sys.exit(3)

    print(" | ".join(messages))
    sys.exit(status)

# Parse command-line arguments
def main():
    parser = argparse.ArgumentParser(description="Nagios plugin for monitoring system metrics via Prometheus")

    parser.add_argument("--prometheus-host", required=True, help="Prometheus server IP or hostname")

    parser.add_argument("--cpu", action="store_true", help="Check CPU usage")
    parser.add_argument("--mem", action="store_true", help="Check Memory usage")
    parser.add_argument("--disk", action="store_true", help="Check Disk usage")
    parser.add_argument("--load", action="store_true", help="Check Load Average")

    parser.add_argument("--warning-cpu", type=float, default=DEFAULT_THRESHOLDS["cpu"]["warning"], help="CPU warning threshold")
    parser.add_argument("--critical-cpu", type=float, default=DEFAULT_THRESHOLDS["cpu"]["critical"], help="CPU critical threshold")

    parser.add_argument("--warning-mem", type=float, default=DEFAULT_THRESHOLDS["mem"]["warning"], help="Memory warning threshold")
    parser.add_argument("--critical-mem", type=float, default=DEFAULT_THRESHOLDS["mem"]["critical"], help="Memory critical threshold")

    parser.add_argument("--warning-disk", type=float, default=DEFAULT_THRESHOLDS["disk"]["warning"], help="Disk warning threshold")
    parser.add_argument("--critical-disk", type=float, default=DEFAULT_THRESHOLDS["disk"]["critical"], help="Disk critical threshold")

    parser.add_argument("--warning-load", type=float, default=DEFAULT_THRESHOLDS["load"]["warning"], help="Load warning threshold")
    parser.add_argument("--critical-load", type=float, default=DEFAULT_THRESHOLDS["load"]["critical"], help="Load critical threshold")

    args = parser.parse_args()
    check_metrics(args)

if __name__ == "__main__":
    main()
