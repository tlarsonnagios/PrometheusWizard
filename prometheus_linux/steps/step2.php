    <!--                                   -->
    <!-- The initial data set from Step 1. -->
    <!--                                   -->
    <input type="hidden" name="linux_hosts" value="<?= encode_form_val($linux_hosts) ?>">
    <input type="hidden" name="port" value="<?= encode_form_val($port) ?>">

<?php
    // Use the hidden inputs directly
    $host = filter_var(trim(explode("\n", $linux_hosts)[0]), FILTER_UNSAFE_RAW); 
    $port = filter_var($port, FILTER_SANITIZE_NUMBER_INT);
    $url = "http://$host:$port/metrics";

    // Fetch the HTML content from the URL
    $response = file_get_contents($url);

    if ($response === FALSE) {
        $error = error_get_last();
        // echo "Error fetching data: " . $error['message'];
    } else {
        $metrics_array = array_filter(explode("\n", $response)); 

        $parsed_metrics = [];
        $categories = 0;
        $metrics = 0;
        $current_category = '';

        foreach ($metrics_array as $line) {
            $line_content = explode(' ', $line);

            // Check for HELP line and create a new category
            if (isset($line_content[1]) && $line_content[1] === 'HELP') {
                $categories += 1;
                $current_category = $line_content[2];
                $parsed_metrics[$current_category] = [
                    "tooltip" => implode(' ', array_slice($line_content, 3)),
                    "metric_type" => '',
                    "metrics" => []
                ];
                continue;
            }

            // Check for TYPE line and add the type to the current category
            if (isset($line_content[1]) && $line_content[1] === 'TYPE' && $current_category) {
                $parsed_metrics[$current_category]["metric_type"] = $line_content[3];
                continue;
            }

            // Check for actual metric lines
            if ($current_category) {
                $metrics += 1;
                $metric_name = $line_content[0];
                $metric_value = $line_content[1];
                $parsed_metrics[$current_category]["metrics"][$metric_name] = $metric_value;
            }
        }
    }
    echo json_encode($categories);
    echo "<br>";
    echo json_encode($metrics);

    echo '<pre>' . json_encode($parsed_metrics, JSON_PRETTY_PRINT) . '</pre>';

    // Function to escape special characters for HTML
    function escapeHtml($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
?>

    <div class="container m-0 g-0">
        <h2 class="mt-4"><?= _('Linux Services') ?></h2>
        <p><?= _('Specify which Linux metrics you would like to monitor') ?></p>
        
        <!-- Linux Metrics Select/Deselect All -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-left">
                    <div class="col-sm-2 d-flex align-items-center">
                        <div class="text-nowrap">
                            <a class="btn btn-link p-0" id="linuxMetricsCheckAll" title="<?= _('Check All Linux Metrics') ?>" onclick="selectAllInSection('linux_metrics', true)"><i class="fa fa-check-square-o" aria-hidden="true"></i></a>
                            <a class="btn btn-link p-0" id="linuxMetricsUncheckAll" title="<?= _('Uncheck All Linux Metrics') ?>" onclick="selectAllInSection('linux_metrics', false)"><i class="fa fa-square-o" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <!-- Linux Host CPU -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center linux_metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="linux_cpu" class="form-check-input me-2" name="linux_services[cpu]" <?= !isset($linux_services["cpu"]) || $linux_services["cpu"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('linux_metrics')">
                        <label for="linux_cpu" class="form-check-label bold me-2 text-nowrap"><?= _('CPU Usage') ?> <?= xi6_info_tooltip(_("Monitor CPU utilization of the Linux hosts")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[cpu][warning]" id="linux_cpu_warning" value="<?= encode_form_val($linux_serviceargs["cpu"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_cpu_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[cpu][critical]" id="linux_cpu_critical" value="<?= encode_form_val($linux_serviceargs["cpu"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_cpu_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <!-- Linux Host Memory -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center linux_metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="linux_memory" class="form-check-input me-2" name="linux_services[memory]" <?= !isset($linux_services["memory"]) || $linux_services["memory"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('linux_metrics')">
                        <label for="linux_memory" class="form-check-label bold me-2 text-nowrap"><?= _('Memory Usage') ?> <?= xi6_info_tooltip(_("Monitor memory utilization of the Linux hosts")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[memory][warning]" id="linux_memory_warning" value="<?= encode_form_val($linux_serviceargs["memory"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_memory_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[memory][critical]" id="linux_memory_critical" value="<?= encode_form_val($linux_serviceargs["memory"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_memory_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <!-- Linux Host Disk -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center linux_metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="linux_disk" class="form-check-input me-2" name="linux_services[disk]" <?= !isset($linux_services["disk"]) || $linux_services["disk"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('linux_metrics')">
                        <label for="linux_disk" class="form-check-label bold me-2 text-nowrap"><?= _('Disk Usage') ?> <?= xi6_info_tooltip(_("Monitor disk space utilization of the Linux hosts")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[disk][warning]" id="linux_disk_warning" value="<?= encode_form_val($linux_serviceargs["disk"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_disk_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="linux_serviceargs[disk][critical]" id="linux_disk_critical" value="<?= encode_form_val($linux_serviceargs["disk"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="linux_services_disk_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <!-- Custom Linux Metrics -->
        <h3 class="mt-4"><?= _('Custom Linux Metrics') ?></h3>
        <p><?= _('Add additional Prometheus metrics to monitor on your Linux hosts') ?></p>

        <!-- Search and Select Metrics -->
        <div class="row mb-2">
            <div class="col-sm-12">
                <label for="custom_linux_metric_search" class="form-label"><?= _('Search Metrics:') ?></label>
                <div class="input-group input-group-sm">
                    <div class="col-sm-9">
                        <input type="text" id="custom_linux_metric_search" class="form-control form-control-sm" placeholder="<?= _('Type to search...') ?>" onkeyup="filterMetrics()">
                        <select name="services-select[]" id="services-select" multiple="" class="form form-control metrics-select multiselect form-select">
                            <?php foreach ($parsed_metrics as $category => $metric): ?>
                                <?php $tooltip = $metric['tooltip']; ?>
                                <?php $type = $metric['metric_type']; ?>
                                <p> <?= escapeHtml($category) ?> </p>
                                <?php foreach ($metric['metrics'] as $name => $value): ?>
                                    <option value="<?= escapeHtml($name) ?>" onclick="addCustomMetric('<?= escapeHtml($name) ?>', '<?= escapeHtml($value) ?>', '<?= escapeHtml($tooltip) ?>', '<?= escapeHtml($type) ?>')" data-value="<?= escapeHtml($value) ?>" selected><?= escapeHtml($name) ?> | <?= escapeHtml($tooltip) ?> | Type: <?= escapeHtml($type) ?></option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>
            </div>
        </div>

        <h3 class="mt-4"><?= _('Selected Metrics') ?></h3>
        <div id="selected-metrics"></div>

    <script type="text/javascript">
        function filterMetrics() {
            // Get the input field and convert to lowercase for case-insensitive comparison
            const input = document.getElementById('custom_linux_metric_search').value.toLowerCase();
            const select = document.getElementById('services-select');
            const options = select.options;

            // Loop through all options in the select dropdown
            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].text.toLowerCase();

                // Check if the option text contains the input text
                if (optionText.includes(input)) {
                    options[i].style.display = '';  // Show the option
                } else {
                    options[i].style.display = 'none';  // Hide the option
                }
            }
        }



        function addCustomMetric(name, value, tooltip, type) {
            console.log("Adding Custom Metric: ", name, value);
            console.log(tooltip);
            const selectedMetricsDiv = document.getElementById('selected-metrics');
            const newMetricRow = document.createElement('div');
            newMetricRow.className = 'row mb-2 custom-linux-metric';
            const index = selectedMetricsDiv.getElementsByClassName('custom-linux-metric').length; 
            newMetricRow.innerHTML = `
                <div class="col-sm-2">
                    <label class="form-label"><?= _('Prometheus Metric Name:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[${index}][name]" class="form-control form-control-sm" value="${name.replace(/"/g, '&quot;')}" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label"><?= _('Service Name:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[${index}][label]" class="form-control form-control-sm" value="" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label"><?= _('Current Value:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[${index}][current_value]" class="form-control form-control-sm" value="${value.replace(/"/g, '&quot;')}" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label"><?= _('Warning:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[${index}][warning]" class="form-control form-control-sm" placeholder="Warning Threshold" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-label"><?= _('Critical:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-critical md-18 md-400">error</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[${index}][critical]" class="form-control form-control-sm" placeholder="Critical Threshold" required>
                    </div>
                </div>
                <div class="col-sm-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeCustomMetric(this)"><i class="fa fa-trash"></i> <?= _('Remove') ?></button>
                </div>
            `;
            selectedMetricsDiv.appendChild(newMetricRow);
        }

        function removeCustomMetric(button) {
            const row = button.closest('.custom-linux-metric');
            row.remove();
        }

        function selectAllInSection(sectionClass, select) {
            const checkboxes = document.querySelectorAll(`.${sectionClass} input[type="checkbox"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = select;
            });
        }

        function updateSelectAll(sectionClass) {
            const checkboxes = document.querySelectorAll(`.${sectionClass} input[type="checkbox"]`);
            const selectAllCheckbox = document.getElementById(`select_all_${sectionClass}`);
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            selectAllCheckbox.checked = allChecked;
        }
    </script>
