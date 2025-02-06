    <input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= encode_form_val($selectedhostconfig) ?>" />
    <input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" />

    <div class="container m-0 g-0">
<?php
    #include_once __DIR__.'/../../../utils-xi2024-wizards.inc.php';
?>
        <!--                         -->
        <!-- The configuration form. -->
        <!--                         -->
        <div id="configForm">
            <h2 class="mb-2"><?= _('Prometheus Server Information') ?></h2>

            <!-- Prometheus Server Address -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="ip_address" class="form-label form-item-required"><?= _('Prometheus Server Address:') ?> <?= xi6_info_tooltip(_('The IP address of the Prometheus instance you would like to monitor')) ?></label>
                    <div class="input-group position-relative">
                        <input type="text" name="ip_address" id="ip_address" value="<?= encode_form_val($address) ?>" class="form-control monitor rounded" placeholder="<?= _("Enter Prometheus Address:") ?>" required>
                        <div class="invalid-feedback">
                            Please enter the Prometheus Address:
                        </div>
                        <i id="ip_address_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
            </div>

            <!-- Prometheus Server Port -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="port" class="form-label form-item-required"><?= _('Prometheus Server Port:') ?> <?= xi6_info_tooltip(_('The port number of the Prometheus instance (default=9090)')) ?></label>
                    <div class="input-group position-relative">
                        <input type="text" name="port" id="port" value="<?= encode_form_val($port ?? 9090) ?>" class="form-control monitor rounded" placeholder="<?= _("Enter Prometheus Port:") ?>" required>
                        <div class="invalid-feedback">
                            <?= _("Please enter the Prometheus Port") ?>
                        </div>
                        <i id="port_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
            </div>

            <!-- Host Name -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="hostname" class="form-label form-item-required"><?= _('Host Name:') ?> <?= xi6_info_tooltip(_('Name you would like to associate with this Prometheus server')) ?></label>
                    <div class="input-group position-relative">
                        <input type="text" name="hostname" id="hostname" value="<?= encode_form_val($hostname ?? "Prometheus Server") ?>" class="form-control form-control-sm monitor rounded" placeholder="<?= _("Enter Host Name:") ?>" >
                        <div class="invalid-feedback">
                            <?= _("Please enter the Host Name") ?>
                        </div>
                        <i id="hostname_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
            </div>

        </div> <!-- config -->
    </div> <!-- container -->

    <script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
