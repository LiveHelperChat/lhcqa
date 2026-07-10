<td nowrap>
    <?php if ($info->qaChats > 0) : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->qaScore;?> %</a>
    <?php else : ?>
        n/a
    <?php endif;?>
</td>
<td nowrap>
    <?php if ($info->qaChats > 0) : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->qaChats;?></a>
    <?php else : ?>
        n/a
    <?php endif;?>
</td>