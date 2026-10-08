<?php

$tpl = erLhcoreClassTemplate::getInstance('lhqaform/settings.tpl.php');

$qaOptions = erLhcoreClassModelChatConfig::fetch('lhcqa_options');
$data = (array)$qaOptions->data;

$internalForms = erLhAbstractModelForm::getList(array(
    'filter' => array('active' => 1,'form_type' => erLhAbstractModelForm::FORM_TYPE_INTERNAL),
    'limit' => false,
    'sort' => 'name ASC'
));

if (isset($_POST['StoreOptions'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('qaform/settings');
        exit;
    }

    $definition = array(
        'form_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        ),
        'highlight_options' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('form_id')) {
        $selectedForm = erLhAbstractModelForm::findOne(array(
            'filter' => array(
                'id' => (int)$form->form_id,
                'active' => 1,
                'form_type' => erLhAbstractModelForm::FORM_TYPE_INTERNAL
            )
        ));

        $data['form_id'] = $selectedForm instanceof erLhAbstractModelForm ? (int)$selectedForm->id : 0;
    } else {
        $data['form_id'] = 0;
    }

    if ($form->hasValidData('highlight_options') && $form->highlight_options != '') {
        $decoded = json_decode($form->highlight_options, true);
        if (is_array($decoded)) {
            $data['highlight_options'] = $decoded;
        }
    } else {
        $data['highlight_options'] = array();
    }

    $survey = array();

    foreach ($internalForms as $formItem) {

        $inputName = 'dep_ids_' . (int)$formItem->id;

        if (!isset($_POST[$inputName]) || !is_array($_POST[$inputName])) {
            continue;
        }

        $depIds = array();

        foreach ($_POST[$inputName] as $depId) {
            $depId = (int)$depId;
            if ($depId > 0 && !in_array($depId, $depIds, true)) {
                $depIds[] = $depId;
            }
        }

        if (!empty($depIds)) {
            $survey[(int)$formItem->id] = $depIds;
        }
    }

    $data['survey'] = $survey;

    $qaOptions->explain = '';
    $qaOptions->type = 0;
    $qaOptions->hidden = 1;
    $qaOptions->identifier = 'lhcqa_options';
    $qaOptions->value = serialize($data);
    $qaOptions->saveThis();

    $tpl->set('updated', 'done');
}

\LiveHelperChatExtension\lhcqa\providers\Statistic\Assessment::resetCache();

$tpl->set('qa_options', $data);

$tpl->set('forms', $internalForms);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('qaform/settings'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'QA Forms Settings')
    )
);

?>
