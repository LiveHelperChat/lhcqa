<?php

$chatId = isset($Params['user_parameters']['chat_id']) ? (int)$Params['user_parameters']['chat_id'] : 0;
$chat = $chatId > 0 ? erLhcoreClassModelChat::fetch($chatId) : false;

if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat)) {
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhqaform/seedetails.tpl.php');
$tpl->set('chat', $chat);

$qaOptions = erLhcoreClassModelChatConfig::fetch('lhcqa_options');
$data = (array)$qaOptions->data;
$formId = isset($data['form_id']) ? (int)$data['form_id'] : 0;
$tpl->set('form_id', $formId);

$existingCollected = erLhAbstractModelFormCollected::findOne(array(
    'filter' => array('form_id' => $formId, 'chat_id' => $chat->id),
    'sort' => 'id DESC'
));

if ($existingCollected instanceof erLhAbstractModelFormCollected) {
    erLhcoreClassFormRenderer::setCollectedInformation($existingCollected->content_array);
    erLhcoreClassFormRenderer::setCollectedObject($existingCollected);

    $form = $existingCollected->form;
    $tpl->set('content', $form instanceof erLhAbstractModelForm ? $form->content_rendered_admin : '');
} else {
    $tpl->set('content', '');
}

echo $tpl->fetch();
exit;

?>