<?php

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * wscms/index.php v.4.0.0. 22/10/2021
*/
session_start();
/*
error_reporting(E_ALL);
ini_set('display_errors',1);
*/

define('PATH', '');
define('MAXPATH', str_replace('includes', '', __DIR__).'');
if (!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}
setlocale(LC_TIME, 'ita', 'it_IT');

if (!isset($_SESSION['csrftoken'])) {
    $_SESSION['csrftoken'] = bin2hex(openssl_random_pseudo_bytes(64));
}

include_once(PATH.'include/configuration.inc.php');

// autoload by composer
//include_once(PATH."classes/class.ToolsUpload.php");
//include_once(PATH."classes/class.ToolsDownload.php");
//include_once(PATH."classes/class.Sql.php");
//include_once(PATH."classes/class.SqlCle.php");
//include_once(PATH."classes/class.Utilities.php");
//include_once(PATH."classes/class.DateFormat.php");
//include_once(PATH."classes/class.Subcategories.php");
//include_once(PATH."classes/class.Categories.php");
//include_once(PATH."classes/class.CategoriesCle.php");
//include_once(PATH."classes/class.Products.php");
//include_once(PATH."classes/class.Menu.php");
//include_once(PATH."classes/class.Form.php");
//include_once(PATH."classes/class.Mails.php");
//include_once(PATH."classes/class.Modules.php");
//include_once(PATH."classes/class.Custom.php");
//include_once(PATH."classes/class.Carts.php");
//include_once(PATH."classes/class.Orders.php");

setlocale(LC_TIME, 'ita', 'it_IT');

Config::setGlobalSettings($globalSettings);
Config::init();

Core::$globalSettings['requestoption']['coremodules'] = ['moduleassociated','login','logout','account','password','profile','nopassword','nousername','moduleassociated','error'];
Core::$globalSettings['requestoption']['othermodules'] = array_merge(['help'], Core::$globalSettings['requestoption']['coremodules']);
Core::$globalSettings['requestoption']['defaultaction'] = 'home';
Core::$globalSettings['requestoption']['defaultpagesmodule'] = 'home';
Core::$globalSettings['requestoption']['sectionadmin'] = 1;
Core::$globalSettings['requestoption']['methods'] = [];
Core::$globalSettings['requestoption']['isRoot'] = 0;
Core::$globalSettings['requestoption']['getlasturlparam'] = false;

Config::$defPath = '';
Core::init();

$CategoriesCle = new CategoriesCle();

// variabili globali
$App = new stdClass();
$_lang = Config::$langVars;

define('DB_TABLE_PREFIX', Sql::getTablePrefix());
$App->templateBase = 'struttura.html';
$renderTpl = true;
$renderAjax = false;
$App->templateApp = '';
$App->pathApplications = 'applications/';
$App->pathApplicationsCore = 'applications/core/';
$App->globalSettings = $globalSettings;
$App->breadcrumb = '';
$App->metaTitlePage = SITE_NAME.' v.'.CODE_VERSION;
$App->metaDescriptionPage = $globalSettings['meta tags page']['description'];
$App->metaKeywordsPage = $globalSettings['meta tags page']['keyword'];

//Sql::setDebugMode(1);

// avvio sessione
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME, AD_SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;

// carica dati utente loggato
$App->userLoggedData = new stdClass();
if (isset($_MY_SESSION_VARS['idUser'])) {
    Sql::initQuery(DB_TABLE_PREFIX.'users', ['*'], [$_MY_SESSION_VARS['idUser']], 'active = 1 AND id = ?', '');
    $App->userLoggedData = Sql::getRecord();
    if (Core::$resultOp->error == 1) {
        die('Errore db utenti!');
    }
    if (isset($App->userLoggedData->is_root)) {
        $App->userLoggedData->is_root = intval($App->userLoggedData->is_root);
    }
}

Core::getRequest();
//ToolsStrings::dump(Config::$modules);
//ToolsStrings::dump(Config::$userModules);
//ToolsStrings::dump(Config::$userLevels);

//ToolsStrings::dump(Core::$request);
//die();

// lingua
Config::$langVars['user'] = 'it';
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'it';
}
// forza italiano per sistemi non multilingua
$_SESSION['lang'] = 'it';

//echo '<br>Core::$request->lang: '.Core::$request->lang;
//echo '<br>lingua :'.$_SESSION['lang'];
Config::loadLanguageVarsAdmin($_SESSION['lang']);
setlocale(LC_TIME, Config::$langVars['lista lingue abbreviate'][Config::$langVars['user']], Config::$langVars['charset date']);
$_lang = Config::$langVars;
//ToolsStrings::dump(Config::$globalSettings);

Config::initDatabaseTables('../');

// caricla l'eleco delle tabelle database
$App->tablesOfDatabase = Sql::getTablesDatabase($globalSettings['database'][DATABASE]['name']);

if (!isset($_MY_SESSION_VARS['idUser'])) {
    if (
        Core::$request->action != 'confirmaccount'
        && Core::$request->action != 'registeragente'
        && Core::$request->action != 'registercliente'
        && Core::$request->action != 'registerfornitore'
        && Core::$request->action != 'nopassword'
        && Core::$request->action != 'nousername') {
        Core::$request->action = 'login';
    }
}

// legge i permessi moduli
$App->user_modules_active = Permissions::getUserLevelModulesRights($App->userLoggedData);
//ToolsStrings::dumpArray($App->user_modules_active);

// controlla permessi per accesso modulo
$App->user_first_module_active = Core::$globalSettings['requestoption']['defaultaction'];
$App->user_module_active = Core::$globalSettings['requestoption']['defaultaction'];
if (Permissions::checkIfModulesIsReadable(Core::$request->action, $App->userLoggedData, ['chackTable' => false]) == false) {
    Core::$request->action = $App->user_first_module_active;
}

//echo '<br>Core::$request->action: '.Core::$request->action;

$App->templateUser = Core::$globalSettings['requestoption']['defaulttemplate'];
$App->user_module_active = Core::$globalSettings['requestoption']['defaultaction'];

// carica i livelli
/*
$App->user_levels = Permissions::getUserLevels();
if (Core::$resultOp->error == 1) die('Errore db livello utenti!');
if (isset($App->userLoggedData->levels_id)) {
    $App->userLoggedData->labelRole = Permissions::getUserLevelLabel($App->user_levels,$App->userLoggedData->levels_id,$App->userLoggedData->is_root);
}

// carica i moduli
$App->userModules = Permissions::getUserModules();
//ToolsStrings::dump($App->userModules);

//die();

// carica i permessi moduli
$App->modules_access = Permissions::getUserLevelModulesRights($App->userLoggedData);
*/
// controlla se il modulo action è leggibile
$App->user_first_module_active = Core::$globalSettings['requestoption']['defaultaction'];
if (Permissions::checkIfModulesIsReadable(Core::$request->action, $App->userLoggedData, ['chackTable' => false]) == false) {
    Core::$request->action = $App->user_first_module_active;
}

//echo '<br>Core::$request->action: '.Core::$request->action; //die('fatto');

//die('fatto');

$pathApplications = $App->pathApplications;
$action = Core::$request->action;
$index = '/index.php';
$App->coreModule = false;

if (in_array(Core::$request->action, Core::$globalSettings['requestoption']['coremodules']) == true) {
    $App->coreModule = true;
    $pathApplications = $App->pathApplicationsCore;
    $action = '';
    $index = Core::$request->action.'.php';
}

/*
echo '<br>$pathApplications: '.$pathApplications;
echo '<br>$action: '.$action;
echo '<br>$index: '.$index;
*/

if (file_exists(PATH.'iniapp.php')) {
    include_once(PATH.'iniapp.php');
}

if (file_exists(PATH.$pathApplications.$action.$index)) {
    include_once(PATH.$pathApplications.$action.$index);
} else {
    Core::$request->action = $App->user_first_module_active;
    include_once(PATH.$pathApplications.$App->user_first_module_active.'/index.php');
}

if (file_exists(PATH.'endapp.php')) {
    include_once(PATH.'endapp.php');
}
//die('fatto');
if ($App->coreModule == true) {
    $pathtemplateApp = PATH.$pathApplications .= 'templates/'.$App->templateUser.'/';
} else {
    if ($App->templateApp != '') {
        $App->templateApp = Core::$request->action.'/templates/'.$App->templateUser.'/'.$App->templateApp;
    }
}

$pathtemplateBase = PATH_SITE_ADMIN.'templates/'.$App->templateUser;
$pathtemplateApp = $pathApplications;

/*
echo '<br>pathtemplateBase: '.$pathtemplateBase;
echo '<br>pathApplications: '.$pathApplications;
echo '<br>App->templateApp: '.$App->templateApp;
*/
/* genera il template */
if ($renderTpl == true && $App->templateApp != '') {

    $arrayVars = [
        'App' => $App,
        'LangVars' => Config::$langVars,
        'Lang' => Config::$langVars,
        'URLSITE' => URL_SITE,
        'URLSITEADMIN' => URL_SITE_ADMIN,
        'PATHSITE' => URL_SITE,
        'PATHSITEADMIN' => PATH_SITE_ADMIN,
        'UPLOADDIR' => UPLOAD_DIR,
        'CoreRequest' => Core::$request,
        'CoreResultOp' => Core::$resultOp,
        'ResultOp' => Config::$resultOp,
        'MySessionVars' => $_MY_SESSION_VARS,
        'Session'   => $_SESSION,
        'GlobalSettings' => $globalSettings,
    ];

    $loader = new FilesystemLoader($pathtemplateBase);
    $loader->addPath($pathtemplateApp);
    $twig = new Environment($loader, [
        //'cache' => PATH_UPLOAD_DIR.'compilation_cache',
        'autoescape' => false,
        'debug' => true,
    ]);

    $twig->addExtension(new DebugExtension());
    $template = $twig->load($App->templateBase);
    echo $template->render($arrayVars);

} else {
    if ($renderAjax != true) {
        echo 'No templateApp found!';
    }
}

if ($renderAjax == true) {
    if (file_exists($pathApplications.$App->templateApp)) {
        include_once($pathApplications.$App->templateApp);
    }
}

/*
ToolsStrings::dump($_SESSION);
ToolsStrings::dump($_MY_SESSION_VARS);
*/
