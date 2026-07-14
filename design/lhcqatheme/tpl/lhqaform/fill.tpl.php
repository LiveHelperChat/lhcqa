<div class="modal-dialog modal-xl">
    <div class="modal-content modal-content-fscreen">
      <div class="modal-header pt-1 pb-1 ps-2 pe-2">

        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?> <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>

	<?php echo htmlspecialchars($user->name)?>&nbsp;<?php echo htmlspecialchars($user->surname)?>
	<?php else : ?>
	-
<?php endif; ?><?php if ($chat->department != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->department)?><?php endif;?><?php if ($chat->product != '') : ?>&nbsp;|&nbsp;<?php echo htmlspecialchars($chat->product)?><?php endif;?>
</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          
      </div>
        <div class="p-1 border-bottom">
            <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>

            <i class="material-icons">label</i><small>ID - <?php echo $chat->id?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created')?> - <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?> - <?php echo $chat->chat_duration_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?> - <?php echo $chat->wait_time_front?></small>&nbsp;<i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Priority')?> - (<?php echo $chat->priority?>)</small>

            <?php if ($chat->online_user_id > 0) : ?><i class="material-icons">label</i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor ID')?> - <?php echo $chat->online_user_id?></small><?php endif; ?>

            <?php if ($chat->bot) : ?>
                <span class="material-icons">android</span><small><?php if (!empty($chat->bot->short_name)) : ?><?php echo htmlspecialchars($chat->bot->short_name); ?><?php else : ?><?php echo htmlspecialchars($chat->gbot_id); ?><?php endif; ?></small>
            <?php endif; ?>

            <?php foreach (erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id))) as $subject) :?>
                <span class="badge bg-info fs12 me-1" <?php if ($subject->subject->color != '') : ?>style="background-color:#<?php echo htmlspecialchars($subject->subject->color)?>!important;" <?php endif;?> ><?php echo htmlspecialchars($subject->subject)?></span>
            <?php endforeach; ?>
        </div>

<div class="modal-body pt-0 pb-0 pe-0 d-flex flex-column">

<div class="row flex-grow-1 m-0">
    <div class="col-6 d-flex flex-column p-0">
        <div id="preview-messages-<?php echo $chat->id?>" class="msgBlock-admin fs13" style="flex:1;overflow-y:auto;overflow-x:hidden; max-height: calc(100vh - 8rem);">
            <?php $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
            <?php if (isset($keyword) && !empty($keyword)) : ?>
                <?php foreach ($messages as $message) : ?>
                    <?php if (is_array($keyword)) : ?>
                        <?php foreach ($keyword as $keywordItem) { $message->msg = preg_replace_callback('/(\[[^\]]*\])|(\b' . preg_quote($keywordItem, '/') . '\b)/is', function($matches) { if (!empty($matches[1])) { return $matches[1]; } return '[level=bg-warning text-dark rounded p-1 d-inline-block][i][b][fs14]' . $matches[2] . '[/fs][/b][/i][/level]'; }, $message->msg); } ?>
                    <?php else : ?>
                        <?php $message->msg = preg_replace_callback('/(\[[^\]]*\])|(\b' . preg_quote($keyword, '/') . '\b)/is', function($matches) { if (!empty($matches[1])) { return $matches[1]; } return '[level=bg-warning text-dark rounded p-1 d-inline-block][i][b][fs14]' . $matches[2] . '[/fs][/b][/i][/level]'; }, $message->msg); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
         </div>
    </div>
    <div class="col-6 p-0 d-flex flex-column" style="min-height:0">
        <iframe style="flex:1;border:0;overflow:auto" class="pb-2 w-100" src="<?php echo erLhcoreClassDesign::baseurl('form/fill')?>/<?php echo $form_id?>?chat_id=<?php echo $chat->id?>" ></iframe>
    </div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>

