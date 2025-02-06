    <div class="container m-0 g-0">
        <!--                         -->
        <!-- The configuration form. -->
        <!--                         -->
        <div id="configForm">
            <h2 class="mb-2"><?= _('Prometheus Linux Hosts') ?></h2>

                <!-- Linux Host Addresses -->
            <div class="row mb-4">
                <div class="col-sm pe-0">
                    <label for="linux_hosts" class="form-label"><?= _('Linux Host Addresses') ?><?= xi6_info_tooltip(_('The IP addresses or hostnames of Linux hosts to monitor. One entry per line.')) ?></label>
                    <textarea name="linux_hosts" id="linux_hosts" style="font-family: Consolas, Courier New, monospace;" class="form-control form-control-sm" placeholder="<?= _('192.168.1.100&#13;&#10;server1.example.com&#13;&#10;10.0.0.50') ?>" rows="10"><?= encode_form_val($linux_hosts ?? '') ?></textarea>
                </div>
            </div>

            <!-- Linux Host Port -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <label for="port" class="form-label form-item-required"><?= _('Linux Host Port:') ?> <?= xi6_info_tooltip(_('The port number of the Linux host. (default=9100)')) ?></label>
                    <div class="input-group position-relative">
                        <input type="text" name="port" id="port" value="<?= encode_form_val(9100) ?>" class="form-control monitor rounded" placeholder="<?= _("Linux Host Port:") ?>" required>
                        <div class="invalid-feedback">
                            <?= _("Please enter the Linux Host Port") ?>
                        </div>
                        <i id="port_Alert" class="visually-hidden position-absolute top-0 start-100 translate-middle icon icon-circle color-ok icon-size-status"></i>
                    </div>
                </div>
            </div>

        </div> <!-- config -->
    </div> <!-- container -->

    <script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
