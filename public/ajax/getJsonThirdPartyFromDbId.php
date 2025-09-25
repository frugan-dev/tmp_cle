<?php
/* ajax/getJsonThirdPartyFromDbId.php v.1.3.0. 26/09/2020 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH','../');

include_once(PATH."include/configuration.inc.php");

// autoload by composer
//include_once(PATH."classes/class.Config.php");
//include_once(PATH."classes/class.Core.php");
//include_once(PATH."classes/class.Sessions.php");
//include_once(PATH."classes/class.Sql.php");
//include_once(PATH."classes/class.ToolsStrings.php");
//include_once(PATH."classes/class.SanitizeStrings.php");

//Core::setDebugMode(1);

/* lingua */
if ($globalSettings['default language'] != '') {
	if (file_exists(PATH."lang/".$globalSettings['default language'].".inc.php")) {
		include_once(PATH."lang/".$globalSettings['default language'].".inc.php");
	} else {
		include_once(PATH."lang/it.inc.php");
	}
} else {
	include_once(PATH."lang/it.inc.php");
}

setlocale(LC_TIME, 'ita', 'it_IT');

Config::setGlobalSettings($globalSettings);
Config::init();
Config::setLangVars($_lang);
Config::initDatabaseTables();
Core::init();

/* variabili globali */
$App = new stdClass;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;


if (!isset($_MY_SESSION_VARS['idUser'])){
	ToolsStrings::redirect(URL_SITE.'login');
}

$id  = 0;
if (isset($_GET['id'])) $id = intval($_GET['id']);
if (isset($_POST['id'])) $id = intval($_POST['id']);

$obj = new stdClass;	
if (intval($id) > 0) {		
	Sql::initQuery(DB_TABLE_PREFIX.'thirdparty',['*'],[$id],'id = ?');	
	$obj = Sql::getRecord();	
	if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
	if (!isset($obj->id) || (isset($obj->id) && $obj->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }	
	echo json_encode($obj);
}
die();
?>