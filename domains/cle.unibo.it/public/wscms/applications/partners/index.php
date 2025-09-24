<?php
/* wscms/partners/index.php v.1.0.0. 28/06/2016
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/module.class.php");

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
$App->targets = $App->params->targets;

$App->itemUploadPathDir = $App->params->itemUploadPathDir;
$App->itemUploadDir = $App->params->itemUploadDir;
$App->cateUploadPathDir = $App->params->cateUploadPathDir;
$App->cateUploadDir = $App->params->cateUploadDir;
$App->scatUploadPathDir = $App->params->scatUploadPathDir;
$App->scatUploadDir = $App->params->scatUploadDir;
$App->ifilUploadPathDir = $App->params->ifilUploadPathDir;
$App->ifilUploadDir = $App->params->ifilUploadDir;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr((string) Core::$request->method,-4,4)) {	
	case 'Ifil':
		$App->sessionName = $App->sessionName.'-files';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10']);
		$Module = new Module(Core::$request->action,$App->tableIfil);
		include_once(PATH.'application/'.Core::$request->action."/files.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'application/'.Core::$request->action.'/files.js"></script>';		
	break;

	case 'Cate':
		$App->sessionName = $App->sessionName.'-cate';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10']);
		$Module = new Module(Core::$request->action,$App->tableCate);
		include_once(PATH.'application/'.Core::$request->action."/categories.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'application/'.Core::$request->action.'/categories.js"></script>';		
	break;

	default:
		$App->sessionName = $App->sessionName.'-item';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10']);
		$Module = new Module(Core::$request->action,$App->tableItem);
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/items.js"></script>';	
	break;
	}	
?>