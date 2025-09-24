<?php
/* wscms/pages/index.php v.3.5.4. 05/06/2019 */

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

$App->defaultJavascript = "messages['Devi inserire un contenuto!'] = '".preg_replace('/%ITEM%/',$_lang['contenuto'],$_lang['Devi inserire un %ITEM%!'])."';";

switch(substr(Core::$request->method,-4,4)) {
	case 'Iimg':
		$App->sessionName = $App->sessionName.'-images';
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/item-images.php");	
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

	case 'Bimg':
		$App->sessionName = $App->sessionName.'-images';
		$App->params->tables['resources'] = $App->params->tableRif.'_blocks_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/block-images.php");	
	break;
	case 'Bfil':
		$App->sessionName = $App->sessionName.'-files';
		$App->params->tables['resources'] = $App->params->tableRif.'_blocks_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/block-files.php");	
	break;
	case 'Bvid':
		$App->sessionName = $App->sessionName.'-videos';
		$App->params->tables['resources'] = $App->params->tableRif.'_blocks_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_owner'=>'0'));
		$Module = new Module($App->params->tables['resources'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		$App->params->fields['resources']['filename']['required'] = false;
		$App->params->fields['resources']['org_filename']['required'] = false;
		$App->params->fields['resources']['code']['required'] = true;
		include_once(PATH.$App->pathApplications.Core::$request->action."/block-videos.php");	
	break;

	case 'Iblo':
		$App->sessionName = $App->sessionName.'-blocks';
		$App->params->tables['resources'] = $App->params->tableRif.'_blocks_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->params->tables['iblo'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/blocks.php");	
	break;

	case 'Page':
	case 'Item':
	default;
		//Core::setDebugMode(1);
		$App->sessionName = $App->sessionName;
		$App->params->tables['resources'] = $App->params->tableRif.'_resources';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'25','srcTab'=>''));
		$Module = new Module($App->params->tables['item'],Core::$request->action,$_MY_SESSION_VARS[$App->sessionName]);	
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");		

		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.js" type="text/javascript"></script>';
		$App->css[] = '<link rel="stylesheet" href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.css"/>';
	break;	
	}
	


?>
