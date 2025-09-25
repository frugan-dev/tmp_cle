<?php
/* wscms/site-pages/index.php v.1.0.1. 07/09/2016 */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");



include_once(PATH.'applications/'.Core::$request->action."/config.inc.php");

include_once(PATH.'applications/'.Core::$request->action."/module.class.php");



$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->appUploadPathDir = $App->params->uploadPathDir;
$App->appUploadDir = $App->params->uploadDir;
$App->typePage = $App->params->typePage;
$App->targets = $App->params->targets;

$App->orderingType = $App->params->orderingType;

$App->labels =  $App->params->labels;

$tableRif = $App->params->tableRif;
$table = $App->params->table;
$fields = $App->params->fields;

$App->tableItem = $App->params->table;
$App->fieldsItem = $App->params->fields;

$App->tableIfil = $App->params->tableIfil;
$App->fieldsIfil = $App->params->fieldsIfil;

$App->itemUploadPathDir = $App->params->uploadPathDir;
$App->itemUploadDir = $App->params->uploadDir;
$App->ifilUploadPathDir = $App->params->ifilUploadPathDir;
$App->ifilUploadDir = $App->params->ifilUploadDir;


$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr((string) Core::$request->method,-4,4)) {
	case 'Ifil':
		$App->sessionName = $App->sessionName.'-files';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10']);
		$Module = new Module($table,Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.'applications/'.Core::$request->action."/files.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/files.js"></script>';		
	break;

	default;
		$App->sessionName = $App->sessionName.'-page';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'25']);
		$Module = new Module($table,Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';

		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.js" type="text/javascript"></script>';
		$App->css[] = '<link rel="stylesheet" href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.css"/>';
	
		


		include_once(PATH.'applications/'.Core::$request->action."/pages.php");
		
		$datetime = DateTime::createFromFormat('Y-m-d H:i:s',$App->item->updated);
		$errors = DateTime::getLastErrors();
		if ($errors['error_count'] == 0 && $errors['warning_count'] == 0) { 
			$defaultdate = $datetime->format('Y-m-d H:i:s');	
		} else {				
			$defaultdate = $App->nowDateTime; 
		}
		$App->defaultJavascript = "defaultdate = '".$defaultdate."';";
		//$App->css[] = '<link href="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/items.css" rel="stylesheet">';		
	break;	
	}
?>
