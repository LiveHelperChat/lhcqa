<?php $qaFormId = erLhcoreClassModelChatConfig::fetch('lhcqa_options')->data['form_id']; ?>
<?php if ($qaFormId > 0) : ?>
<?php if (!isset($qaCanFillPrivate)) : ?><?php
$qaCanFillPrivate = erLhcoreClassUser::instance()->hasAccessTo('lhform', 'fill_private');
$qaCanSeeDetailed = erLhcoreClassUser::instance()->hasAccessTo('lhqaform', 'see_detailed');
$qaCanFill = erLhcoreClassUser::instance()->hasAccessTo('lhqaform', 'fill');
?><?php endif; ?>
<?php if ($qaCanFill || $qaCanSeeDetailed) : ?>
<th>QA</th>
<?php endif; ?>
<?php endif; ?>