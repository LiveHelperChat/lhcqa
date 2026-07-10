<?php

class erLhcoreClassExtensionLhcqa
{
    public function __construct()
    {

    }

    public function run()
    {
        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
        $dispatcher->listen('statistic.getagentstatistic_extensions', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','statisticAggregate'));
        $dispatcher->listen('statistic.getagentstatistic_extensions_item', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','statisticAggregateItem'));
        $dispatcher->listen('statistic.getagentstatisticaveragefield', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','statisticAggregateAvg'));
        $dispatcher->listen('statistic.getagentstatistic_export_columns', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','statisticXLSColumn'));
        $dispatcher->listen('statistic.getagentstatistic_export_columns_value', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','statisticXLSColumnValue'));
        $dispatcher->listen('statistic.performance_columns', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','performanceColumns'));
        $dispatcher->listen('statistic.performance_aggregate', array('LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment','performanceAggregate'));
        $dispatcher->listen('chat.trans_lhcbo', array($this,'translationsBo'));
    }

    function translationsBo($params)
    {
        $params['trans']['dep_performance.col_qaChats'] = 'QA Chats';
        $params['trans']['dep_performance.col_qaScore'] = 'QA Score';
    }

    public function __get($var) {
        switch ($var) {

            default :
                ;
                break;
        }
    }
}