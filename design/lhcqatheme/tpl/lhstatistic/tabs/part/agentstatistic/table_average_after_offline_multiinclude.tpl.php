<?php if (\LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment::hasAnyForm()) : ?>
<td>
    <?php echo htmlspecialchars($agentStatistic_avg['qaScore'])?> %
</td>
<td>
    <?php echo htmlspecialchars($agentStatistic_avg['qaChats'])?>
</td>
<?php endif; ?>