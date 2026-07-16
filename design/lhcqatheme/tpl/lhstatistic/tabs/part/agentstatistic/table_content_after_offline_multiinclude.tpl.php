<?php if (isset($qaFormId) && $qaFormId > 0) : ?>
<td nowrap>
    <?php if ($info->qaChats > 0) : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo (int)$qaFormId?>/(chat_time)/1<?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->qaScore;?> %</a>
    <?php else : ?>
        n/a
    <?php endif;?>
</td>
<td nowrap>
    <?php if ($info->qaChats > 0) : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo (int)$qaFormId?>/(chat_time)/1<?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->qaChats;?></a>
    <?php else : ?>
        n/a
    <?php endif;?>
</td>
<?php endif; ?>