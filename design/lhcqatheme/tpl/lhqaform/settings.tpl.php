<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'QA Forms Settings'); ?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php')); ?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers', 'Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php')); ?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'Choose a form to use for QA'); ?></label>
        <select name="form_id" class="form-control form-control-sm">
            <option value="0">---</option>
            <?php foreach ($forms as $formItem) : ?>
                <option value="<?php echo $formItem->id; ?>" <?php (isset($qa_options['form_id']) && $qa_options['form_id'] == $formItem->id) ? print 'selected="selected"' : '' ?>><?php echo htmlspecialchars($formItem->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'Per-department surveys'); ?></label>
        <small class="text-muted d-block mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'Assign a survey to specific departments. Departments without an assignment use the default survey above.'); ?></small>
        <?php foreach ($forms as $formItem) : ?>
            <div class="row mb-2 align-items-center">
                <div class="col-md-4">
                    <div class="form-control form-control-sm bg-light"><?php echo htmlspecialchars($formItem->name); ?></div>
                </div>
                <div class="col-md-8">
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'dep_ids_' . (int)$formItem->id . '[]',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'Choose departments'),
                        'selected_id'    => isset($qa_options['survey'][$formItem->id]) ? $qa_options['survey'][$formItem->id] : array(),
                        'ajax'           => 'deps',
                        'css_class'      => 'form-control',
                        'display_name'   => 'name',
                        'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 50], erLhcoreClassUserDep::conditionalDepartmentFilter()),
                        'list_function'  => 'erLhcoreClassModelDepartament::getList'
                    )); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'Highlight options (JSON)'); ?></label>
        <textarea name="highlight_options" class="form-control form-control-sm" rows="12"><?php echo htmlspecialchars(json_encode(isset($qa_options['highlight_options']) ? $qa_options['highlight_options'] : new stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
        <small class="text-muted">Example:</small>
        <pre class="bg-light p-2 mt-1 rounded border" style="font-size:12px;">{
    "qa_spelling_remarks": {
        "add": "Grammar mistake",
        "update": "Update Grammar mistake",
        "remove": "Remove Grammar mistake"
    }
}</pre>
        <small class="text-muted">Sample form field:</small>
        <pre class="bg-light p-2 mt-1 rounded border" style="font-size:12px;">[[json_content{"type":"textarea","log_changes":true,"name":"qa_spelling_remarks","rows":"1"}]]</pre>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />

</form>

<script>
$(function() {
    $('.btn-block-department').makeDropdown();
});
</script>
