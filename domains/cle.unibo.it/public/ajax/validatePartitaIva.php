<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
define('PATH','../');

include_once(PATH."include/configuration.inc.php");
include_once(PATH."classes/class.Config.php");
include_once(PATH."classes/class.Core.php");
include_once(PATH."classes/class.Sessions.php");
include_once(PATH."classes/class.Sql.php");
include_once(PATH."classes/class.ToolsStrings.php");
include_once(PATH."classes/class.SanitizeStrings.php");
include_once(PATH."classes/class.Form.php");


setlocale(LC_TIME, 'ita', 'it_IT');

Config::setGlobalSettings($globalSettings);
Config::init();
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

$value = '';
$country = 'IT';

if ( isset($_REQUEST['value']) && $_REQUEST['value'] != '' ) $value = $_REQUEST['value'];
if ( isset($_REQUEST['country']) && $_REQUEST['country'] != '' ) $country = $_REQUEST['country'];

$data['result'] = '1';
$data['message'] = preg_replace('/%ITEM%/',Config::$langVars['partita IVA'],Config::$langVars['Il valore per il campo %ITEM% è già presente nel nostro database!']);
if ($value != '') {         
    list( $data['result'],$data['message'] ) = Form::validateVAT($value,$country);
}
echo json_encode($data);
?>