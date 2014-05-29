<fieldset id="fieldset-dublin-core-extended-form">
    <div class="field">
        <div class="two columns alpha">
            <label for="dublin_core_extended_refines"><?php echo __('Refines Items Search'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('dublin_core_extended_refines', true,
                array('checked' => (boolean) get_option('dublin_core_extended_refines'))); ?>
            <p class="explanation">
                <?php echo __('If selected, an advanced search on a element of the Dublin Core will be enlarged to its refinements, if any.'); ?>
            </p>
        </div>
    </div>
</fieldset>
