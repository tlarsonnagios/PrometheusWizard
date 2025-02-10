<?php
//
// Prometheus Config Wizard
// Copyright (c) 2025 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../configwizardhelper.inc.php');

prometheus_linux_configwizard_init();

function prometheus_linux_configwizard_init()
{
    $name = "prometheus";
    $args = array(
        CONFIGWIZARD_NAME => $name,
        CONFIGWIZARD_VERSION => "1.0.0",
        CONFIGWIZARD_TYPE => CONFIGWIZARD_TYPE_MONITORING,
        CONFIGWIZARD_DESCRIPTION => _("Monitor Linux hosts with Prometheus Node Exporter."),
        CONFIGWIZARD_DISPLAYTITLE => _("Prometheus Linux"),
        CONFIGWIZARD_FUNCTION => "prometheus_linux_configwizard_func",
        CONFIGWIZARD_PREVIEWIMAGE => "prometheus.png",
        CONFIGWIZARD_FILTER_GROUPS => array('linux'),
        CONFIGWIZARD_REQUIRES_VERSION => 60100
    );
    register_configwizard($name, $args);
}

/**
 * @param string $mode
 * @param null   $inargs
 * @param        $outargs
 * @param        $result
 *
 * @return string
 */
function prometheus_linux_configwizard_func($mode = "", $inargs = null, &$outargs = null, &$result = null)
{
    $wizard_name = "prometheus_linux";

    // initialize return code and output
    $result = 0;
    $output = "";

    // Debug: Print current mode and input arguments
    print "Prometheus Wizard Mode: " . $mode . "<br>\n";

    // initialize output args - pass back the same data we got
    $outargs[CONFIGWIZARD_PASSBACK_DATA] = $inargs;

    switch ($mode) {
        case CONFIGWIZARD_MODE_GETSTAGE1HTML:
            ob_start();
            include __DIR__.'/steps/step1.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE1DATA:
            // Get variables that were passed to us
            $linux_hosts = grab_array_var($inargs, "linux_hosts", "");
            $port = grab_array_var($inargs, "port", 9100);

            print "Stage 1 Validation - Linux Hosts: " . $linux_hosts . ", Linux Host Port: " . $port . "<br>\n";

            // Check for errors
            $errors = 0;
            $errmsg = array();

            if ($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE2HTML:            
            // Get variables that were passed to us
            $linux_hosts = grab_array_var($inargs, "linux_hosts");
            $port = grab_array_var($inargs, "port", 9100);

            // Encode all data for passing through
            $linux_hosts_serial = base64_encode($linux_hosts);

            print "Stage 2 HTML - Linux Hosts: " . $linux_hosts . ", Linux Host Port: " . $port . "<br>\n";

            ob_start();
            include __DIR__.'/steps/step2.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE2DATA:
            print "Stage 2 Validation - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";

            // Get variables that were passed to us
            $linux_hosts = grab_array_var($inargs, "linux_hosts");
            $port = grab_array_var($inargs, "port", 9100);

            print "Stage 2 Validation - Linux Hosts: " . $linux_hosts . "<br>\n";

            // Check for errors
            $errors = 0;
            $errmsg = array();

            if ($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE3HTML:
            print "Stage 3 HTML - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";

            // get variables that were passed to us
            $linux_hosts = grab_array_var($inargs, "linux_hosts");
            $port = grab_array_var($inargs, "port", 9100);
            $linux_services = grab_array_var($inargs, "linux_services");
            $linux_serviceargs = grab_array_var($inargs, "linux_serviceargs");
            $custom_linux_metrics = grab_array_var($inargs, "custom_linux_metrics");

            // Encode all data for passing through
            $linux_hosts_serial = base64_encode($linux_hosts);
            $linux_services_serial = base64_encode(json_encode($linux_services));
            $linux_serviceargs_serial = base64_encode(json_encode($linux_serviceargs));
            $custom_linux_metrics_serial = base64_encode(json_encode($custom_linux_metrics));

            print "Stage 3 HTML - Data being passed through:<br>\n";
            print "Linux Services: <pre>" . print_r($linux_services, true) . "</pre><br>\n";
            print "Linux Service Args: <pre>" . print_r($linux_serviceargs, true) . "</pre><br>\n";

            $output = '
                <input type="hidden" name="linux_hosts_serial" value="' . $linux_hosts_serial . '">
                <input type="hidden" name="port" value="' . $port . '">
                <input type="hidden" name="linux_services_serial" value="' . $linux_services_serial . '">
                <input type="hidden" name="linux_serviceargs_serial" value="' . $linux_serviceargs_serial . '">
                <input type="hidden" name="custom_linux_metrics_serial" value="' . $custom_linux_metrics_serial . '">
            ';

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE3DATA:
            break;

        case CONFIGWIZARD_MODE_GETFINALSTAGEHTML:
            $output = '';
            break;

        case CONFIGWIZARD_MODE_GETOBJECTS:
            // Get all input data
            $linux_hosts_serial = grab_array_var($inargs, "linux_hosts_serial", "");
            $port = grab_array_var($inargs, "port", 9100);
            $linux_services_serial = grab_array_var($inargs, "linux_services_serial", "");
            $linux_serviceargs_serial = grab_array_var($inargs, "linux_serviceargs_serial", "");
            $custom_linux_metrics_serial = grab_array_var($inargs, "custom_linux_metrics_serial", "");

            // Decode all serialized data
            $linux_hosts = base64_decode($linux_hosts_serial);
            $linux_services = json_decode(base64_decode($linux_services_serial), true);
            $linux_serviceargs = json_decode(base64_decode($linux_serviceargs_serial), true);
            $custom_linux_metrics = json_decode(base64_decode($custom_linux_metrics_serial), true);

            // Debug output
            print "Get Objects - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";
            print "Linux Hosts: " . $linux_hosts . "<br>\n";
            print "Linux Host Port: " . $port . "<br>\n";
            print "Linux Services: <pre>" . print_r($linux_services, true) . "</pre><br>\n";
            print "Linux Service Args: <pre>" . print_r($linux_serviceargs, true) . "</pre><br>\n";
            print "Custom Linux Metrics: <pre>" . print_r($custom_linux_metrics, true) . "</pre><br>\n";

            // save data for later use in re-entrance
            $meta_arr = array();
            $meta_arr["linux_hosts"] = $linux_hosts;
            $meta_arr["linux_services"] = $linux_services;
            $meta_arr["linux_serviceargs"] = $linux_serviceargs;
            $meta_arr["custom_linux_metrics"] = $custom_linux_metrics;
            save_configwizard_object_meta($wizard_name, $hostname, "", $meta_arr);

            $objs = array();

            // If no Linux hosts are selected, break
            if (empty($linux_hosts)) {
                break;
            }

            // Add Linux hosts and their services
            $linux_host_list = explode("\n", trim($linux_hosts));
            foreach ($linux_host_list as $linux_host) {
                $linux_host = trim($linux_host);
                if (empty($linux_host)) {
                    continue;
                }

                // Add Linux host
                $linux_host_name = "Prometheus Linux Host " . $linux_host;
                if (!host_exists($linux_host)) {
                    $objs[] = array(
                        "type" => OBJECTTYPE_HOST,
                        "use" => "xiwizard_prometheus_linux_host",
                        "host_name" => $linux_host_name,
                        "address" => $linux_host,
                        "icon_image" => "prometheus.png",
                        "statusmap_image" => "prometheus.png",
                        "_xiwizard" => $wizard_name,
                    );
                }

                // Build the check commands with all enabled metrics
                foreach ($linux_services as $svc => $svcstate) {
                    if (empty($svcstate) || $svcstate !== "on") {
                        continue;
                    }

                    $linux_check_command = "check_prometheus_linux!-H " . $linux_host . " -P " . $port . " ";
                    $linux_service_description = "";
                    
                    switch ($svc) {
                        case "cpu":
                            $linux_check_command .= "--cpu --cpu-warning " . $linux_serviceargs["cpu"]["warning"] . " --cpu-critical " . $linux_serviceargs["cpu"]["critical"] . " ";
                            $linux_service_description = "CPU Usage";
                            break;
                        case "memory":
                            $linux_check_command .= "--mem --mem-warning " . $linux_serviceargs["memory"]["warning"] . " --mem-critical " . $linux_serviceargs["memory"]["critical"] . " ";
                            $linux_service_description = "Memory Usage";
                            break;

                        case "disk":
                            $linux_check_command .= "--disk --disk-warning " . $linux_serviceargs["disk"]["warning"] . " --disk-critical " . $linux_serviceargs["disk"]["critical"] . " ";
                            $linux_service_description = "Disk Usage";
                            break;
                    }

                    print "Linux Check Command for " . $linux_host . ": " . $linux_check_command . "<br>\n";

                    // Add the service check
                    $objs[] = array(
                        "type" => OBJECTTYPE_SERVICE,
                        "host_name" => $linux_host_name,
                        "service_description" => $linux_service_description,
                        "use" => "xiwizard_prometheus_linux_service",
                        "check_command" => $linux_check_command,
                        "check_interval" => 1,
                        "_xiwizard" => $wizard_name,
                    );
                }

                // Add services for custom metrics
                if (!empty($custom_linux_metrics)) {
                    foreach ($custom_linux_metrics as $metric) {
                        $linux_check_command = "check_prometheus_linux!-H " . $linux_host . " -P " . $port . " ";
                        $linux_check_command .= "--custom-metric '" . $metric['name'] . "' --custom-warning '" . $metric['warning'] . "' --custom-critical '" . $metric['critical'] . "' ";
                        
                        // Add the service check for custom metric
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $linux_host_name,
                            "service_description" => $metric['label'],
                            "use" => "xiwizard_prometheus_linux_service", 
                            "check_command" => $linux_check_command,
                            "check_interval" => 1,
                            "_xiwizard" => $wizard_name,
                        );
                    }
                }
            }
            

            // After creating objects
            print "Get Objects - Created objects: <pre>" . print_r($objs, true) . "</pre><br>\n";

            // return the object definitions to the wizard
            $outargs[CONFIGWIZARD_NAGIOS_OBJECTS] = $objs;

            break;

        default:
            break;
    }

    return $output;
}
