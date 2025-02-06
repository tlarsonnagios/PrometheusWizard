    <!--                                   -->
    <!-- The initial data set from Step 1. -->
    <!--                                   -->
    
    <!-- <input type="hidden" id="operation" name="operation" value="<?= encode_form_val($operation) ?>">
    <input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= encode_form_val($selectedhostconfig) ?>">
    <input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" /> -->

    <input type="hidden" name="linux_hosts" value="<?= encode_form_val($linux_hosts) ?>">

<?php
   # include_once __DIR__.'/../../../utils-xi2024-wizards.inc.php';
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
        <div id="custom-linux-metrics">
<?php
    if (!empty($custom_linux_metrics)) {
        foreach ($custom_linux_metrics as $index => $metric) {
?>
            <div class="row mb-2 custom-linux-metric">
                <div class="col-sm-3">
                    <label for="custom_linux_metric_name_<?= $index ?>" class="form-label"><?= _('Linux Metric Name:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[<?= $index ?>][name]" id="custom_linux_metric_name_<?= $index ?>" class="form-control form-control-sm" value="<?= encode_form_val($metric['name']) ?>" placeholder="<?= _('node_memory_MemTotal_bytes') ?>" required>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="custom_linux_metric_label_<?= $index ?>" class="form-label"><?= _('Service Label:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[<?= $index ?>][label]" id="custom_linux_metric_label_<?= $index ?>" class="form-control form-control-sm" value="<?= encode_form_val($metric['label']) ?>" placeholder="<?= _('Memory Total') ?>" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="custom_linux_metric_warning_<?= $index ?>" class="form-label"><?= _('Warning:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[<?= $index ?>][warning]" id="custom_linux_metric_warning_<?= $index ?>" class="form-control form-control-sm" value="<?= encode_form_val($metric['warning']) ?>" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="custom_linux_metric_critical_<?= $index ?>" class="form-label"><?= _('Critical:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-critical md-18 md-400">error</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[<?= $index ?>][critical]" id="custom_linux_metric_critical_<?= $index ?>" class="form-control form-control-sm" value="<?= encode_form_val($metric['critical']) ?>" required>
                    </div>
                </div>
                <div class="col-sm-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm mb-2" onclick="removeCustomLinuxMetric(<?= $index ?>)"><i class="fa fa-trash"></i> <?= _('Remove') ?></button>
                </div>
            </div>
<?php
        }
    }
?>
        </div>
        <button type="button" class="btn btn-primary mb-4" onclick="addCustomLinuxMetric()"><i class="fa fa-plus"></i> <?= _('Add Custom Linux Metric') ?></button>

    </div> <!-- container -->

    <script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
    <script type="text/javascript">
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

        let customLinuxMetricCount = <?= !empty($custom_linux_metrics) ? max(array_keys($custom_linux_metrics)) + 1 : 0 ?>;

        function addCustomLinuxMetric() {
            const customMetrics = document.getElementById('custom-linux-metrics');
            const newMetric = document.createElement('div');
            newMetric.className = 'row mb-2 custom-linux-metric';
            newMetric.innerHTML = `
                <div class="col-sm-3">
                    <label for="custom_linux_metric_name_${customLinuxMetricCount}" class="form-label"><?= _('Linux Metric Name:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[${customLinuxMetricCount}][name]" id="custom_linux_metric_name_${customLinuxMetricCount}" class="form-control form-control-sm" placeholder="<?= _('node_memory_MemTotal_bytes') ?>" required>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="custom_linux_metric_label_${customLinuxMetricCount}" class="form-label"><?= _('Service Label:') ?></label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="custom_linux_metrics[${customLinuxMetricCount}][label]" id="custom_linux_metric_label_${customLinuxMetricCount}" class="form-control form-control-sm" placeholder="<?= _('Memory Total') ?>" required>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="custom_linux_metric_warning_${customLinuxMetricCount}" class="form-label"><?= _('Warning:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[${customLinuxMetricCount}][warning]" id="custom_linux_metric_warning_${customLinuxMetricCount}" class="form-control form-control-sm" value="80" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="custom_linux_metric_critical_${customLinuxMetricCount}" class="form-label"><?= _('Critical:') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="material-symbols-outlined md-critical md-18 md-400">error</i>
                        </span>
                        <input type="text" name="custom_linux_metrics[${customLinuxMetricCount}][critical]" id="custom_linux_metric_critical_${customLinuxMetricCount}" class="form-control form-control-sm" value="90" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-sm-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm mb-2" onclick="removeCustomLinuxMetric(${customLinuxMetricCount})"><i class="fa fa-trash"></i> <?= _('Remove') ?></button>
                </div>
            `;
            customMetrics.appendChild(newMetric);
            customLinuxMetricCount++;
        }

        function removeCustomLinuxMetric(id) {
            const metric = document.getElementById(`custom_linux_metric_name_${id}`).closest('.custom-linux-metric');
            metric.remove();
        }
    </script>
