    <!-- <input type="hidden" id="selectedhostconfig" name="selectedhostconfig" value="<?= encode_form_val($selectedhostconfig) ?>" />
    <input type="hidden" id="config_serial" name="config_serial" value="<?= (!empty($config)) ? base64_encode(json_encode($config)) : "" ?>" /> -->

<div class="container m-0 g-0">
    <!--                         -->
    <!-- The configuration form. -->
    <!--                         -->
    <div id="configForm">
        <h2 class="mb-2"><?= _('Prometheus Linux Hosts') ?></h2>

            <!-- Linux Host Addresses -->
        <div class="row mb-4">
            <div class="col-sm pe-0">
                <label for="linux_hosts" class="form-label"><?= _('Linux Host Addresses') ?><?= xi6_info_tooltip(_('The IP addresses or hostnames of Linux hosts to monitor. One entry per line. Each host will be monitored using the selected metrics above.')) ?></label>
                <textarea name="linux_hosts" id="linux_hosts" style="font-family: Consolas, Courier New, monospace;" class="form-control form-control-sm" placeholder="<?= _('192.168.1.100&#13;&#10;server1.example.com&#13;&#10;10.0.0.50') ?>" rows="10"><?= encode_form_val($linux_hosts ?? '') ?></textarea>
            </div>
        </div>


    </div> <!-- config -->
</div> <!-- container -->

<script type="text/javascript" src="<?= get_base_url() ?>includes/js/wizards-bs5.js?<?= get_build_id(); ?>"></script>
