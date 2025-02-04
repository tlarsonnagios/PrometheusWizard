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
            # Return the value from the first result as a float
            return float(data["data"]["result"][0]["value"][1])
        return None
    except requests.exceptions.RequestException as e:
        print(f"CRITICAL: Failed to query Prometheus - {e}")
        sys.exit(2)

def check_metrics(args):
    prometheus_url = f"http://{args.prometheus_host}:9090"

    status = 0
    messages = []

    # If an instance is specified, create a filter string to append to the PromQL queries.
    # The instance value should be in the format "IP:PORT"
    instance_filter = ""
    if args.instance:
        instance_filter = f",instance='{args.instance}'"

    # CPU usage
    if args.cpu:
        if args.instance:
            # Query CPU for a specific instance (no averaging)
            cpu_query = f"100 - (rate(node_cpu_seconds_total{{mode='idle'{instance_filter}}}[5m]) * 100)"
        else:
            # Aggregate CPU usage across all instances
            cpu_query = "100 - (avg by(instance) (rate(node_cpu_seconds_total{mode='idle'}[5m])) * 100)"
        cpu_usage = get_metric(prometheus_url, cpu_query)
        if cpu_usage is not None:
            if cpu_usage > args.critical_cpu:
                messages.append(f"CRITICAL: CPU at {cpu_usage:.2f}%")
                status = 2
            elif cpu_usage > args.warning_cpu:
                messages.append(f"WARNING: CPU at {cpu_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: CPU at {cpu_usage:.2f}%")
        else:
            messages.append("UNKNOWN: CPU metric not found")

    # Memory usage
    if args.mem:
        if args.instance:
            mem_query = (
                f"(node_memory_MemTotal_bytes{{instance='{args.instance}'}} - node_memory_MemAvailable_bytes{{instance='{args.instance}'}})"
                f" / node_memory_MemTotal_bytes{{instance='{args.instance}'}} * 100"
            )
        else:
            mem_query = "(node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes * 100"
        mem_usage = get_metric(prometheus_url, mem_query)
        if mem_usage is not None:
            if mem_usage > args.critical_mem:
                messages.append(f"CRITICAL: Memory at {mem_usage:.2f}%")
                status = 2
            elif mem_usage > args.warning_mem:
                messages.append(f"WARNING: Memory at {mem_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: Memory at {mem_usage:.2f}%")
        else:
            messages.append("UNKNOWN: Memory metric not found")

    # Disk usage
    if args.disk:
        if args.instance:
            disk_query = (
                f"(node_filesystem_size_bytes{{mountpoint='/' , instance='{args.instance}'}} - node_filesystem_avail_bytes{{mountpoint='/' , instance='{args.instance}'}})"
                f" / node_filesystem_size_bytes{{mountpoint='/' , instance='{args.instance}'}} * 100"
            )
        else:
            disk_query = "(node_filesystem_size_bytes{mountpoint='/'} - node_filesystem_avail_bytes{mountpoint='/'}) / node_filesystem_size_bytes{mountpoint='/'} * 100"
        disk_usage = get_metric(prometheus_url, disk_query)
        if disk_usage is not None:
            if disk_usage > args.critical_disk:
                messages.append(f"CRITICAL: Disk at {disk_usage:.2f}%")
                status = 2
            elif disk_usage > args.warning_disk:
                messages.append(f"WARNING: Disk at {disk_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: Disk at {disk_usage:.2f}%")
        else:
            messages.append("UNKNOWN: Disk metric not found")

    # Load average (1-minute load)
    if args.load:
        if args.instance:
            load_query = f"node_load1{{instance='{args.instance}'}}"
        else:
            load_query = "avg by(instance) (node_load1)"
        load_avg = get_metric(prometheus_url, load_query)
        if load_avg is not None:
            if load_avg > args.critical_load:
                messages.append(f"CRITICAL: Load at {load_avg:.2f}")
                status = 2
            elif load_avg > args.warning_load:
                messages.append(f"WARNING: Load at {load_avg:.2f}")
                status = max(status, 1)
            else:
                messages.append(f"OK: Load at {load_avg:.2f}")
        else:
            messages.append("UNKNOWN: Load metric not found")

    if not messages:
        print("UNKNOWN: No metrics selected. Use --cpu, --mem, --disk, or --load")
        sys.exit(3)

    print(" | ".join(messages))
    sys.exit(status)

def main():
    parser = argparse.ArgumentParser(
        description="Nagios plugin for monitoring system metrics via Prometheus with optional per-instance filtering"
    )
    parser.add_argument("--prometheus-host", required=True, help="Prometheus server IP or hostname")
    parser.add_argument("--instance", help="Specify a Node Exporter instance (IP:PORT) to query")
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
