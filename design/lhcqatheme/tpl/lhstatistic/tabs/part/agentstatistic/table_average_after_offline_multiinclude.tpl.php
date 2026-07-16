<?php $qaFormId = erLhcoreClassModelChatConfig::fetch('lhcqa_options')->data['form_id']; ?>
<?php if ($qaFormId > 0) : ?>
<td>
    <?php echo htmlspecialchars($agentStatistic_avg['qaScore'])?> %
</td>
<td>
    <?php echo htmlspecialchars($agentStatistic_avg['qaChats'])?>
</td>
<?php endif; ?>