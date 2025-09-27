<?php

/* wscms/site-galleries/index.php v.1.0.0. 26/05/2016 */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action.'/lang/'.$_lang['user'].'.inc.php');
include_once(PATH.$App->pathApplications.Core::$request->action.'/config.inc.php');
include_once(PATH.$App->pathApplications.Core::$request->action.'/module.class.php');

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;
$App->orderingType =  $App->params->orderingType;
$App->labels =  $App->params->labels;
$App->itemUploadPathDir = $App->params->itemUploadPathDir;
$App->itemUploadDir = $App->params->itemUploadDir;

$App->tableCate = $App->params->tableCate;
$App->fieldsCate = $App->params->fieldsCate;
$App->tableItem = $App->params->tableItem;
$App->fieldsItem = $App->params->fieldsItem;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) {
    $App->id = intval($_POST['id']);
}

switch (substr((string) Core::$request->method, -4, 4)) {
    case 'Cate':
        $App->sessionName = $App->sessionName.'-cate';
        $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS, $App->sessionName, ['page' => 1,'ifp' => '10']);
        $Module = new Module($App->sessionName, $App->tableCate);
        include_once(PATH.'applications/'.Core::$request->action.'/categories.php');
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/categories.js"></script>';
        break;

    default:
        $App->sessionName = $App->sessionName.'-items';
        $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS, $App->sessionName, ['page' => 1,'ifp' => '10']);
        $Module = new Module($App->sessionName, $App->tableItem);
        include_once(PATH.'applications/'.Core::$request->action.'/items.php');
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/items.js"></script>';
        break;
}
