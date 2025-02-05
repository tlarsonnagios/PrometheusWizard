    <!--                                   -->
    <!-- The initial data set from Step 1. -->
    <!--                                   -->
    <input type="hidden" id="hostname" name="hostname" value="<?= (!empty($hostname)) ? encode_form_val($hostname) : "" ?>">
    <input type="hidden" id="operation" name="operation" value="<?= (!empty($operation)) ? encode_form_val($operation) : "" ?>">
    <input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= (!empty($selectedhostconfig)) ? encode_form_val($selectedhostconfig) : "" ?>">
    <input type="hidden" id="services_serial" name="services_serial" value="<?= (!empty($services)) ? base64_encode(json_encode($services)) : "" ?>" />
    <input type="hidden" id="serviceargs_serial" name="serviceargs_serial" value="<?= (!empty($serviceargs)) ? base64_encode(json_encode($serviceargs)) : "" ?>" />
    <input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" />

    <input type="hidden" id="ip_address" name="ip_address" value="<?= (!empty($address)) ? encode_form_val($address) : "" ?>">
    <input type="hidden" id="instance" name="instance" value="<?= (!empty($instance)) ? encode_form_val($instance) : "" ?>">
    <input type="hidden" id="port" name="port" value="<?= (!empty($port)) ? encode_form_val($port) : "" ?>">
    <input type="hidden" id="mssql_version" name="mssql_version" value="<?= (!empty($mssql_version)) ? encode_form_val($mssql_version) : "" ?>">
    <input type="hidden" id="tds_version" name="tds_version" value="<?= (!empty($tds_version)) ? encode_form_val($tds_version) : "" ?>">
    <input type="hidden" id="username" name="username" value="<?= (!empty($username)) ? encode_form_val($username) : "" ?>">
    <input type="hidden" id="password" name="password" value="<?= (!empty($password)) ? encode_form_val($password) : "" ?>">
    <input type="hidden" id="instancename" name="instancename" value="<?= (!empty($instancename)) ? encode_form_val($instancename) : "" ?>">
<?php
    #include_once __DIR__.'/../../../utils-xi2024-wizards.inc.php';
?>
    <div class="container m-0 g-0">

        <h2 class="mb-2"><?= _('MSSQL Server') ?></h2>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="hostname" class="form-label"><?= _('Host Name') ?> <?= xi6_info_tooltip(_('The name you would like to have associated with this MSSQL Database')) ?></label>
                <div class="input-group position-relative">
                    <input type="text" name="hostname" id="hostname" value="<?= (!empty($hostname)) ? encode_form_val($hostname) : "" ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Host Name") ?>" >
                    <i id="hostname_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="ip_address" class="form-label"><?= _('Address') ?> </label>
                <div class="input-group position-relative">
                    <input type="text" name="ip_address" id="ip_address" value="<?= (!empty($address)) ? encode_form_val($address) : "" ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Address") ?>" disabled="on">
                    <i id="ip_address_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label class="form-label"><?= _('Version') ?></label>
                <select name="mssql_version" id="mssql_version" class="form-control form-control-sm form-select form-select-sm" disabled>
                    <option value="PDW" <?= is_selected($mssql_version, "PDW") ?>><?= _('Parallel Data Warehouse') ?></option>
                    <option value="SQLDW" <?= is_selected($mssql_version, "SQLDW") ?>><?= _('Azure Synapse Analytics (SQL DW)') ?></option>
                    <option value="AZURESQLDB" <?= is_selected($mssql_version, "AZURESQLDB") ?>><?= _('Azure SQL DB') ?></option>
                    <option value="2022" <?= is_selected($mssql_version, "2022") ?>><?= _('2022 (Dallas)') ?></option>
                    <option value="2019" <?= is_selected($mssql_version, "2019") ?>><?= _('2019 (Seattle)') ?></option>
                    <option value="2017" <?= is_selected($mssql_version, "2017") ?>><?= _('2017 (Helsinki)') ?></option>
                    <option value="2016" <?= is_selected($mssql_version, "2016") ?>><?= _('2016 (SQL16)') ?></option>
                    <option value="2014" <?= is_selected($mssql_version, "2014") ?>><?= _('2014 (SQL14)') ?></option>
                    <option value="2012" <?= is_selected($mssql_version, "2012") ?>><?= _('2012 (Denali)') ?></option>
                    <option value="2008-R2" <?= is_selected($mssql_version, "2008-R2") ?>><?= _('2008 R2 (Kilimanjaro)') ?></option>
                    <option value="2008" <?= is_selected($mssql_version, "2008") ?>><?= _('2008 (Katmai)') ?></option>
                    <option value="2005" <?= is_selected($mssql_version, "2005") ?>><?= _('2005 (Yukon)') ?></option>
                    <option value="2000-64" <?= is_selected($mssql_version, "2000-64") ?>><?= _('2000 64-bit (Liberty)') ?></option>
                    <option value="2000" <?= is_selected($mssql_version, "2000") ?>><?= _('2000 (Shiloh)') ?></option>
                    <option value="other" <?= is_selected($mssql_version, "other") ?>><?= _('Other') ?></option>
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="tds_version" class="form-label"><?= _("TDS Version") ?></label>
                <select name="tds_version" id="tds_version" class="form-control form-control-sm form-select form-select-sm" placeholder="<?= _("Select version") ?> " disabled>
                    <option value="" selected>Choose a version...</option>
                    <option value="4.2" <?= is_selected($tds_version, "4.2") ?>>4.2</option>
                    <option value="5.0" <?= is_selected($tds_version, "5.0") ?>>5.0</option>
                    <option value="7.0" <?= is_selected($tds_version, "7.0") ?>>7.0</option>
                    <option value="7.1" <?= is_selected($tds_version, "7.1") ?>>7.1</option>
                    <option value="7.2" <?= is_selected($tds_version, "7.2") ?>>7.2</option>
                    <option value="7.3" <?= is_selected($tds_version, "7.3") ?>>7.3</option>
                    <option value="7.4" <?= is_selected($tds_version, "7.4") ?>>7.4</option>
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="instance" class="form-label"><?= _('Instance') ?> </label>
                <div class="input-group position-relative">
                    <input type="text" name="instance" id="instance" value="<?= (!empty($instance)) ? encode_form_val($instance) : "" ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Instance") ?>" disabled="on">
                    <i id="instance_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="port" class="form-label"><?= _('Port') ?> </label>
                <div class="input-group position-relative">
                    <input type="text" name="port" id="port" value="<?= (!empty($port)) ? encode_form_val($port) : "" ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Port") ?>" disabled="on">
                    <i id="port_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <label for="username" class="form-label"><?= _('Username') ?> </label>
                <div class="input-group position-relative">
                    <input type="text" name="username" id="username" value="<?= (!empty($username)) ? encode_form_val($username) : "" ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Username") ?>" disabled="on">
                    <i id="username_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                </div>
            </div>
        </div>

        <!--                         -->
        <!-- The metrics to monitor. -->
        <!--                         -->

<script type="text/javascript">
    $(document).ready(function() {
        // Set up undo buffers by "category" (thing m_select is managing)
        m_select_cats = ["services"];
        m_select_states = {};

        // Set up handlers and current state for multi-selects
        $.each(m_select_cats, function(i, category) {
            m_select_states[category] = [];

            $("#"+category+"-select").on("change", function($this) {
                manageMultiSelectForTable(category);
            });

            $("#"+category+"-undo").on("click", function($this) {
                multiSelectUndo(category);
            });

            // Set initial state for m selects
            manageMultiSelectForTable(category);
            $("#"+category+"-undo").prop( "disabled", true );
        });

        $(".multiselect").on("mouseenter", function($this) {
            $(this).trigger("focus");
        });

        $(".multi-select-remove").on("click", function($this) {
            // Figure out which was clicked
            fullName = $(this).attr("value");
            elem = "." + fullName;
            category = fullName.split('-')[0];
            name = fullName.replace(category+ '-', '')

            // Update multi select
            optn = $('#' + category + '-select option[value="'+ name +'"]');
            optn.prop('selected', false);
            // console.log("multi-select-remove: ", category, fullName, elem, name, optn)

            // Update table
            manageMultiSelectForTable(category);
        });

        function manageMultiSelectForTable(category) {
            container = "#"+category+"-list";
            select = "#"+category+"-select";
            selVal =  $(select).val();

            // Add new state to undo buffer
            m_select_states[category].push(selVal);
            $("#"+category+"-undo").prop( "disabled", false );
            // console.log("manageMultiSelectForTable: ", category, container, select, selVal)
            // console.log("manageMultiSelectForTable buffer: ", m_select_states[category])

            // Clear multi select and table
            $(container +" .service-item").prop( "checked", false );
            $(container +" .service-item").closest(".row").hide();

            // Apply updated settings
            $.each(selVal, function(i, val) {
                elem = "." + category + "-" + val;
                $(elem).prop( "checked", true );
                $(elem).closest(".row").show();
            });
        }

        function multiSelectUndo(category) {
            container = "#"+category+"-list";
            select = "#"+category+"-select";

            // Ignore current state
            m_select_states[category].pop();

            // Get previous state and apply
            selVal = m_select_states[category].pop();
            $(select).val(selVal);
            manageMultiSelectForTable(category);
            $(select).trigger("focus");

            // Disable undo button if appropriate
            if (m_select_states[category][0].length <= 0 ) {
                m_select_states[category] = [];
                $("#"+category+"-undo").prop( "disabled", true );
            }
        }
    });
</script>


    <h2 class="mt-4"><?= _('MSSQL Server Metrics') ?></h2>
        <p><?= _('Specify the metrics you would like to monitor on the MSSQL Server') ?>.</p>

        <div class="row">
            <div class="col-sm-3">
                <div class="multi-select-label"><?= _('Make your Metrics Selections')?><?= xi6_info_tooltip(_("Click with shift or CRTL/CMD key to adjust your selection.")) ?></div>
                    <?= $service_select_html ?>
                    <div class="select-undo-wrap">
                        <button type="button"  id="services-undo" class="select-undo"><i class="material-symbols-outlined md-button md-20 md-400">undo</i></button>
                    </div>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="multi-select-label"><?= _('Selected Metrics')?></div>
                    </div>
                </div>
                <div id="services-list" class="select-table-wrap">

            <?php

            foreach ($services_array as $name => $service) {
                $monitor = isset($services[$service]) && $services[$service] != '';
                $safe_name = $service;
                $service_tooltip = (isset($service_tooltips[$service])) ? _($service_tooltips[$service]) : '';

                ?>
                <div class="row">
                    <div class="col-sm-9">
                        <fieldset class="row g-2 mb-1 wz-fieldset">
                            <div class="form-check col-sm-4 mt-0 pt-1">
                                <input type="checkbox" id="services[<?= $service ?>]" class="form-check-input service-item services-<?= $safe_name ?>" name="services[<?= $service ?>]"  <?= is_checked($monitor, 'on') ?>>
                                <label for="services[<?= $service ?>]" class="form-check-label bold"><?= $name ?> <?= xi6_info_tooltip($service_tooltip) ?></label>
                            </div>
                            <div class="col-sm-3 mt-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Warning Threshold')) ?> class="material-symbols-outlined md-warning md-18 md-400">warning</i>
                                    </span>
                                    <input type="text" id="serviceargs[<?= $service ?>_warning]" name="serviceargs[<?= $service ?>_warning]" value="<?= (!empty($serviceargs)) ? encode_form_val($serviceargs[$service . '_warning']) : "" ?>" class="form-control form-control-sm monitor">
                                    <span class="input-group-text">/sec</span>
                                    <i id="serviceargs_<?= $service ?>_warning_Alert-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-3 mt-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i <?= xi6_title_tooltip(_('Critical Threshold')) ?> class="material-symbols-outlined md-critical md-18 md-400">error</i>
                                    </span>
                                    <input type="text" id="serviceargs[<?= $service ?>_critical]" name="serviceargs[<?= $service ?>_critical]" value="<?= (!empty($serviceargs)) ? encode_form_val($serviceargs[$service . '_critical']) : "" ?>" class="form-control form-control-sm monitor">
                                    <span class="input-group-text">/sec</span>
                                    <i id="serviceargs_<?= $service ?>_critical_Alert-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                                </div>
                            </div>
                            <div class="col-sm-1 mt-0">
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn-close multi-select-remove" value="services-<?= $safe_name ?>" aria-label="Remove"></button>
                                </div>
                        </fieldset>
                    </div>
                </div>


                <?php
                }
                ?>
                </div> <!-- services-list -->
            </div>
        </div>

        <!--                         -->
        <!-- Custom Metrics          -->
        <!--                         -->
        <h2 class="mt-4"><?= _('Custom Metrics') ?></h2>
        <p><?= _("Other metrics provided by the performance and Ring Buffer tables.") ?></p>

        <div id="custom-metrics-list">

            <div class="row g-1">
                <div class="col-sm-2 d-flex align-bottom">
                    <label for="serviceargs[process][0][counter_name]" class="form-label ps-4 mt-auto"><?= _('Counter Name') ?>&nbsp;<?= xi6_info_tooltip(_('The counter_name from the sys.sysperfinfo, sys.dm_os_performance_counters, etc. table or the field from the Ring table. ')) ?></label>
                </div>
                <div class="col-sm-2 d-flex align-bottom">
                    <label for="serviceargs[process][0][display_name]" class="form-label mt-auto"><?= _('Display Name') ?>&nbsp;<?= xi6_info_tooltip(_('A meaningful name for monitoring.  If left blank, it will be generated from the Counter and Instance Names.')) ?></label>
                </div>
                <div class="col-sm d-flex align-bottom">
                    <label for="serviceargs[process][0][instance_name]" class="form-label mt-auto"><?= _('Instance Name') ?>&nbsp;<?= xi6_info_tooltip(_('Name of the database instance.')) ?></label>
                </div>
                <div class="col-sm d-flex align-bottom">
                    <label for="serviceargs[process][0][unit]" class="form-label mt-auto"><?= _('Unit') ?>&nbsp;<?= xi6_info_tooltip(_('Optional label for unit of measure, e.g., s, ms, MB.')) ?></label>
                </div>
                <div class="col-sm d-flex align-bottom">
                    <label for="serviceargs[process][0][modifier]" class="form-label mt-auto"><?= _('Mod') ?>&nbsp;<?= xi6_info_tooltip(_('Optional multiplication modifier, e.g., 100.')) ?></label>
                </div>
                <div class="col-sm-2 d-flex align-bottom">
                    <label for="serviceargs[process][0][ring_buffer_type]" class="form-label mt-auto"><?= _('Ring Buffer Type') ?>&nbsp;<?= xi6_info_tooltip(_('Required for Ring Buffer queries.  The ring_buffer_type from the sys.dm_os_ring_buffers table.')) ?></label>
                </div>
                <div class="col-sm-2 d-flex align-bottom">
                    <label for="serviceargs[process][0][xpath]" class="form-label mt-auto"><?= _('XPath') ?>&nbsp;<?= xi6_info_tooltip(_('Required for Ring Buffer queries.  The xpath to the value you want to monitor.')) ?></label>
                </div>
                <div class="col-sm d-flex align-bottom">
                    <label for="serviceargs[process][0][warning]" class="form-label pad-t2 mt-auto"><?= _('Warning') ?>&nbsp;#</label>
                </div>
                <div class="col-sm d-flex align-bottom">
                    <label for="serviceargs[process][0][critical]" class="form-label pad-t2 mt-auto"><?= _('Critical') ?>&nbsp;#</label>
                </div>
            </div>

<?php
    foreach ($serviceargs['process'] as $i => $metrics) {
        $monitorCheck = (array_key_exists('process', $services) ? $services['process'][$i] : '');
?>
            <div class="row mb-2 g-1">
                <div class="col-sm-2">
                    <div class="input-group input-group-sm">
                        <div class="p-1 pe-2">
                            <input type="checkbox" class="form-check-input deselect" name="serviceargs[process][<?= $i ?>][monitor]" <?= is_checked($monitorCheck, 'on') ?>>
                        </div>
                        <input type="text" id="serviceargs[process][<?= $i ?>][counter_name]" name="serviceargs[process][<?= $i ?>][counter_name]" value="<?= (!empty($metrics['counter_name'])) ? encode_form_val($metrics['counter_name']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor" <?php if ($monitorCheck != 'on') echo(' onChange="nameEntered()"'); ?>>
                        <i id="serviceargs_process_<?= $i ?>_counter_name_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][display_name]" name="serviceargs[process][<?= $i ?>][display_name]" value="<?= (!empty($metrics['display_name'])) ? encode_form_val($metrics['display_name']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_display_name_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][instance_name]" name="serviceargs[process][<?= $i ?>][instance_name]" value="<?= (!empty($metrics['instance_name'])) ? encode_form_val($metrics['instance_name']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_instance_name_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][unit]" name="serviceargs[process][<?= $i ?>][unit]" value="<?= (!empty($metrics['unit'])) ? encode_form_val($metrics['unit']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_unit_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][modifier]" name="serviceargs[process][<?= $i ?>][modifier]" value="<?= (!empty($metrics['modifier'])) ? encode_form_val($metrics['modifier']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_modifier_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][ring_buffer_type]" name="serviceargs[process][<?= $i ?>][ring_buffer_type]" value="<?= (!empty($metrics['ring_buffer_type'])) ? encode_form_val($metrics['ring_buffer_type']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_ring_buffer_type_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][xpath]" name="serviceargs[process][<?= $i ?>][xpath]" value="<?= (!empty($metrics['xpath'])) ? encode_form_val($metrics['xpath']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_xpath_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][warning]" name="serviceargs[process][<?= $i ?>][warning]" value="<?= (!empty($metrics['warning'])) ? encode_form_val($metrics['warning']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_warning_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="input-group input-group-sm">
                        <input type="text" id="serviceargs[process][<?= $i ?>][critical]" name="serviceargs[process][<?= $i ?>][critical]" value="<?= (!empty($metrics['critical'])) ? encode_form_val($metrics['critical']) : "" ?>" class="form-control form-control-sm me-1 rounded-1 monitor">
                        <i id="serviceargs_process_<?= $i ?>_critical_Alert-cust-sm" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
            </div>
<?php
    }
?>
        </div> <!-- custom-metrics-list -->

        <p><a style="cursor: pointer;" id="add-new-metric" class="btn btn-link"><i class="fa fa-plus"></i> <?= _('Add Another Custom Metric') ?></a></p>

    </div> <!-- container -->

    <script type="text/javascript">
        /* TODO: Is this correct? */
        var metricnum = <?= (array_key_exists('process', $services) ? count($services['process']) - 1 : 0) ?>;

        function nameEntered() {
            $("input[name='services[process][0]']").prop("checked", true);
        }

        $(document).ready(function() {

            $("#add-new-metric").click(function() {
                metricnum++;

                row = "".concat(
    '<div class="row mb-2 g-1">',
    '    <div class="col-sm-2">',
    '       <div class="input-group input-group-sm">',
    '           <div class="p-1 pe-2"><input type="checkbox" class="form-check-input" name="serviceargs[process]['+metricnum+'][monitor]" checked></div>',
    '           <input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][counter_name]" value="">',
    '       </div>',
    '    </div>',
    '    <div class="col-sm-2"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][display_name]" value=""></div></div>',
    '    <div class="col-sm"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][instance_name]" value=""></div></div>',
    '    <div class="col-sm"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][unit]" value=""></div></div>',
    '    <div class="col-sm"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][modifier]" value=""></div></div>',
    '    <div class="col-sm-2"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][ring_buffer_type]" value=""></div></div>',
    '    <div class="col-sm-2"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][xpath]" value=""></div></div>',
    '    <div class="col-sm"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][warning]" value="60"></div></div>',
    '    <div class="col-sm"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm me-1 rounded-1" name="serviceargs[process]['+metricnum+'][critical]" value="100"></div></div>',
    '</div>');

                $("#custom-metrics-list").append(row);
            });
        });

        var hostData = '';
<?php
    if (isset($config)) {
?>
        hostData = JSON.parse('<?= json_encode($config) ?>');
<?php
    }
?>
    </script>

    <script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
