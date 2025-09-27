<?php

/*
    framework siti html-PHP-Mysql
    copyright 2011 Roberto Mantovani
    http://www.robertomantovani.vr;it
    email: me@robertomantovani.vr.it
    news/index.php v.2.6.3. 11/04/2016
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action.'/lang/'.$_lang['user'].'.inc.php');
include_once(PATH.'applications/'.Core::$request->action.'/config.inc.php');
include_once(PATH.'applications/'.Core::$request->action.'/module.class.php');

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->tableItem = $App->params->tableItem;
$App->fieldsItem = $App->params->fieldsItem;
$App->tableCate = $App->params->tableCate;
$App->fieldsCate = $App->params->fieldsCate;
$App->tableIfil = $App->params->tableIfil;
$App->fieldsIfil = $App->params->fieldsIfil;

$App->orderingType =  $App->params->orderingType;
$App->labels =  $App->params->labels;

$App->itemUploadPathDir = $App->params->itemUploadPathDir;
$App->itemUploadDir = $App->params->itemUploadDir;
$App->cateUploadPathDir = $App->params->cateUploadPathDir;
$App->cateUploadDir = $App->params->cateUploadDir;
$App->scatUploadPathDir = $App->params->scatUploadPathDir;
$App->scatUploadDir = $App->params->scatUploadDir;
$App->ifilUploadPathDir = $App->params->ifilUploadPathDir;
$App->ifilUploadDir = $App->params->ifilUploadDir;

$App->params->tables['news'] = $App->tableItem;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) {
    $App->id = intval($_POST['id']);
}

$App->defaultJavascript = '';

switch (substr((string) Core::$request->method, -4, 4)) {
    case 'Conf':
        $Module = new Module(Core::$request->action, $App->params->tables['news']);
        include_once(PATH.$App->pathApplications.Core::$request->action.'/config.php');
        break;

    case 'Ifil':
        $App->sessionName = $App->sessionName.'-files';
        $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS, $App->sessionName, ['page' => 1,'ifp' => '10']);
        $Module = new Module(Core::$request->action, $App->tableIfil);
        include_once(PATH.'applications/'.Core::$request->action.'/item-files.php');
        break;

    case 'Cate':
        $App->sessionName = $App->sessionName.'-cate';
        $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS, $App->sessionName, ['page' => 1,'ifp' => '10']);
        $Module = new Module(Core::$request->action, $App->tableCate);
        include_once(PATH.'applications/'.Core::$request->action.'/categories.php');
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/categories.js"></script>';
        break;

    default:
        $App->sessionName = $App->sessionName.'-item';
        $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS, $App->sessionName, ['page' => 1,'ifp' => '10']);
        $Module = new Module(Core::$request->action, $App->tableItem);
        include_once(PATH.'applications/'.Core::$request->action.'/items.php');
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $App->item->datatimeins);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] == 0 && $errors['warning_count'] == 0) {
            $defaultdateins = $datetime->format('Y-m-d H:i:s');
        } else {
            $defaultdateins = Config::$nowDateTimeIso;
        }
        $App->defaultJavascript .= "defaultdate = '".$defaultdateins."';";
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';

        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.js" type="text/javascript"></script>';
        $App->css[] = '<link rel="stylesheet" href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.css"/>';

        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'. Core::$request->action.'/items.js"></script>';

        break;
}
