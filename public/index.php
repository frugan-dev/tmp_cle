<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * index.php v.4.0.0. 22/10/2021
*/

header('Access-Control-Allow-Origin: *');
session_start();
if (!isset($_SESSION['csrftoken'])) $_SESSION['csrftoken'] = bin2hex(openssl_random_pseudo_bytes(64));
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

define('PATH','');
define('MAXPATH', str_replace("includes","",__DIR__).'');
if(!ini_get('date.timezone')) date_default_timezone_set('GMT');
setlocale(LC_TIME, 'ita', 'it_IT');

include_once(PATH."wscms/include/configuration.inc.php");

// autoload by composer
//include_once(PATH."wscms/classes/class.ToolsUpload.php");
//include_once(PATH."wscms/classes/class.ToolsDownload.php");
//include_once(PATH."wscms/classes/class.Sql.php");
//include_once(PATH."wscms/classes/class.Utilities.php");
//include_once(PATH."wscms/classes/class.DateFormat.php");
//include_once(PATH."wscms/classes/class.Subcategories.php");
//include_once(PATH."wscms/classes/class.Categories.php");
//include_once(PATH."wscms/classes/class.Products.php");
//include_once(PATH."wscms/classes/class.Menu.php");
//include_once(PATH."wscms/classes/class.Form.php");
//include_once(PATH."wscms/classes/class.Mails.php");
//include_once(PATH."wscms/classes/class.Modules.php");
//include_once(PATH."wscms/classes/class.Custom.php");
//include_once(PATH."wscms/classes/class.Pages.php");
//include_once(PATH."wscms/classes/class.Multilanguage.php");
//include_once(PATH."wscms/classes/class.Carts.php");
//include_once(PATH."wscms/classes/class.Orders.php");

Config::setGlobalSettings($globalSettings);
Config::init();
Config::$defPath = 'wscms/';
Core::$globalSettings['requestoption']['coremodules'] = ['moduleassociated','login','logout','account','password','profile','nopassword','nousername','moduleassociated','error'];
Core::$globalSettings['requestoption']['othermodules'] = array_merge(['listpage','help'],Core::$globalSettings['requestoption']['coremodules']);
Core::$globalSettings['requestoption']['defaultaction'] = 'home';
Core::$globalSettings['requestoption']['defaultpagesmodule'] = 'pages';
Core::$globalSettings['requestoption']['sectionadmin'] = 1;
Core::$globalSettings['requestoption']['methods'] = [];
Core::$globalSettings['requestoption']['isRoot'] = 0;
Core::$globalSettings['requestoption']['getlasturlparam'] = false;
Core::init();

//Sql::setDebugMode(1);

// variabili globali
$App = new stdClass;
$_lang = Config::$langVars;

define('DB_TABLE_PREFIX',Sql::getTablePrefix());
$App->templateBase = 'struttura';
$renderTpl = true;
$renderAjax = false;
$App->templateApp = '';
$App->pathApplications = '';
$App->pathApplicationsCore = '';
$App->globalSettings = $globalSettings;
$App->breadcrumb = '';
$App->metaTitlePage = SITE_NAME.' v.'.CODE_VERSION;
$App->metaDescriptionPage = $globalSettings['meta tags page']['description'];
$App->metaKeywordsPage = $globalSettings['meta tags page']['keyword'];

// gestisce la richiesta http parametri get
//ToolsStrings::dump(Core::$globalSettings['requestoption']);
Core::getRequest();
//ToolsStrings::dump(Core::$request);

/* avvio sessione */

$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;


// lingua
//echo '<br>lingua :'.$_SESSION['lang'];
Config::loadLanguageVars($_SESSION['lang']);
setlocale(LC_TIME,Config::$langVars['lista lingue abbreviate'][Config::$langVars['user']], Config::$langVars['charset date']);
$_lang = Config::$langVars;
//ToolsStrings::dump(Config::$globalSettings);
//ToolsStrings::dump(Config::$langVars);


Config::initDatabaseTables();

// caricla l'eleco delle tabelle database
$App->tablesOfDatabase = Sql::getTablesDatabase($globalSettings['database'][DATABASE]['name']);


/* templates */
$App->templateUser = Core::$request->templateUser;
// templates
$App->templateUser = Config::$globalSettings['requestoption']['defaulttemplate'];



/* INCLUDE PAGES PHP */
/* carica la configurazione del singolo template */
if (file_exists(PATH."templates/".$App->templateUser."/configuration.inc.php")) include_once(PATH."templates/".$App->templateUser."/configuration.inc.php");

/* aggiorna la config */
Config::setGlobalSettings($globalSettings);

if (file_exists(PATH."pages/site_init.php")) include_once(PATH."pages/site_init.php");

switch(Core::$request->action) {
	default:		
		if (file_exists(PATH."pages/".Core::$request->action.".php")) {
			$App->templateApp = Core::$request->action;
			include_once(PATH."pages/".Core::$request->action.".php");		
		} else {	
			Core::$request->action = 'error';
			Core::$request->method = '404';
			include_once(PATH."pages/error.php");			
		}					
	break;	
}
/* END INCLUDE PAGES PHP */

if ($renderAjax == true){	
	if (file_exists(PATH."templates/".$App->templateUser."/".Core::$request->action.".html")) {
		include_once(PATH."templates/".$App->templateUser."/".Core::$request->action.".html");	
	}
	$renderTlp = false;
}		

$App->lang = $_lang;
$App->mySessionVars = $_MY_SESSION_VARS;
$App->globalSettings = $globalSettings;

/* genera il template */

$pathtemplateBase = PATH_SITE."templates/".$App->templateUser;
$pathtemplateApp = $App->pathApplications;


$App->templateApp .= '.html';
$App->templateBase .= '.html';

//echo PATH."templates/".$App->templateUser.'/'.$App->templateApp;
/* controlla se esite il template */
if (!file_exists(PATH."templates/".$App->templateUser.'/'.$App->templateApp)) {
	Core::$request->action = 'error';
	Core::$request->method = '404';
	include_once(PATH."pages/error.php");
	$App->templateApp = "error.html";
	//echo 'template NON esiste';
} else {

	//echo 'template esiste';
}


if (file_exists(PATH."pages/site_main.php")) include_once(PATH."pages/site_main.php");

/*
echo '<br>App->templateApp: '.$App->templateApp;
echo '<br>pathtemplateApp: '.$pathtemplateApp;
echo '<br>pathtemplateBase: '.$pathtemplateBase;
echo '<br>pathtemplateApp: '.$pathtemplateApp;
*/


if ($renderTpl == true && $App->templateApp != '') {

	$arrayVars = [
		'App'=>$App,
		'Lang'=>Config::$langVars,
		'LangVars'=>Config::$langVars,
		'URLSITE'=>URL_SITE,
		'URLSITEADMIN'=>URL_SITE_ADMIN,
		'PATHSITE'=>URL_SITE,
		'PATHSITEADMIN'=>PATH_SITE_ADMIN,
		'UPLOADDIR'=>UPLOAD_DIR,
		'CoreRequest'=>Core::$request,
		'CoreResultOp'=>Core::$resultOp,
		'ResultOp'=>Config::$resultOp,
		'MySessionVars'=>$_MY_SESSION_VARS,
		'Session'   => $_SESSION,
		'GlobalSettings'=>$globalSettings
	];

	$loader = new \Twig\Loader\FilesystemLoader($pathtemplateBase);
	$loader->addPath($pathtemplateApp);
	$twig = new \Twig\Environment($loader, [
		//'cache' => PATH_UPLOAD_DIR.'compilation_cache',
		'autoescape'=>false,
		'debug' => true
	]);

	$twig->addExtension(new \Twig\Extension\DebugExtension());
	$template = $twig->load('strutture/'.$App->templateBase);
	echo $template->render($arrayVars);

	} else { if ($renderAjax != true) echo 'No templateApp found!';}

if ($renderAjax == true){
	if (file_exists($pathApplications.$App->templateApp)) {
		include_once($pathApplications.$App->templateApp);
	}
}

//ToolsStrings::dump($_SESSION);
//die();
?>
