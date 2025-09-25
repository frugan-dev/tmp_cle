<?php
/* wscms/users/index.php v.3.5.4. 28/03/2019 */

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

$App->formTabActive = 1;

$App->user_levels = Config::$userLevels;
	
switch(substr((string) Core::$request->method,-4,4)) {		
	default:
		$App->sessionName .= '';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);
		$Module = new Module($App->sessionName,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		$App->defaultJavascript = "
		messages['password not match'] = '".addslashes((string) $_lang['Le due password non corrispondono!'])."';
		";
	break;
	}

?>