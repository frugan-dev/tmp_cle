<?php
/* wscms/newsletter/index.php v.3.1.0. 10/01/2017 */

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.'applications/'.Core::$request->action."/config.inc.php");
include_once(PATH.'applications/'.Core::$request->action."/classes/module.class.php");

$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->applicationName = $App->params->applicationName;
$App->tableBaseName = $App->params->tableBaseName;

$App->templatesFolder = $App->params->templatesFolder;

$App->labels = $App->params->labels;

$App->tableIndCat = $App->params->appTableIndCat;
$App->tableInd = $App->params->appTableInd;
$App->tableRifCatInd = $App->params->appTableRifCatInd;
$App->tableNew = $App->params->appTableNew;
$App->tableConf = $App->params->appTableConf;
$App->tableIndInvio = $App->params->appTableIndInvio;
$App->tableNewCode = $App->params->appTableNewCode;

$App->fieldsIndCat = $App->params->fieldsIndCat;
$App->fieldsInd = $App->params->fieldsInd;
//$fieldsCatInd = $App->params->fieldsCatInd;
$App->fieldsNew = $App->params->fieldsNew;
//$fieldsConf = $App->params->fieldsConf;
//$fieldsIndInvio = $App->params->fieldsIndInvio;
$App->fieldsNewCode = $App->params->fieldsNewCode;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$App->defaultJavascript = '';

if (!isset($_SESSION['newsletter']['newsletter da inviare'])) $_SESSION['newsletter']['newsletter da inviare'] = '';
if (!isset($_SESSION['newsletter']['newsletter da inviare finale'])) $_SESSION['newsletter']['newsletter da inviare finale'] = '';

switch(Core::$request->method) {
	case 'listNewCode':
	case 'activeNewCode':
	case 'disactiveNewCode':
	case 'deleteNewCode':
	case 'newNewCode':
	case 'insertNewCode':
	case 'modifyNewCode':
	case 'updateNewCode':
	case 'pageNewCode':
	case 'previewNewCode':
		$App->sessionName = 'newsletter-code';
		$Module = new Module($App->sessionName,$App->tableNewCode);		
		include_once(PATH.'applications/'.Core::$request->action."/newsletter-code.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/newsletter-code.js"></script>';
	break;
	
	case 'listIndSos':
	case 'confirmIndSos':
	case 'deleteIndSos':
	case 'modifyIndSos':
	case 'updateIndSos':
	case 'pageIndSos':
	case 'confirmIndSos':
	case 'deleteOldIndSos':
		$App->sessionName = 'newsletter-indirizzi-sos';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module($App->sessionName,$App->tableInd);	
		include_once(PATH.'applications/'.Core::$request->action."/indirizzisos.php");
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/indirizzisos.js"></script>';	
	break;
	
	case 'listIndCat':
	case 'activeIndCat':
	case 'disactiveIndCat':
	case 'deleteIndCat':
	case 'newIndCat':
	case 'insertIndCat':
	case 'modifyIndCat':
	case 'updateIndCat':
	case 'pageIndCat':
	case 'messageIndCat':
		$App->sessionName = 'newsletter-indirizzi-cat';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module($App->sessionName,$App->tableInd);	
		include_once(PATH.'applications/'.Core::$request->action."/indirizzi-cat.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/indirizzi-cat.js"></script>';
	break;

	case 'expDBInd':
	case 'expCSVInd':
	case 'listInd':
	case 'activeInd':
	case 'disactiveInd':
	case 'deleteInd':
	case 'newInd':
	case 'insertInd':
	case 'modifyInd':
	case 'updateInd':
	case 'pageInd':
	case 'messageInd':
		$App->sessionName = 'newsletter-indirizzi';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module($App->sessionName,$App->tableInd);	
		include_once(PATH.'applications/'.Core::$request->action."/indirizzi.php");	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/indirizzi.js"></script>';
	break;
	
	case 'listInvio':
	case 'previewInvio':
	case 'ajaxGetListAddressTemp':
	case 'ajaxMoveAddressToSendList':
	case 'ajaxDeleteAddressToSendList';
		$App->sessionName = 'newsletter-invio';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1));
		$Module = new Module($App->sessionName,$App->tableInd);
		include_once(PATH.'applications/'.Core::$request->action."/invio.php");	
		$App->defaultJavascript = "var appFolder = '".Core::$request->action."';";
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/invio.js"></script>';
	break;
	
	case 'listInvioCat':
	case 'previewInvioCat':
	case 'ajaxGetListAddressCatTemp':
	case 'ajaxMoveAddressCatToSendList':
	case 'ajaxDeleteAddressCatToSendList';
		$App->sessionName = 'newsletter-inviocat';
		$_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1));
		$Module = new Module($App->sessionName,$App->tableInd);
		include_once(PATH.'applications/'.Core::$request->action."/invio-categories.php");	
		$App->defaultJavascript = "var appFolder = '".Core::$request->action."';";
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/invio-categories.js"></script>';
	break;

		
	case 'sendEmail':
	case 'ajaxUpdatePanel':
		include_once(PATH."classes/class.phpmailer.php");
		$App->sessionName = 'newsletter-invio-email';
		include_once(PATH.'applications/'.Core::$request->action."/invio-email.php");	
		$App->defaultJavascript = "var appFolder = '".Core::$request->action."';";
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/invio-email.js"></script>';
	break;
	
	case 'listConfig':
	case 'updateConfig':
		$App->sessionName = 'newsletter-config';
		include_once(PATH.'applications/'.Core::$request->action."/sendconfig.php");	
	break;
		
	default:	
	case 'listNew':
	case 'activeNew':
	case 'disactiveNew':
	case 'deleteNew':
	case 'newNew':
	case 'insertNew':
	case 'modifyNew':
	case 'updateNew':
	case 'pageNew':
	case 'previewNew':
	case 'previewNew1':
		$App->sessionName = 'newsletter';
		$Module = new Module($App->sessionName,$App->tableInd);		
		include_once(PATH.'applications/'.Core::$request->action."/newsletter.php");	
		$App->defaultJavascript .= "defaultdate = '".$defaultdateins."';";
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';

		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.js" type="text/javascript"></script>';
		$App->css[] = '<link rel="stylesheet" href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/tempusdominus/tempusdominus-bootstrap-4.min.css"/>';

		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/templates/'.$App->templateUser.'/js/newsletter.js"></script>';
	break;
}
switch(substr(Core::$request->method,-4,4)) {	
	case 'Conf':
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/config.php");
	break;						
}
?>
