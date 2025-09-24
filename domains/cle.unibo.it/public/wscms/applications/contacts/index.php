<?php
/* wscms/contacts/index.php v.3.5.4. 02/04/2019  */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);


switch(substr((string) Core::$request->method,-4,4)) {
	case 'Conf':
	default:	
		$App->sessionName = 'config';
		$Module = new Module($App->sessionName,$App->params->tables['conf']);		
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/config.php");	
	break;
	}
?>
