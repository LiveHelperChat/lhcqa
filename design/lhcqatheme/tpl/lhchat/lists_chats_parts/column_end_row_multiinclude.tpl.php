<?php if ($qaCanFill || $qaCanSeeDetailed) : ?>
<td>
<?php if (isset($chat->chat_variables_array['qa_chat_score']) && !$qaCanFillPrivate) : ?>
    <?php if ($qaCanSeeDetailed) : ?>
        <button onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'qaform/seedetails/<?php echo $chat->id?>'})" class="btn btn-xs bg-success-subtle" type="button"><?php echo htmlspecialchars($chat->chat_variables_array['qa_chat_score']);?>%</button>
    <?php else : ?>
        <?php echo htmlspecialchars($chat->chat_variables_array['qa_chat_score']);?>%
    <?php endif; ?>
<?php endif;?>
<?php if ($qaCanFillPrivate && $qaCanFill) : ?>
&nbsp;<button onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'qaform/fill/<?php echo $chat->id?>'})" type="button" class="btn btn-xs <?php if (isset($chat->chat_variables_array['qa_chat_score'])) :?>bg-success-subtle<?php else : ?>bg-light<?php endif;?>"><?php if (isset($chat->chat_variables_array['qa_chat_score'])) :?><?php echo htmlspecialchars($chat->chat_variables_array['qa_chat_score']);?>%&nbsp;<?php endif;?>QA</button>
<?php endif; ?>
</td>
<?php endif; ?>