#!/usr/bin/env python3

import requests
import argparse
import sys
import time

# Function to query Prometheus Node Exporter metrics
def get_metrics(prometheus_url):
    try:
        response = requests.get(prometheus_url)
        response.raise_for_status()
        return response.text
    except requests.exceptions.RequestException as e:
        print(f"CRITICAL: Failed to query Prometheus - {e}")
        sys.exit(2)

# Function to parse metrics
def parse_metrics(metrics_text):
    metrics = {}
    for line in metrics_text.splitlines():
        if line.startswith('#') or not line.strip():
            continue
        parts = line.split()
        if len(parts) == 2:
            metrics[parts[0]] = float(parts[1])
    return metrics

# Function to check CPU usage
def check_cpu(metrics, previous_metrics, warning, critical):
    cpu_idle = sum(value for key, value in metrics.items() if 'node_cpu_seconds_total' in key and 'idle' in key)
    cpu_total = sum(value for key, value in metrics.items() if 'node_cpu_seconds_total' in key)
    
    prev_cpu_idle = sum(value for key, value in previous_metrics.items() if 'node_cpu_seconds_total' in key and 'idle' in key)
    prev_cpu_total = sum(value for key, value in previous_metrics.items() if 'node_cpu_seconds_total' in key)
    
    cpu_usage = 100 * (1 - ((cpu_idle - prev_cpu_idle) / (cpu_total - prev_cpu_total)))
    
    if cpu_usage >= critical:
        return f"CRITICAL: CPU usage is {cpu_usage:.2f}%", 2
    elif cpu_usage >= warning:
        return f"WARNING: CPU usage is {cpu_usage:.2f}%", 1
    else:
        return f"OK: CPU usage is {cpu_usage:.2f}%", 0

# Function to check memory usage
def check_memory(metrics, warning, critical):
    mem_total = metrics.get('node_memory_MemTotal_bytes', 0)
    mem_available = metrics.get('node_memory_MemAvailable_bytes', 0)
    mem_usage = 100 * (1 - (mem_available / mem_total))
    
    if mem_usage >= critical:
        return f"CRITICAL: Memory usage is {mem_usage:.2f}%", 2
    elif mem_usage >= warning:
        return f"WARNING: Memory usage is {mem_usage:.2f}%", 1
    else:
        return f"OK: Memory usage is {mem_usage:.2f}%", 0

# Function to check disk usage
def check_disk(metrics, warning, critical):
    disk_total = sum(value for key, value in metrics.items() if 'node_filesystem_size_bytes' in key)
    disk_free = sum(value for key, value in metrics.items() if 'node_filesystem_free_bytes' in key)
    disk_usage = 100 * (1 - (disk_free / disk_total))
    
    if disk_usage >= critical:
        return f"CRITICAL: Disk usage is {disk_usage:.2f}%", 2
    elif disk_usage >= warning:
        return f"WARNING: Disk usage is {disk_usage:.2f}%", 1
    else:
        return f"OK: Disk usage is {disk_usage:.2f}%", 0

# Function to check custom metric
def check_custom_metric(metrics, custom_metric, warning, critical):
    value = metrics.get(custom_metric, None)
    if value is None:
        return f"CRITICAL: Custom metric {custom_metric} not found", 2
    
    if value >= critical:
        return f"CRITICAL: {custom_metric} is {value}", 2
    elif value >= warning:
        return f"WARNING: {custom_metric} is {value}", 1
    else:
        return f"OK: {custom_metric} is {value}", 0

# Main function to check metrics
def check_metrics(args):
    prometheus_url = f"http://{args.ip}:{args.port}/metrics"
    
    # Get initial metrics
    metrics_text = get_metrics(prometheus_url)
    metrics = parse_metrics(metrics_text)
    
    # Wait for the interval to get the next set of metrics
    time.sleep(args.interval)
    
    # Get the next set of metrics
    metrics_text = get_metrics(prometheus_url)
    new_metrics = parse_metrics(metrics_text)

    status = 0
    results = []

    if args.cpu:
        result, code = check_cpu(new_metrics, metrics, args.cpu_warning, args.cpu_critical)
        results.append(result)
        status = max(status, code)

    if args.mem:
        result, code = check_memory(new_metrics, args.mem_warning, args.mem_critical)
        results.append(result)
        status = max(status, code)

    if args.disk:
        result, code = check_disk(new_metrics, args.disk_warning, args.disk_critical)
        results.append(result)
        status = max(status, code)

    if args.custom_metric:
        result, code = check_custom_metric(new_metrics, args.custom_metric, args.custom_warning, args.custom_critical)
        results.append(result)
        status = max(status, code)

    print(", ".join(results) + " |")
    sys.exit(status)

# Argument parsing
def parse_arguments():
    parser = argparse.ArgumentParser(description="Check Prometheus Node Exporter metrics.")
    parser.add_argument("-H", "--ip", required=True, help="IP address of the Node Exporter machine")
    parser.add_argument("-P", "--port", required=True, help="Port of the Node Exporter machine")
    parser.add_argument("--cpu", action="store_true", help="Check CPU usage")
    parser.add_argument("--mem", action="store_true", help="Check memory usage")
    parser.add_argument("--disk", action="store_true", help="Check disk usage")
    parser.add_argument("--custom-metric", help="Check custom metric")
    parser.add_argument("--interval", type=int, default=5, help="Interval in seconds between metric checks")
    parser.add_argument("--cpu-warning", type=float, default=70.0, help="Warning threshold for CPU usage")
    parser.add_argument("--cpu-critical", type=float, default=90.0, help="Critical threshold for CPU usage")
    parser.add_argument("--mem-warning", type=float, default=70.0, help="Warning threshold for memory usage")
    parser.add_argument("--mem-critical", type=float, default=90.0, help="Critical threshold for memory usage")
    parser.add_argument("--disk-warning", type=float, default=70.0, help="Warning threshold for disk usage")
    parser.add_argument("--disk-critical", type=float, default=90.0, help="Critical threshold for disk usage")
    parser.add_argument("--custom-warning", type=float, default=70.0, help="Warning threshold for custom metric")
    parser.add_argument("--custom-critical", type=float, default=90.0, help="Critical threshold for custom metric")
    return parser.parse_args()

if __name__ == "__main__":
    args = parse_arguments()
    check_metrics(args)