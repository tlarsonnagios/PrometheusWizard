<?php
//
// Prometheus Config Wizard
// Copyright (c) 2025 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../configwizardhelper.inc.php');

prometheus_configwizard_init();

function prometheus_configwizard_init() {
    $name = "prometheus";
    $args = array(
        CONFIGWIZARD_NAME => $name,
        CONFIGWIZARD_VERSION => "1.0.0",
        CONFIGWIZARD_TYPE => CONFIGWIZARD_TYPE_MONITORING,
        CONFIGWIZARD_DESCRIPTION => _("Monitor system metrics via Prometheus."),
        CONFIGWIZARD_DISPLAYTITLE => _("Prometheus"),
        CONFIGWIZARD_FUNCTION => "prometheus_configwizard_func",
        CONFIGWIZARD_PREVIEWIMAGE => "prometheus.png",
        CONFIGWIZARD_COPYRIGHT => "Copyright &copy; 2025 Nagios Enterprises, LLC.",
        CONFIGWIZARD_AUTHOR => "Nagios Enterprises, LLC",
        CONFIGWIZARD_FILTER_GROUPS => array('network'),
        CONFIGWIZARD_REQUIRES_VERSION => 500
    );
    register_configwizard($name, $args);
}

function prometheus_configwizard_func($mode = "", $inargs = null, &$outargs = null, &$result = null) {
    $wizard_name = "prometheus";
    
    // Initialize return code and output
    $result = 0;
    $output = "";
    
    // Initialize output args - pass back the received data
    $outargs[CONFIGWIZARD_PASSBACK_DATA] = $inargs;
    
    switch ($mode) {
        case CONFIGWIZARD_MODE_GETSTAGE1HTML:
            $address = grab_array_var($inargs, "address", "");
            
            ob_start();
            include __DIR__.'/steps/step1.php';
            $output = ob_get_clean();
            
            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE1DATA:
            $address = grab_array_var($inargs, "address", "");
            $errors = 0;
            $errmsg = array();

            if (empty($address)) {
                $errmsg[$errors++] = _("No address specified.");
            } elseif (!filter_var($address, FILTER_VALIDATE_IP) && !filter_var($address, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $errmsg[$errors++] = _("Invalid address specified.");
            }

            if ($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE2HTML:
            // Get variables that were passed to us
            $address = grab_array_var($inargs, "address", "");
            $services = grab_array_var($inargs, "services", array());
            $serviceargs = grab_array_var($inargs, "serviceargs", array());

            ob_start();
            include __DIR__.'/steps/step2.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE2DATA:
            // Get variables that were passed to us
            $address = grab_array_var($inargs, "address", "");
            $address = nagiosccm_replace_user_macros($address);
            $services = grab_array_var($inargs, "services");
            $serviceargs = grab_array_var($inargs, "serviceargs");

            // Check for errors
            $errors = 0;
            $errmsg = array();

            if (empty($address)) {
                $errmsg[$errors++] = _("No address specified.");
            }

            if ($errors > 0) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errmsg;
                $result = 1;
            }

            break;
        
        case CONFIGWIZARD_MODE_GETSTAGE3HTML:
            // Get variables that were passed to us
            $address = grab_array_var($inargs, "address");
            $services = grab_array_var($inargs, "services");
            $serviceargs = grab_array_var($inargs, "serviceargs");

            $services_serial = grab_array_var($inargs, "services_serial", base64_encode(json_encode($services)));
            $serviceargs_serial = grab_array_var($inargs, "serviceargs_serial", base64_encode(json_encode($serviceargs)));

            $output = '
                <input type="hidden" name="address" value="' . encode_form_val($address) . '" />
                <input type="hidden" name="services_serial" value="' . $services_serial . '" />
                <input type="hidden" name="serviceargs_serial" value="' . $serviceargs_serial . '" />
            ';
            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE3DATA:
            break;

        case CONFIGWIZARD_MODE_GETFINALSTAGEHTML:
            $output = ' ';
            break;

        case CONFIGWIZARD_MODE_GETOBJECTS:
            $address = grab_array_var($inargs, "address", "");
            $services_serial = grab_array_var($inargs, "services_serial", "");
            $serviceargs_serial = grab_array_var($inargs, "serviceargs_serial", "");

            $services = json_decode(base64_decode($services_serial), true);
            $serviceargs = json_decode(base64_decode($serviceargs_serial), true);

            // Save data for later use in re-entrance
            $meta_arr = array();
            $meta_arr["address"] = $address;
            $meta_arr["services"] = $services;
            $meta_arr["serviceargs"] = $serviceargs;
            save_configwizard_object_meta($wizard_name, $address, "", $meta_arr);
            
            $objs = array();

            if (!host_exists($address)) {
                $objs[] = array(
                    "type" => OBJECTTYPE_HOST,
                    "use" => "xiwizard_prometheus_host",
                    "host_name" => $address,
                    "address" => $address,
                    "icon_image" => "prometheus.png",
                    "statusmap_image" => "prometheus.png",
                    "_xiwizard" => $wizard_name,
                );
            }
            
            // Add services based on user selection
            foreach ($services as $svc => $svcstate) {
                switch ($svc) {
                    case "cpu":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $address,
                            "service_description" => "CPU Usage",
                            "use" => "xiwizard_prometheus_service",
                            "check_command" => "check_prometheus!--cpu --warning-cpu " . $serviceargs["warning_cpu"] . " --critical-cpu " . $serviceargs["critical_cpu"],
                            "_xiwizard" => $wizard_name,
                        );
                        break;
                    case "mem":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $address,
                            "service_description" => "Memory Usage",
                            "use" => "xiwizard_prometheus_service",
                            "check_command" => "check_prometheus!--mem --warning-mem " . $serviceargs["warning_mem"] . " --critical-mem " . $serviceargs["critical_mem"],
                            "_xiwizard" => $wizard_name,
                        );
                        break;
                    case "disk":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $address,
                            "service_description" => "Disk Usage",
                            "use" => "xiwizard_prometheus_service",
                            "check_command" => "check_prometheus!--disk --warning-disk " . $serviceargs["warning_disk"] . " --critical-disk " . $serviceargs["critical_disk"],
                            "_xiwizard" => $wizard_name,
                        );
                        break;
                    case "load":
                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $address,
                            "service_description" => "Load Average",
                            "use" => "xiwizard_prometheus_service",
                            "check_command" => "check_prometheus!--load --warning-load " . $serviceargs["warning_load"] . " --critical-load " . $serviceargs["critical_load"],
                            "_xiwizard" => $wizard_name,
                        );
                        break;
                    default:
                        break;
                }
            }

            // Return the object definitions to the wizard
            $outargs[CONFIGWIZARD_NAGIOS_OBJECTS] = $objs;

            break;
        
        default:
            break;
    }

    return $output;
}
?>
