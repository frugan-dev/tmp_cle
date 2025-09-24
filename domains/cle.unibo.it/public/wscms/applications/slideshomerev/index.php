<?php
/* wscms/slides-home-rev/index.php v.3.5.4. 05/06/2019 */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".Config::$langVars['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr((string) Core::$request->method,-4,4)) {	
	case 'Laye':
		$App->sessionName = $App->sessionName.'-laye';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/layers.php");
	break;
	
	default:
		$App->sessionName = $App->sessionName;
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
	break;
	}	
?>