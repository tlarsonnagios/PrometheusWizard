    <!--                                   -->
    <!-- The initial data set from Step 1. -->
    <!--                                   -->
    <input type="hidden" id="hostname" name="hostname" value="<?= encode_form_val($hostname) ?>">
    <input type="hidden" id="operation" name="operation" value="<?= encode_form_val($operation) ?>">
    <input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= encode_form_val($selectedhostconfig) ?>">
    <input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" />
    <input type="hidden" name="ip_address" value="<?= encode_form_val($address) ?>">
    <input type="hidden" name="services_serial" value="<?= encode_form_val($services_serial) ?>">
    <input type="hidden" name="serviceargs_serial" value="<?= encode_form_val($serviceargs_serial) ?>">

<?php
   # include_once __DIR__.'/../../../utils-xi2024-wizards.inc.php';
?>
    <div class="container m-0 g-0">
        <h2 class="mt-4"><?= _('Prometheus Server Monitoring') ?></h2>
        <p><?= _('Specify which Prometheus server metrics and components you would like to monitor') ?></p>

        <!-- Prometheus Server Metrics Select/Deselect All -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-left">
                    <div class="col-sm-2 d-flex align-items-center">
                        <div class="text-nowrap">
                            <a class="btn btn-link p-0" id="serverMetricsCheckAll" title="<?= _('Check All Server Metrics') ?>" onclick="selectAllInSection('metrics', true)"><i class="fa fa-check-square-o" aria-hidden="true"></i></a>
                            <a class="btn btn-link p-0" id="serverMetricsUncheckAll" title="<?= _('Uncheck All Server Metrics') ?>" onclick="selectAllInSection('metrics', false)"><i class="fa fa-square-o" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Prometheus Server CPU -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="cpu" class="form-check-input me-2" name="services[cpu]" <?= !isset($services["cpu"]) || $services["cpu"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('metrics')">
                        <label for="cpu" class="form-check-label bold me-2 text-nowrap"><?= _('CPU Usage') ?> <?= xi6_info_tooltip(_("Monitor CPU utilization of the Prometheus server")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="serviceargs[cpu][warning]" id="cpu_warning" value="<?= encode_form_val($serviceargs["cpu"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_cpu_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="serviceargs[cpu][critical]" id="cpu_critical" value="<?= encode_form_val($serviceargs["cpu"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_cpu_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Prometheus Server Memory -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="memory" class="form-check-input me-2" name="services[memory]" <?= !isset($services["memory"]) || $services["memory"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('metrics')">
                        <label for="memory" class="form-check-label bold me-2 text-nowrap"><?= _('Memory Usage') ?> <?= xi6_info_tooltip(_("Monitor memory utilization of the Prometheus server")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="serviceargs[memory][warning]" id="memory_warning" value="<?= encode_form_val($serviceargs["memory"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_memory_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="serviceargs[memory][critical]" id="memory_critical" value="<?= encode_form_val($serviceargs["memory"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_memory_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Prometheus Server Disk -->
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="row g-2 mb-1 wz-fieldset align-items-center metrics">
                    <div class="form-check col-sm-2 d-flex align-items-center">
                        <input type="checkbox" id="disk" class="form-check-input me-2" name="services[disk]" <?= !isset($services["disk"]) || $services["disk"] ? 'checked="checked"' : '' ?> onchange="updateSelectAll('metrics')">
                        <label for="disk" class="form-check-label bold me-2 text-nowrap"><?= _('Disk Usage') ?> <?= xi6_info_tooltip(_("Monitor disk space utilization of the Prometheus server")) ?></label>
                    </div>
                    <div class="col-sm-6 offset-sm-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold % (default=80)')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" name="serviceargs[disk][warning]" id="disk_warning" value="<?= encode_form_val($serviceargs["disk"]["warning"] ?? 80) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_disk_warning_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold % (default=90)')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" name="serviceargs[disk][critical]" id="disk_critical" value="<?= encode_form_val($serviceargs["disk"]["critical"] ?? 90) ?>" class="form-control form-control-sm">
                                    <span class="input-group-text">%</span>
                                    <i id="services_disk_critical_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
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
    </script>
    