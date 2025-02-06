<?php
//
// Prometheus Server Config Wizard
// Copyright (c) 2025 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../configwizardhelper.inc.php');

prometheus_server_configwizard_init();

function prometheus_server_configwizard_init()
{
    $name = "prometheus_server";
    $args = array(
        CONFIGWIZARD_NAME => $name,
        CONFIGWIZARD_VERSION => "1.0.0",
        CONFIGWIZARD_TYPE => CONFIGWIZARD_TYPE_MONITORING,
        CONFIGWIZARD_DESCRIPTION => _("Monitor a Prometheus server with Prometheus Node Exporter."),
        CONFIGWIZARD_DISPLAYTITLE => _("Prometheus Server"),
        CONFIGWIZARD_FUNCTION => "prometheus_server_configwizard_func",
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
function prometheus_server_configwizard_func($mode = "", $inargs = null, &$outargs = null, &$result = null)
{
    $wizard_name = "prometheus_server";

    // initialize return code and output
    $result = 0;
    $output = "";

    // Debug: Print current mode and input arguments
    print "Prometheus Server Wizard Mode: " . $mode . "<br>\n";

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

            // Encode all data for passing through
            $services_serial = base64_encode(json_encode($services));
            $serviceargs_serial = base64_encode(json_encode($serviceargs));

            print "Stage 3 HTML - Data being passed through:<br>\n";
            print "Services: <pre>" . print_r($services, true) . "</pre><br>\n";
            print "Service Args: <pre>" . print_r($serviceargs, true) . "</pre><br>\n";

            $output = '
                <input type="hidden" name="ip_address" value="' . encode_form_val($address) . '">
                <input type="hidden" name="hostname" value="' . encode_form_val($hostname) . '">
                <input type="hidden" name="services_serial" value="' . $services_serial . '">
                <input type="hidden" name="serviceargs_serial" value="' . $serviceargs_serial . '">
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

            // Decode all serialized data
            $services = json_decode(base64_decode($services_serial), true);
            $serviceargs = json_decode(base64_decode($serviceargs_serial), true);

            // Debug output
            print "Get Objects - Input data: <pre>" . print_r($inargs, true) . "</pre><br>\n";
            print "Hostname: " . $hostname . "<br>\n";
            print "Address: " . $address . "<br>\n";
            print "Services: <pre>" . print_r($services, true) . "</pre><br>\n";
            print "Service Args: <pre>" . print_r($serviceargs, true) . "</pre><br>\n";

            // save data for later use in re-entrance
            $meta_arr = array();
            $meta_arr["hostname"] = $hostname;
            $meta_arr["ip_address"] = $address;
            $meta_arr["services"] = $services;
            $meta_arr["serviceargs"] = $serviceargs;
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
            $prometheus_server_check_command = "check_prometheus_server!--prometheus-host " . $address . " ";
            foreach ($services as $svc => $svcstate) {
                if (empty($svcstate) || $svcstate !== "on") {
                    continue;
                }

                switch ($svc) {
                    case "cpu":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Prometheus Server CPU",
                            "use" => "xiwizard_prometheus_server_service",
                            "check_command" => $prometheus_server_check_command . "--cpu --warning-cpu " . $serviceargs["cpu"]["warning"] . " --critical-cpu " . $serviceargs["cpu"]["critical"],
                            "check_interval" => 1,
                            "_xiwizard" => $wizard_name,
                        );
                        break; 
                    case "memory":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Prometheus Server Memory",
                            "use" => "xiwizard_prometheus_server_service",
                            "check_command" => $prometheus_server_check_command . "--mem --warning-mem " . $serviceargs["memory"]["warning"] . " --critical-mem " . $serviceargs["memory"]["critical"],
                            "check_interval" => 1,
                            "_xiwizard" => $wizard_name,
                        );
                        break;
                    case "disk":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Prometheus Server Storage Usage",
                            "use" => "xiwizard_prometheus_server_service",
                            "check_command" => $prometheus_server_check_command . "--disk --warning-disk " . $serviceargs["disk"]["warning"] . " --critical-disk " . $serviceargs["disk"]["critical"],
                            "check_interval" => 1,
                            "_xiwizard" => $wizard_name,
                        );
                        break;
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
