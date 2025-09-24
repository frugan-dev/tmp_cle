<?php
/* wscms/gallery/index.php v.3.5.4. 17/06/2019 */

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

switch(substr(Core::$request->method,-4,4)) {	
	case 'Tags':	
		$App->sessionName = $App->sessionName.'-tags';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,Config::$DatabaseTablesFields['galleriesimages tags']);		
		include_once(PATH.$App->pathApplications.Core::$request->action."/tags.php");	
	break;

	case 'Cate':	
		$App->sessionName = $App->sessionName.'-cate';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,Config::$DatabaseTablesFields['galleriesimages categories']);		
		include_once(PATH.$App->pathApplications.Core::$request->action."/categories.php");	
	break;		

	default:
		$App->sessionName = $App->sessionName;
		if (!isset($_SESSION[$App->sessionName]['categories_id'])) $_SESSION[$App->sessionName]['categories_id'] = 0;
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,Config::$DatabaseTablesFields['galleriesimages']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/images.php");
	break;
	}	
?>