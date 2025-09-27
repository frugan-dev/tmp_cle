<?php

/* wscms/site-filemanager/index.php v.3.5.1. 09/01/2018 */

$App->sessionName = Core::$request->action;
$App->codeVersion = ' 4.0.0.';
$App->pageTitle = 'File manager';
$App->pageSubTitle = 'Gestisci i files del sito';
$App->templateApp = 'frame.html';
$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/module.js" type="text/javascript"></script>';
