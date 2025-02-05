<?php
//
// Prometheus Config Wizard
// Copyright (c) 2025 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../configwizardhelper.inc.php');

prometheus_configwizard_init();

function prometheus_configwizard_init()
{
    $name = "prometheus";
    $args = array(
        CONFIGWIZARD_NAME => $name,
        CONFIGWIZARD_VERSION => "1.0.0",
        CONFIGWIZARD_TYPE => CONFIGWIZARD_TYPE_MONITORING,
        CONFIGWIZARD_DESCRIPTION => _("Monitor a Prometheus instance."),
        CONFIGWIZARD_DISPLAYTITLE => _("Prometheus"),
        CONFIGWIZARD_FUNCTION => "prometheus_configwizard_func",
        CONFIGWIZARD_PREVIEWIMAGE => "prometheus.png",
        CONFIGWIZARD_FILTER_GROUPS => array('linux', 'windows'),
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
function prometheus_configwizard_func($mode = "", $inargs = null, &$outargs = null, &$result = null)
{
    $wizard_name = "prometheus";

    // initialize return code and output
    $result = 0;
    $output = "";

    // Debug: Print current mode and input arguments
    print "Prometheus Wizard Mode: " . $mode . "<br>\n";

    // initialize output args - pass back the same data we got
    $outargs[CONFIGWIZARD_PASSBACK_DATA] = $inargs;

    switch ($mode) {
        case CONFIGWIZARD_MODE_GETSTAGE1HTML:
            $address = grab_array_var($inargs, "ip_address", "");

            ob_start();
            include __DIR__.'/steps/step1.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE1DATA:
            // Get variables that were passed to us
            $address = grab_array_var($inargs, "ip_address", "");
            $address = nagiosccm_replace_user_macros($address);
            $hostname = grab_array_var($inargs, "hostname", "");

            print "Stage 1 Validation - Processed address: " . $address . ", Hostname: " . $hostname . "<br>\n";

            // Check for errors
            $errors = 0;
            $errmsg = array();

            if (have_value($address) == false) {
                $errmsg[$errors++] = _("No address specified.");
            } else if (!valid_ip($address)) {
                $errmsg[$errors++] = _("Invalid IP address.");
            }

            if ($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE2HTML:            
            // Get variables that were passed to us
            $address = grab_array_var($inargs, "ip_address", "");
            $address = nagiosccm_replace_user_macros($address);
            $hostname = grab_array_var($inargs, "hostname", "");

            print "Stage 2 HTML - Address: " . $address . ", Hostname: " . $hostname . "<br>\n";

            ob_start();
            include __DIR__.'/steps/step2.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE2DATA:
            print "Stage 2 Validation - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";

            // Get variables that were passed to us
            $address = grab_array_var($inargs, "ip_address", "");
            $address = nagiosccm_replace_user_macros($address);
            $hostname = grab_array_var($inargs, "hostname", "");

            print "Stage 2 Validation - Address: " . $address . ", Hostname: " . $hostname . "<br>\n";

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
            $address = grab_array_var($inargs, "ip_address");
            $hostname = grab_array_var($inargs, "hostname");
            $services = grab_array_var($inargs, "services");
            $serviceargs = grab_array_var($inargs, "serviceargs");
            $linux_hosts = grab_array_var($inargs, "linux_hosts");
            $linux_services = grab_array_var($inargs, "linux_services");
            $linux_serviceargs = grab_array_var($inargs, "linux_serviceargs");
            $custom_linux_metrics = grab_array_var($inargs, "custom_linux_metrics");

            // Encode all data for passing through
            $services_serial = base64_encode(json_encode($services));
            $serviceargs_serial = base64_encode(json_encode($serviceargs));
            $linux_hosts_serial = base64_encode($linux_hosts);
            $linux_services_serial = base64_encode(json_encode($linux_services));
            $linux_serviceargs_serial = base64_encode(json_encode($linux_serviceargs));
            $custom_linux_metrics_serial = base64_encode(json_encode($custom_linux_metrics));

            print "Stage 3 HTML - Data being passed through:<br>\n";
            print "Services: <pre>" . print_r($services, true) . "</pre><br>\n";
            print "Service Args: <pre>" . print_r($serviceargs, true) . "</pre><br>\n";
            print "Linux Services: <pre>" . print_r($linux_services, true) . "</pre><br>\n";
            print "Linux Service Args: <pre>" . print_r($linux_serviceargs, true) . "</pre><br>\n";

            $output = '
                <input type="hidden" name="ip_address" value="' . encode_form_val($address) . '">
                <input type="hidden" name="hostname" value="' . encode_form_val($hostname) . '">
                <input type="hidden" name="services_serial" value="' . $services_serial . '">
                <input type="hidden" name="serviceargs_serial" value="' . $serviceargs_serial . '">
                <input type="hidden" name="linux_hosts_serial" value="' . $linux_hosts_serial . '">
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
            $hostname = grab_array_var($inargs, "hostname", "");
            $address = grab_array_var($inargs, "ip_address", "");
            $services_serial = grab_array_var($inargs, "services_serial", "");
            $serviceargs_serial = grab_array_var($inargs, "serviceargs_serial", "");
            $linux_hosts_serial = grab_array_var($inargs, "linux_hosts_serial", "");
            $linux_services_serial = grab_array_var($inargs, "linux_services_serial", "");
            $linux_serviceargs_serial = grab_array_var($inargs, "linux_serviceargs_serial", "");
            $custom_linux_metrics_serial = grab_array_var($inargs, "custom_linux_metrics_serial", "");

            // Decode all serialized data
            $services = json_decode(base64_decode($services_serial), true);
            $serviceargs = json_decode(base64_decode($serviceargs_serial), true);
            $linux_hosts = base64_decode($linux_hosts_serial);
            $linux_services = json_decode(base64_decode($linux_services_serial), true);
            $linux_serviceargs = json_decode(base64_decode($linux_serviceargs_serial), true);
            $custom_linux_metrics = json_decode(base64_decode($custom_linux_metrics_serial), true);

            // Debug output
            print "Get Objects - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";
            print "Hostname: " . $hostname . "<br>\n";
            print "Address: " . $address . "<br>\n";
            print "Services: <pre>" . print_r($services, true) . "</pre><br>\n";
            print "Service Args: <pre>" . print_r($serviceargs, true) . "</pre><br>\n";
            print "Linux Hosts: " . $linux_hosts . "<br>\n";
            print "Linux Services: <pre>" . print_r($linux_services, true) . "</pre><br>\n";
            print "Linux Service Args: <pre>" . print_r($linux_serviceargs, true) . "</pre><br>\n";
            print "Custom Linux Metrics: <pre>" . print_r($custom_linux_metrics, true) . "</pre><br>\n";

            // save data for later use in re-entrance
            $meta_arr = array();
            $meta_arr["hostname"] = $hostname;
            $meta_arr["ip_address"] = $address;
            $meta_arr["services"] = $services;
            $meta_arr["serviceargs"] = $serviceargs;
            $meta_arr["linux_hosts"] = $linux_hosts;
            $meta_arr["linux_services"] = $linux_services;
            $meta_arr["linux_serviceargs"] = $linux_serviceargs;
            $meta_arr["custom_linux_metrics"] = $custom_linux_metrics;
            save_configwizard_object_meta($wizard_name, $hostname, "", $meta_arr);

            $objs = array();

            // Add Prometheus server host
            if (!host_exists($address)) {
                $objs[] = array(
                    "type" => OBJECTTYPE_HOST,
                    "use" => "xiwizard_prometheus_host",
                    "host_name" => $hostname,
                    "address" => $address,
                    "icon_image" => "prometheus.png",
                    "statusmap_image" => "prometheus.png",
                    "_xiwizard" => $wizard_name,
                );
            }

            // Add Prometheus server services
            $prometheus_server_check_command = "check_prometheus!";
            foreach ($services as $svc => $svcstate) {
                if (empty($svcstate) || $svcstate !== "on") {
                    continue;
                }

                switch ($svc) {
                    case "ping":
                        $prometheus_server_check_command .= "--ping ";
                        break;
                    case "cpu":
                        $prometheus_server_check_command .= "--cpu --warning-cpu " . $serviceargs["cpu"]["warning"] . " --critical-cpu " . $serviceargs["cpu"]["critical"] . " ";
                        break;
                    case "memory":
                        $prometheus_server_check_command .= "--mem --warning-mem " . $serviceargs["memory"]["warning"] . " --critical-mem " . $serviceargs["memory"]["critical"] . " ";
                        break;
                    case "disk":
                        $prometheus_server_check_command .= "--disk --warning-disk " . $serviceargs["disk"]["warning"] . " --critical-disk " . $serviceargs["disk"]["critical"] . " ";
                        break;
                }
            }
            print "Prometheus Server Check Command: " . $prometheus_server_check_command . "<br>\n";

            // Add the Prometheus server service check
            $objs[] = array(
                "type" => OBJECTTYPE_SERVICE,
                "host_name" => $hostname,
                "service_description" => "Prometheus Server",
                "use" => "xiwizard_prometheus_service",
                "check_command" => $prometheus_server_check_command,
                "check_interval" => 1,
                "_xiwizard" => $wizard_name,
            );

            // Add Linux hosts and their services
            // if (!empty($linux_hosts)) {
            //     $linux_host_list = explode("\n", trim($linux_hosts));
            //     foreach ($linux_host_list as $linux_host) {
            //         $linux_host = trim($linux_host);
            //         if (empty($linux_host)) {
            //             continue;
            //         }

            //         $linux_hostname = "linux_" . preg_replace("/[^a-zA-Z0-9]/", "_", $linux_host);

            //         // Add Linux host
            //         if (!host_exists($linux_hostname)) {
            //             $objs[] = array(
            //                 "type" => OBJECTTYPE_HOST,
            //                 "use" => "xiwizard_prometheus_linux_host",
            //                 "host_name" => $linux_hostname,
            //                 "address" => $linux_host,
            //                 "icon_image" => "linux40.png",
            //                 "statusmap_image" => "linux40.png",
            //                 "_xiwizard" => $wizard_name,
            //             );
            //         }

            //         // Add Linux services
            //         foreach ($linux_services as $svc => $svcstate) {
            //             if (!$svcstate) {
            //                 continue;
            //             }

            //             switch ($svc) {
            //                 case "ping":
            //                     $objs[] = array(
            //                         "type" => OBJECTTYPE_SERVICE,
            //                         "host_name" => $linux_hostname,
            //                         "service_description" => "Ping",
            //                         "use" => "xiwizard_prometheus_ping_service",
            //                         "_xiwizard" => $wizard_name,
            //                     );
            //                     break;

            //                 case "cpu":
            //                     $objs[] = array(
            //                         "type" => OBJECTTYPE_SERVICE,
            //                         "host_name" => $linux_hostname,
            //                         "service_description" => "CPU Usage",
            //                         "use" => "xiwizard_prometheus_linux_cpu_service",
            //                         "check_command" => "check_prometheus_linux_metric!cpu!" . $linux_serviceargs["cpu"]["warning"] . "!" . $linux_serviceargs["cpu"]["critical"],
            //                         "_xiwizard" => $wizard_name,
            //                     );
            //                     break;

            //                 case "memory":
            //                     $objs[] = array(
            //                         "type" => OBJECTTYPE_SERVICE,
            //                         "host_name" => $linux_hostname,
            //                         "service_description" => "Memory Usage",
            //                         "use" => "xiwizard_prometheus_linux_memory_service",
            //                         "check_command" => "check_prometheus_linux_metric!memory!" . $linux_serviceargs["memory"]["warning"] . "!" . $linux_serviceargs["memory"]["critical"],
            //                         "_xiwizard" => $wizard_name,
            //                     );
            //                     break;

            //                 case "disk":
            //                     $objs[] = array(
            //                         "type" => OBJECTTYPE_SERVICE,
            //                         "host_name" => $linux_hostname,
            //                         "service_description" => "Disk Usage",
            //                         "use" => "xiwizard_prometheus_linux_disk_service",
            //                         "check_command" => "check_prometheus_linux_metric!disk!" . $linux_serviceargs["disk"]["warning"] . "!" . $linux_serviceargs["disk"]["critical"],
            //                         "_xiwizard" => $wizard_name,
            //                     );
            //                     break;
            //             }
            //         }

            //         // Add custom Linux metrics
            //         if (!empty($custom_linux_metrics)) {
            //             foreach ($custom_linux_metrics as $metric) {
            //                 $objs[] = array(
            //                     "type" => OBJECTTYPE_SERVICE,
            //                     "host_name" => $linux_hostname,
            //                     "service_description" => $metric['label'],
            //                     "use" => "xiwizard_prometheus_linux_custom_service",
            //                     "check_command" => "check_prometheus_linux_custom_metric!" . $metric['name'] . "!" . $metric['warning'] . "!" . $metric['critical'],
            //                     "_xiwizard" => $wizard_name,
            //                 );
            //             }
            //         }
            //     }
            // }

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
