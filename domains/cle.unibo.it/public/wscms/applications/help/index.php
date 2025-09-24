<?php
/*	wscms/help/index.php v.3.5.4. 30/07/2019 */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr(Core::$request->method,-4,4)) {	
	default:
		$App->sessionName = $App->sessionName.'-item';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['page']))  $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		if ($App->userLoggedData->is_root == 1) {
			include_once(PATH.$App->pathApplications.Core::$request->action."/adminitems.php");
			//include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		} else {
			include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		}	
	break;
}
?>