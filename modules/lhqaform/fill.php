<?php

$chatId = isset($Params['user_parameters']['chat_id']) ? (int)$Params['user_parameters']['chat_id'] : 0;
$chat = $chatId > 0 ? erLhcoreClassModelChat::fetch($chatId) : false;

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {
    $tpl = erLhcoreClassTemplate::getInstance('lhqaform/fill.tpl.php');
    $tpl->set('chat', $chat);

    $qaOptions = erLhcoreClassModelChatConfig::fetch('lhcqa_options');
    $data = (array)$qaOptions->data;
    $tpl->set('form_id', isset($data['form_id']) ? $data['form_id'] : 0);

    echo $tpl->fetch();
}

exit;

?>