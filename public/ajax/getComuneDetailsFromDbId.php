<?php

/* ajax/getComuneFromDbId.php v.4.5.1. 20/11/2018 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH', '../');

include_once(PATH.'include/configuration.inc.php');

// autoload by composer
//include_once(PATH."classes/class.Config.php");
//include_once(PATH."classes/class.Core.php");
//include_once(PATH."classes/class.Sessions.php");
//include_once(PATH."classes/class.Sql.php");
//include_once(PATH."classes/class.SanitizeStrings.php");
//include_once(PATH."classes/class.ToolsStrings.php");

//Core::setDebugMode(1);

setlocale(LC_TIME, 'ita', 'it_IT');

Config::setGlobalSettings($globalSettings);
Config::init();
Config::initDatabaseTables();
Core::init();

/* variabili globali */
$App = new stdClass();
define('DB_TABLE_PREFIX', Sql::getTablePrefix());

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME, SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;

$App->params = new stdClass();
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

$comuni_id = (isset($_POST['comuni_id']) ? intval($_POST['comuni_id']) : 6242);

$data = [];
if ($comuni_id > 0) {
    $where = 'id = ?';
    $f = ['*'];
    $fv = [$comuni_id];
    Sql::initQuery($App->params->tables['comuni'], $f, $fv, $where);
    $data = Sql::getRecord();
}

echo json_encode($data);
die();
