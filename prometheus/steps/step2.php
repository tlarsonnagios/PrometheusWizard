<!--                                   -->
<!-- The initial data set from Step 1. -->
<!--                                   -->
<input type="hidden" id="hostname" name="hostname" value="<?= encode_form_val($hostname) ?>">
<input type="hidden" id="operation" name="operation" value="<?= encode_form_val($operation) ?>">
<input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= encode_form_val($selectedhostconfig) ?>">
<input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" />
<input type="hidden" id="services_serial" name="services_serial" value="<?= (!empty($services)) ? base64_encode(json_encode($services)) : "" ?>" />
<input type="hidden" id="serviceargs_serial" name="serviceargs_serial" value="<?= (!empty($serviceargs)) ? base64_encode(json_encode($serviceargs)) : "" ?>" />

<input type="hidden" name="ip_address" value="<?= encode_form_val($address) ?>">

<div class="container m-0 g-0">
    <h2 class="mb-2"><?= _('Prometheus Services') ?></h2>
    <p><?= _('Specify which services you would like to monitor for the Prometheus server') ?></p>

    <div class="row">
        <div class="col-sm-8">
            <fieldset class="row g-2 mb-1 wz-fieldset">
                <div class="form-check col-sm-3 mt-0 pt-1">
                    <input type="checkbox" id="cpu" class="form-check-input" name="services[cpu]" checked="on">
                    <label for="cpu" class="form-check-label bold"><?= _('CPU Usage') ?></label>
                </div>
                <div class="col-sm-9 mt-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><?= _('Warning') ?></span>
                        <input type="text" id="warning_cpu" name="serviceargs[warning_cpu]" value="<?= encode_form_val($warning_cpu) ?>" class="form-control form-control-sm rounded monitor">
                        <span class="input-group-text"><?= _('Critical') ?></span>
                        <input type="text" id="critical_cpu" name="serviceargs[critical_cpu]" value="<?= encode_form_val($critical_cpu) ?>" class="form-control form-control-sm rounded monitor">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            <fieldset class="row g-2 mb-1 wz-fieldset">
                <div class="form-check col-sm-3 mt-0 pt-1">
                    <input type="checkbox" id="mem" class="form-check-input" name="services[mem]" checked="on">
                    <label for="mem" class="form-check-label bold"><?= _('Memory Usage') ?></label>
                </div>
                <div class="col-sm-9 mt-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><?= _('Warning') ?></span>
                        <input type="text" id="warning_mem" name="serviceargs[warning_mem]" value="<?= encode_form_val($warning_mem) ?>" class="form-control form-control-sm rounded monitor">
                        <span class="input-group-text"><?= _('Critical') ?></span>
                        <input type="text" id="critical_mem" name="serviceargs[critical_mem]" value="<?= encode_form_val($critical_mem) ?>" class="form-control form-control-sm rounded monitor">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            <fieldset class="row g-2 mb-1 wz-fieldset">
                <div class="form-check col-sm-3 mt-0 pt-1">
                    <input type="checkbox" id="disk" class="form-check-input" name="services[disk]" checked="on">
                    <label for="disk" class="form-check-label bold"><?= _('Disk Usage') ?></label>
                </div>
                <div class="col-sm-9 mt-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><?= _('Warning') ?></span>
                        <input type="text" id="warning_disk" name="serviceargs[warning_disk]" value="<?= encode_form_val($warning_disk) ?>" class="form-control form-control-sm rounded monitor">
                        <span class="input-group-text"><?= _('Critical') ?></span>
                        <input type="text" id="critical_disk" name="serviceargs[critical_disk]" value="<?= encode_form_val($critical_disk) ?>" class="form-control form-control-sm rounded monitor">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            <fieldset class="row g-2 mb-1 wz-fieldset">
                <div class="form-check col-sm-3 mt-0 pt-1">
                    <input type="checkbox" id="load" class="form-check-input" name="services[load]" checked="on">
                    <label for="load" class="form-check-label bold"><?= _('Load Average') ?></label>
                </div>
                <div class="col-sm-9 mt-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><?= _('Warning') ?></span>
                        <input type="text" id="warning_load" name="serviceargs[warning_load]" value="<?= encode_form_val($warning_load) ?>" class="form-control form-control-sm rounded monitor">
                        <span class="input-group-text"><?= _('Critical') ?></span>
                        <input type="text" id="critical_load" name="serviceargs[critical_load]" value="<?= encode_form_val($critical_load) ?>" class="form-control form-control-sm rounded monitor">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</div> <!-- container -->

<script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
