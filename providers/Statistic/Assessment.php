<?php

namespace LiveHelperChatExtension\lhcqa\providers\Statistic;

class Assessment
{
    private static $options = null;

    /**
     * Fetch and cache lhcqa_options data
     */
    public static function getOptions()
    {
        if (self::$options === null) {
            $qaOptions = \erLhcoreClassModelChatConfig::fetch('lhcqa_options');
            self::$options = (array)$qaOptions->data;
        }

        return self::$options;
    }

    /**
     * Reset cached options (useful after settings update)
     */
    public static function resetCache()
    {
        self::$options = null;
    }

    /**
     * Default QA survey (form_id)
     */
    public static function getDefaultFormId()
    {
        $options = self::getOptions();

        return isset($options['form_id']) ? (int)$options['form_id'] : 0;
    }

    /**
     * Sanitized per-department survey map: form_id => array(dep_id, ...)
     */
    public static function getSurveyMap()
    {
        $options = self::getOptions();

        $survey = array();

        if (isset($options['survey']) && is_array($options['survey'])) {
            foreach ($options['survey'] as $formId => $depIds) {

                $formId = (int)$formId;

                if ($formId <= 0 || !is_array($depIds)) {
                    continue;
                }

                $cleanDepIds = array();

                foreach ($depIds as $depId) {
                    $depId = (int)$depId;
                    if ($depId > 0 && !in_array($depId, $cleanDepIds, true)) {
                        $cleanDepIds[] = $depId;
                    }
                }

                if (!empty($cleanDepIds)) {
                    $survey[$formId] = $cleanDepIds;
                }
            }
        }

        return $survey;
    }

    /**
     * Resolve which QA survey should be used for a chat.
     * Department assignment wins, otherwise default form_id is used.
     */
    public static function getFormIdForChat($chat)
    {
        $depId = ($chat instanceof \erLhcoreClassModelChat && $chat->dep_id > 0) ? (int)$chat->dep_id : 0;

        if ($depId > 0) {
            foreach (self::getSurveyMap() as $formId => $depIds) {
                if (in_array($depId, $depIds, true)) {
                    return (int)$formId;
                }
            }
        }

        return self::getDefaultFormId();
    }

    /**
     * Is at least one QA survey configured (default or per-department)
     */
    public static function hasAnyForm()
    {
        return self::getDefaultFormId() > 0 || count(self::getSurveyMap()) > 0;
    }

    /**
     * All configured form ids (default + per-department surveys)
     */
    public static function getAllFormIds()
    {
        $formIds = array();

        if (self::getDefaultFormId() > 0) {
            $formIds[] = self::getDefaultFormId();
        }

        foreach (array_keys(self::getSurveyMap()) as $formId) {
            if (!in_array($formId, $formIds, true)) {
                $formIds[] = $formId;
            }
        }

        return $formIds;
    }

    public static function statisticAggregate($params)
    {
        if (!self::hasAnyForm()) {
            return;
        }

        $filterAggregate = $params['filter']['filter'];
        $filterAggregate['filterin']['`lh_chat`.`user_id`'] = array_keys($params['filter']['user_list']);
        $filterAggregate['filterin']['`lh_abstract_form_collected`.`form_id`'] = self::getAllFormIds();
        $filterAggregate['innerjoin']['lh_chat'] = array('`lh_abstract_form_collected`.`chat_id`','`lh_chat`.`id`');
        $filterAggregate['group'] = '`lh_chat`.`user_id`';

        $avgScore = \erLhAbstractModelFormCollected::getCount(
            $filterAggregate,                           // $params
            '',                                         // $operattion
            false,                                      // $field
            '`lh_chat`.`user_id`, MAX(`lh_abstract_form_collected`.`form_id`) as form_id, AVG(`lh_abstract_form_collected`.`attr_int_1`) as qa_score, COUNT(`lh_chat`.`id`) as qa_count',   // $rawSelect
            false,                                      // $fetchColumn
            true                                        // $fetchAll
        );

        // Transform $avgScore array into qa_list keyed by user_id
        // Input:  [['user_id' => 1, 'form_id' => 6, 'qa_score' => 74.0000, 'qa_count' => 1], ...]
        // Output: [1 => ['score' => 74.0000, 'chats' => 1, 'form_id' => 6], ...]
        $params['status_extension']['qa_list'] = [];
        foreach ($avgScore as $row) {
            $params['status_extension']['qa_list'][$row['user_id']] = [
                'score' => round($row['qa_score'],2),
                'chats' => $row['qa_count'],
                'form_id' => (int)$row['form_id']
            ];
        }
    }

    public static function statisticAggregateItem($params)
    {
        $params['item']['qaChats'] = $params['status_extension']['qa_list'][$params['item']['userId']]['chats'] ?? 0;
        $params['item']['qaScore'] = $params['status_extension']['qa_list'][$params['item']['userId']]['score'] ?? 0;
        $params['item']['qaFormId'] = $params['status_extension']['qa_list'][$params['item']['userId']]['form_id'] ?? 0;
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

        if (!self::hasAnyForm() || empty($params['filter']['filtergte']['time'])) {
            return;
        }

        $filter = array(
            'limit' => false,
            'filterin' => array('form_id' => self::getAllFormIds()),
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
