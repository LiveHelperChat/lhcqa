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

         <script>
            var highlightOptions = <?php echo json_encode(!empty($highlight_options) ? $highlight_options : array()); ?>;

            function buildPopoverContent(msg_id, chat_id) {
                var types = ($('#msg-' + msg_id).attr('data-qa-quoted') || '');
                var html = '';
                for (var typeKey in highlightOptions) {
                    if (highlightOptions.hasOwnProperty(typeKey)) {
                        var opt = highlightOptions[typeKey];
                        if (types.indexOf(typeKey + ',') !== -1) {
                            html += '<a href="#" class="d-block qa-popover-action" data-qa-type="' + typeKey + '" data-qa-action="remove" data-qa-chat="' + chat_id + '"><i class="material-icons">&#xe5cd;</i>' + opt.remove + '</a>';
                            html += '<a href="#" class="d-block mt-1 qa-popover-action" data-qa-type="' + typeKey + '" data-qa-action="update" data-qa-chat="' + chat_id + '"><i class="material-icons">&#xE244;</i>' + opt.update + '</a>';
                        } else {
                            html += '<a href="#" class="d-block qa-popover-action" data-qa-type="' + typeKey + '" data-qa-action="add" data-qa-chat="' + chat_id + '"><i class="material-icons">&#xE244;</i>' + opt.add + '</a>';
                        }
                    }
                }
                return html;
            }

            $('#preview-messages-'+<?php echo $chat->id?>+' > .message-row:not([qt])').on('mouseup',{chat_id:<?php echo $chat->id?>, that : lhinst}, function(e){

                 selected = e.data.that.getSelectedText();

                $('.popover-copy').popover('dispose');

                if (selected.text.length && (e.data.that.selection === null || e.data.that.selection.text !== selected.text)) {

                    e.data.that.selection = selected;

                    var msg_id = $(this).attr('id').replace('msg-','');

                    var quoteParams = {
                        placement:'right',
                        trigger:'manual',
                        animation:false,
                        sanitize: false,
                        html:true,
                        container:'#preview-messages-'+e.data.chat_id,
                        template : '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>',
                        content: buildPopoverContent(msg_id, e.data.chat_id)
                    }
                    
                    var placement = typeof $(this).attr('id') !== 'undefined' ? '#preview-messages-'+e.data.chat_id+' > #msg-'+msg_id+' > .msg-body' : this;

                    var containerPopover = $(placement);

                    if (containerPopover.length == 0) return ;
  
                    containerPopover.popover(quoteParams).popover('show').addClass('popover-copy');

                    $('.qa-popover-action').off('click').on('click', function(){
                        var qaType = $(this).attr('data-qa-type');
                        var qaAction = $(this).attr('data-qa-action');
                        var msgRow = $('#msg-' + msg_id);

 
                        if (qaAction === 'add') {
                            var result = document.getElementById('qa-form-evaluate').contentWindow.addQuoteAction({
                                type: qaType,
                                chat_id: e.data.chat_id,
                                msg_id: msg_id,
                                text: lhinst.getSelectedTextPlain()
                            }, 'add');
                            if (result.bgClass) msgRow.addClass(result.bgClass);
                            showQuoteBadge(msgRow, qaType, lhinst.getSelectedTextPlain());
                            msgRow.attr('data-qa-quoted', (msgRow.attr('data-qa-quoted') || '') + qaType + ',');
                        } else if (qaAction === 'remove') {
                            var quotedTypes = (msgRow.attr('data-qa-quoted') || '');
                            var result = document.getElementById('qa-form-evaluate').contentWindow.addQuoteAction({
                                type: qaType,
                                chat_id: e.data.chat_id,
                                msg_id: msg_id,
                                text: ''
                            }, 'remove');
                            if (result.bgClass) msgRow.removeClass(result.bgClass);
                            hideQuoteBadge(msgRow, qaType);
                            var types = quotedTypes.replace(qaType + ',', '');
                            if (types) { msgRow.attr('data-qa-quoted', types); }
                            else { msgRow.removeAttr('data-qa-quoted'); }
                            $('.popover-copy').popover('dispose');
                        } else if (qaAction === 'update') {
                            document.getElementById('qa-form-evaluate').contentWindow.addQuoteAction({
                                type: qaType,
                                chat_id: e.data.chat_id,
                                msg_id: msg_id,
                                text: ''
                            }, 'remove');
                            var result = document.getElementById('qa-form-evaluate').contentWindow.addQuoteAction({
                                type: qaType,
                                chat_id: e.data.chat_id,
                                msg_id: msg_id,
                                text: lhinst.getSelectedTextPlain()
                            }, 'add');
                            if (result.bgClass) msgRow.addClass(result.bgClass);
                            showQuoteBadge(msgRow, qaType, lhinst.getSelectedTextPlain());
                            $('.popover-copy').popover('dispose');
                        }
                    });

                    e.data.that.popoverShown = true;
                    e.data.that.popoverShownNow = true;
                } else {
                    e.data.that.selection = null;
                }
            });
        </script>

    </div>

    <div class="col-6 p-0 d-flex flex-column" style="min-height:0">
        <script>
            function showQuoteBadge(msgRow, type, text) {
                var msgBody = msgRow.find('.msg-body');
                if (!msgBody.length) return;
                var badgesContainer = msgBody.find('.qa-quote-badges');
                if (!badgesContainer.length) {
                    badgesContainer = $('<div class="qa-quote-badges mt-1"></div>');
                    msgBody.append(badgesContainer);
                }
                badgesContainer.find('[data-qa-badge-type="' + type + '"]').remove();
                var badge = $('<span class="badge bg-warning text-dark me-1 mb-1" data-qa-badge-type="' + type + '"><i class="material-icons fs12 me-1">format_quote</i>' + $('<span>').text(text).html() + '</span>');
                badgesContainer.append(badge);
            }
            function hideQuoteBadge(msgRow, type) {
                var msgBody = msgRow.find('.msg-body');
                if (!msgBody.length) return;
                msgBody.find('[data-qa-badge-type="' + type + '"]').remove();
                var badgesContainer = msgBody.find('.qa-quote-badges');
                if (badgesContainer.length && !badgesContainer.children().length) {
                    badgesContainer.remove();
                }
            }
            function applyQuoteHighlights(quotes) {
                if (!quotes || !quotes.length) return;
                quotes.forEach(function(q) {
                    var msgRow = $('#msg-' + q.msg_id);
                    if (!msgRow.length) return;
                    var quotedTypes = (msgRow.attr('data-qa-quoted') || '');
                    if (quotedTypes.indexOf(q.type + ',') === -1) {
                        if (q.bgClass) msgRow.addClass(q.bgClass);
                        msgRow.attr('data-qa-quoted', quotedTypes + q.type + ',');
                        if (q.text) showQuoteBadge(msgRow, q.type, q.text);
                    }
                });
            }
        </script>
        <iframe id="qa-form-evaluate" style="flex:1;border:0;overflow:auto" class="pb-2 w-100" src="<?php echo erLhcoreClassDesign::baseurl('form/fill')?>/<?php echo $form_id?>?chat_id=<?php echo $chat->id?>" ></iframe>
    </div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>

