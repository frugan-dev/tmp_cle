<?php
/* ajax/getComuneFromDbId.php v.4.5.1. 20/11/2018 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH','../');

include_once(PATH."include/configuration.inc.php");
include_once(PATH."classes/class.Config.php");
include_once(PATH."classes/class.Core.php");
include_once(PATH."classes/class.Sessions.php");
include_once(PATH."classes/class.Sql.php");
include_once(PATH."classes/class.SanitizeStrings.php");

Core::setDebugMode(1);

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
$_MY_SESSION_VARS = array();
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;


$App->params->tables['products'] = DB_TABLE_PREFIX.'products';

$productsArray = array();
$q = $_POST['q']; //This is the textbox value
if ($q != '') {
    Sql::initQuery($App->params->tables['products'],array('id,title'),array('%'.$q.'%'),'title LIKE ? AND active = 1');
    $pdoObject = Sql::getPdoObjRecords();
    while ($row = $pdoObject->fetch()) {
            $productsArray[] = array(
                'title'=>$row->title,
                'id'=>$row->id
            );
    }		
}
echo json_encode($productsArray);
die();
?>