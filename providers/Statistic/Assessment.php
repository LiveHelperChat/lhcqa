<?php

namespace LiveHelperChatExtension\lhcqa\providers\Statistic;

class Assessment
{
    public static function statisticAggregate($params)
    {
        $qaOptions = \erLhcoreClassModelChatConfig::fetch('lhcqa_options');
        $data = (array)$qaOptions->data;

        $filterAggregate = $params['filter']['filter'];
        $filterAggregate['filterin']['`lh_chat`.`user_id`'] = array_keys($params['filter']['user_list']);
        $filterAggregate['filter']['`lh_abstract_form_collected`.`form_id`'] = $data['form_id'];
        $filterAggregate['innerjoin']['lh_chat'] = array('`lh_abstract_form_collected`.`chat_id`','`lh_chat`.`id`');
        $filterAggregate['group'] = '`lh_chat`.`user_id`';

        $avgScore = \erLhAbstractModelFormCollected::getCount(
            $filterAggregate,                           // $params
            '',                                         // $operattion
            false,                                      // $field
            '`lh_chat`.`user_id`, AVG(`lh_abstract_form_collected`.`attr_int_1`) as qa_score, COUNT(`lh_chat`.`id`) as qa_count',   // $rawSelect
            false,                                      // $fetchColumn
            true                                        // $fetchAll
        );

        // Transform $avgScore array into qa_list keyed by user_id
        // Input:  [['user_id' => 1, 'qa_score' => 74.0000, 'qa_count' => 1], ...]
        // Output: [1 => ['score' => 74.0000, 'chats' => 1], ...]
        $params['status_extension']['qa_list'] = [];
        foreach ($avgScore as $row) {
            $params['status_extension']['qa_list'][$row['user_id']] = [
                'score' => round($row['qa_score'],2),
                'chats' => $row['qa_count']
            ];
        }
    }

    public static function statisticAggregateItem($params)
    {
        $params['item']['qaChats'] = $params['status_extension']['qa_list'][$params['item']['userId']]['chats'] ?? 0;
        $params['item']['qaScore'] = $params['status_extension']['qa_list'][$params['item']['userId']]['score'] ?? 0;
    }

    public static function statisticAggregateAvg($params)
    {
        $params['attr'][] = 'qaChats';
        $params['attr'][] = 'qaScore';
    }

    public static function statisticXLSColumn($params)
    {
        $params['last_column']++;
        $params['xls']->getActiveSheet()->setCellValueByColumnAndRow($params['last_column'], 2, 'QA Chats');
        $params['last_column']++;
        $params['xls']->getActiveSheet()->setCellValueByColumnAndRow($params['last_column'], 2, 'QA Score');
    }

    public static function statisticXLSColumnValue($params)
    {
        $params['key']++;
        $params['xls']->getActiveSheet()->setCellValueByColumnAndRow($params['key'], $params['i'], $params['item']->qaChats);
        $params['key']++;
        $params['xls']->getActiveSheet()->setCellValueByColumnAndRow($params['key'], $params['i'], $params['item']->qaScore);
    }

    public static function performanceColumns($params)
    {
        if ($params['scope'] !== 'op') {
            return;
        }

        $params['columns'][] = 'qaChats';
        $params['columns'][] = 'qaScore';
        if (isset($params['translations'])) {
            $params['translations']['qaChats'] = \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'QA Chats');
            $params['translations']['qaScore'] = \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'QA Score');
        }
    }

    public static function performanceAggregate($params)
    {
        if ($params['scope'] !== 'op' || !array_intersect(array('qaChats', 'qaScore'), $params['configuration']['columns'])) {
            return;
        }

        $qaOptions = \erLhcoreClassModelChatConfig::fetch('lhcqa_options');
        $data = (array)$qaOptions->data;
        if (empty($data['form_id']) || empty($params['filter']['filtergte']['time'])) {
            return;
        }

        $filter = array(
            'limit' => false,
            'filter' => array('form_id' => $data['form_id']),
            'filtergte' => array('`lh_chat`.`time`' => $params['filter']['filtergte']['time']),
            'innerjoin' => array('lh_chat' => array('`lh_abstract_form_collected`.`chat_id`', '`lh_chat`.`id`')),
            'group' => '`lh_chat`.`user_id`',
        );

        if (!empty($params['user_list'])) {
            $filter['filterin']['`lh_chat`.`user_id`'] = array_keys($params['user_list']);
        }

        $rows = \erLhAbstractModelFormCollected::getCount(
            $filter,
            '',
            false,
            '`lh_chat`.`user_id` AS user_id, COUNT(`lh_abstract_form_collected`.`id`) AS qa_count, AVG(`lh_abstract_form_collected`.`attr_int_1`) AS qa_score',
            false,
            true
        );

        $params['result']['qaChats'] = array();
        $params['result']['qaScore'] = array();
        foreach ($rows as $row) {
            $row['qa_score'] = round((float)$row['qa_score'], 2);
            $params['result']['qaChats'][] = array('user_id' => $row['user_id'], 'qaChats' => $row['qa_count']);
            $params['result']['qaScore'][] = array('user_id' => $row['user_id'], 'qaScore' => $row['qa_score'] . ' %');
        }
    }
}
