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

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />

</form>
