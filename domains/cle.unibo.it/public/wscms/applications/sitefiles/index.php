<?php
/* wscms/site-files/index.php v.1.0.0. 26/06/2016
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/module.class.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;
$App->orderingType =  $App->params->orderingType;
$App->labels =  $App->params->labels;
$App->itemUploadPathDir = $App->params->itemUploadPathDir;
$App->itemUploadDir = $App->params->itemUploadDir;

$App->tableFold = $App->params->tableFold;
$App->fieldsFold = $App->params->fieldsFold;
$App->tableItem = $App->params->tableItem;
$App->fieldsItem = $App->params->fieldsItem;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
	
switch(substr(Core::$request->method,-4,4)) {	
	case 'Fold':
		$App->sessionName = $App->sessionName.'-fold';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module(Core::$request->action,$App->tableFold);
		include_once(PATH.'applications/'.Core::$request->action."/folders.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/folders.js"></script>';		
	break;
	
	default:
		$App->sessionName = $App->sessionName.'-items';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module(Core::$request->action,$App->tableItem);
		include_once(PATH.'applications/'.Core::$request->action."/items.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/items.js"></script>';		
	break;
	}
?>