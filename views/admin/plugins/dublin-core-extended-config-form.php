<fieldset id="fieldset-dublin-core-extended-form">
    <div class="field">
        <div class="two columns alpha">
            <?php echo $view->formLabel('dublin_core_extended_oaipmh_unrefined_dc',
                __('Unrefined DC for OAI-PMH')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $view->formCheckbox('dublin_core_extended_oaipmh_unrefined_dc', true,
                array('checked' => (boolean) get_option('dublin_core_extended_oaipmh_unrefined_dc'))); ?>
            <p class="explanation">
                <?php echo __('If checked, refined elements will be merged into the 15 default elements, so they will be harvestable.'); ?>
                <?php echo __('In any case, detailled qualified Dublin Core elements is available via the "qdc" metadata format.'); ?>
                <?php if (!plugin_is_active('OaiPmhRepository')): ?>
            </p>
            <p class="explanation">
                <?php echo __('This option applies only when the plugin %s is enabled.', '<a href="http://omeka.org/add-ons/plugins/oai-pmh-repository">OAI-PMH Repository</a>'); ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</fieldset>
