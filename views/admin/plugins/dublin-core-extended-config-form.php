<fieldset id="fieldset-dublin-core-extended-form"><legend><?php echo __('Search'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $view->formLabel('dublin_core_extended_refines',
                __('Refines Items Search')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $view->formCheckbox('dublin_core_extended_refines', true,
                array('checked' => (boolean) get_option('dublin_core_extended_refines'))); ?>
            <p class="explanation">
                <?php echo __('If selected, an advanced search on a element of the Dublin Core will be enlarged to its refinements, if any.'); ?>
            </p>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-dublin-core-extended-form"><legend><?php echo __('OAI-PMH Repository'); ?></legend>
    <p><?php
        if (plugin_is_active('OaiPmhRepository')):
            echo __('These options allow to select formats of metadata to expose via the the plugin %sOAI-PMH Repository%s.',
                '<a href="http://omeka.org/add-ons/plugins/oai-pmh-repository/">', '</a>');
        else:
            echo __('These options allow to define formats of metadata to expose when the plugin %sOAI-PMH Repository%s is installed.',
                '<a href="http://omeka.org/add-ons/plugins/oai-pmh-repository/">', '</a>');
        endif;
    ?></p>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $view->formLabel('dublin_core_extended_oaipmh_unrefined_dc',
                __('Unrefined Dublin Core')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $view->formCheckbox('dublin_core_extended_oaipmh_unrefined_dc', true,
                array('checked' => (boolean) get_option('dublin_core_extended_oaipmh_unrefined_dc'))); ?>
            <p class="explanation">
                <?php echo __('If checked, refined elements will be merged into the 15 default elements, so they will be harvestable by default.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('dublin_core_extended_oaipmh_oai_dcq',
                __('Qualified Dublin Core (oai_dcq)')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('dublin_core_extended_oaipmh_oai_dcq', true,
                array('checked' => (boolean) get_option('dublin_core_extended_oaipmh_oai_dcq'))); ?>
            <p class="explanation">
                <?php echo __('This format is defined by the British Library to expose qualified Dublin Core.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('dublin_core_extended_oaipmh_qdc',
                __('Qualified Dublin Core (qdc)')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('dublin_core_extended_oaipmh_qdc', true,
                array('checked' => (boolean) get_option('dublin_core_extended_oaipmh_qdc'))); ?>
            <p class="explanation">
                <?php echo __('This format has been replaced by the oai_dcq one, with a new namespace.'); ?>
            </p>
        </div>
    </div>
</fieldset>
