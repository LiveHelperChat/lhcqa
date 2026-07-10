<?php

$tpl = erLhcoreClassTemplate::getInstance('lhqaform/settings.tpl.php');

$qaOptions = erLhcoreClassModelChatConfig::fetch('lhcqa_options');
$data = (array)$qaOptions->data;

if (isset($_POST['StoreOptions'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('qaform/settings');
        exit;
    }

    $definition = array(
        'form_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
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

    $qaOptions->explain = '';
    $qaOptions->type = 0;
    $qaOptions->hidden = 1;
    $qaOptions->identifier = 'lhcqa_options';
    $qaOptions->value = serialize($data);
    $qaOptions->saveThis();

    $tpl->set('updated', 'done');
}

$tpl->set('qa_options', $data);

$tpl->set('forms', erLhAbstractModelForm::getList(array(
    'filter' => array('active' => 1,'form_type' => erLhAbstractModelForm::FORM_TYPE_INTERNAL),
    'limit' => false,
    'sort' => 'name ASC'
)));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('qaform/settings'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhcqa/module', 'QA Forms Settings')
    )
);

?>
