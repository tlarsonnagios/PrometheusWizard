#!/usr/bin/env python3
import requests
import argparse
import sys

# Default thresholds for alerts
DEFAULT_THRESHOLDS = {
    "cpu": {"warning": 80, "critical": 90},
    "mem": {"warning": 70, "critical": 90},
    "disk": {"warning": 80, "critical": 90},
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

    instance_filter = f", instance='{args.instance}'" if args.instance else ""

    # CPU usage
    if args.cpu:
        cpu_query = f"100 - (avg by(instance) (rate(windows_cpu_time_total{{mode='idle'{instance_filter}}}[5m])) * 100)"
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
        mem_query = f"windows_memory_physical_free_bytes{{instance='{args.instance}'}} / windows_memory_physical_total_bytes{{instance='{args.instance}'}}"

        mem_usage = get_metric(prometheus_url, mem_query)
        if mem_usage is not None and mem_usage != 0:
            mem_usage = 100 - mem_usage  # Convert free memory to used memory
            if mem_usage > args.critical_mem:
                messages.append(f"CRITICAL: Memory at {mem_usage:.2f}%")
                status = 2
            elif mem_usage > args.warning_mem:
                messages.append(f"WARNING: Memory at {mem_usage:.2f}%")
                status = max(status, 1)
            else:
                messages.append(f"OK: Memory at {mem_usage:.2f}%")
        else:
            print("Error: No value returned for memory metric.")  # Debugging message
            messages.append("UNKNOWN: Memory metric not found")


    # Disk usage
    if args.disk:
        disk_query = (
            f"(windows_logical_disk_size_bytes{{volume='C:'{instance_filter}}} - windows_logical_disk_free_bytes{{volume='C:'{instance_filter}}})"
            f" / windows_logical_disk_size_bytes{{volume='C:'{instance_filter}}} * 100"
        )
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

    # Custom metric
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

    if not messages:
        print("UNKNOWN: No metrics selected. Use --cpu, --mem, --disk, or --custom")
        sys.exit(3)

    print(", ".join(messages) + " |")
    sys.exit(status)

def main():
    parser = argparse.ArgumentParser(
        description="Nagios plugin for monitoring Windows system metrics via Prometheus with optional per-instance filtering"
    )
    parser.add_argument("--prometheus-host", required=True, help="Prometheus server IP or hostname")
    parser.add_argument("--instance", help="Specify a Windows Exporter instance (IP:PORT) to query")
    parser.add_argument("--cpu", action="store_true", help="Check CPU usage")
    parser.add_argument("--mem", action="store_true", help="Check Memory usage")
    parser.add_argument("--disk", action="store_true", help="Check Disk usage")
    parser.add_argument("--custom", type=str, help="Custom PromQL query for a metric")

    parser.add_argument("--warning-cpu", type=float, default=DEFAULT_THRESHOLDS["cpu"]["warning"], help="CPU warning threshold")
    parser.add_argument("--critical-cpu", type=float, default=DEFAULT_THRESHOLDS["cpu"]["critical"], help="CPU critical threshold")

    parser.add_argument("--warning-mem", type=float, default=DEFAULT_THRESHOLDS["mem"]["warning"], help="Memory warning threshold")
    parser.add_argument("--critical-mem", type=float, default=DEFAULT_THRESHOLDS["mem"]["critical"], help="Memory critical threshold")

    parser.add_argument("--warning-disk", type=float, default=DEFAULT_THRESHOLDS["disk"]["warning"], help="Disk warning threshold")
    parser.add_argument("--critical-disk", type=float, default=DEFAULT_THRESHOLDS["disk"]["critical"], help="Disk critical threshold")

    parser.add_argument("--warning-custom", type=float, help="Custom metric warning threshold")
    parser.add_argument("--critical-custom", type=float, help="Custom metric critical threshold")

    args = parser.parse_args()

    if args.custom:
        if args.warning_custom is None or args.critical_custom is None:
            print("ERROR: When using --custom, both --warning-custom and --critical-custom must be provided.")
            sys.exit(3)

    check_metrics(args)

if __name__ == "__main__":
    main()
