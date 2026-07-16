<?php $qaFormId = erLhcoreClassModelChatConfig::fetch('lhcqa_options')->data['form_id']; ?>
<?php if ($qaFormId > 0) : ?>
<th>QA (score)</th>
<th>QA (chats)</th>
<?php endif; ?>