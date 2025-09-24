<?php
/* wscms/news/index.php v.3.5.4. 10/09/2019 */

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
	case 'Iimg':
		$App->sessionName = $App->sessionName.'-images';
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplication.Core::$request->action."/item-images.php");	
	break;
	case 'Ifil':
		$App->sessionName = $App->sessionName.'-files';
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/item-files.php");	
	break;
	case 'Igal':
		$App->sessionName = $App->sessionName.'-gallery';
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/item-gallery.php");	
	break;
	case 'Ivid':
		$App->sessionName = $App->sessionName.'-videos';
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		$App->params->fields['resources']['filename']['required'] = false;
		$App->params->fields['resources']['org_filename']['required'] = false;
		$App->params->fields['resources']['code']['required'] = true;
		include_once(PATH.$App->pathApplications.Core::$request->action."/item-videos.php");	
	break;
	case 'Cate':	
		$App->sessionName = $App->sessionName.'-categories';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,$App->params->tables['cate']);		
		include_once(PATH.$App->pathApplications.Core::$request->action."/categories.php");	
	break;
	case 'Tags':	
		$App->sessionName = $App->sessionName.'-tags';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,$App->params->tables['tags']);		
		include_once(PATH.$App->pathApplicationss.Core::$request->action."/tags.php");	
	break;		
	default:
		$App->patchdatapicker = 0;
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_cat'=>0));
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");					
		$defaultdatains = DateFormat::checkDataTimeIso($App->item->datatimeins,$App->nowDateTime);
		$defaultdatascaini = DateFormat::checkDataTimeIso($App->item->datatimescaini,$App->nowDateTime);
		$defaultdatascaend = DateFormat::checkDataTimeIso($App->item->datatimescaend,$App->nowDateTime);
		$App->defaultJavascript = "defaultdata = '".$defaultdatains."';";
		$App->defaultJavascript .= "defaultdata1 = '".$defaultdatascaini."';";
		$App->defaultJavascript .= "defaultdata2 = '".$defaultdatascaend."';";
		$App->css[] = '<link href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/items.css" rel="stylesheet">';		
		
	}	
?>