<?php

$Module = array( "name" => "Quality Assessment" );

$ViewList = array();

$ViewList['fill'] = array(
    'params' => array('chat_id'),
    'functions' => array('fill')
);

$ViewList['seedetails'] = array(
    'params' => array('chat_id'),
    'functions' => array('see_detailed')
);

$ViewList['settings'] = array(
    'params' => array(),
    'functions' => array('configure')
);

$FunctionList['configure'] = array('explain' => 'Allow operator to configure Quality Assessments Forms');
$FunctionList['fill'] = array('explain' => 'Allow operator to fill Quality Assessments Forms');
$FunctionList['see_detailed'] = array('explain' => 'Allow operator to see Quality Assessments Form details');
