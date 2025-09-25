<?php
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
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;


/* 
Controlla se un dato esiste già nella tabella->campo indicata
Parametri richiesti:
$table @string = la tabella della ricerca
$fieldid @string = il campo COUNT() della tabella della ricerca
$field @string = il campo della tabella della ricerca
$fieldsvalue @array = i valori per i campi della where e da ricercare
$matchtype @string = il controlllo da fare (=, like, ecc)
Risposta:
array(
    result => 1 = il dato esiste; 0 = il dato non esiste
    messagge => eventuale messaggio
)
*/

//Core::setDebugMode(1);
//ToolsStrings::dump($_POST);
//ToolsStrings::dump($_GET);

$table = '';
$fieldId = 'id';
$field = '';
$fieldLabel = '';
$fieldsValue = [];
$matchType = '=';
$customClause = '';
$andClause = '';
$foo = 0;
if ( isset($_REQUEST['table']) && $_REQUEST['table'] != '' ) $table = $_REQUEST['table'];
if ( isset($_REQUEST['fieldLabel']) && $_REQUEST['fieldLabel'] != '' ) $fieldLabel = $_REQUEST['fieldLabel'];
if ( isset($_REQUEST['fieldId']) && $_REQUEST['fieldId'] != '' ) $fieldId = $_REQUEST['fieldId'];
if ( isset($_REQUEST['field']) && $_REQUEST['field'] != '' ) $field = $_REQUEST['field'];
if ( isset($_REQUEST['fieldsValue']) && $_REQUEST['fieldsValue'] != '' ) $fieldsValue = $_REQUEST['fieldsValue'];
if ( isset($_REQUEST['matchType']) && $_REQUEST['matchType'] != '' ) $matchType = $_REQUEST['matchType'];
if ( isset($_REQUEST['customClause']) && $_REQUEST['customClause'] != '' ) $customClause = $_REQUEST['customClause'];
if ( isset($_REQUEST['andClause']) && $_REQUEST['andClause'] != '' ) $andClause = $_REQUEST['andClause'];
//echo '<br>table: '.$table;
//echo '<br>field: '.$field;
if ($table != '' && $field != '') {         
    $clause = $field . $matchType . '?';
    if ($customClause != '') $clause .= $andClause .'('.$customClause.')';   
    Config::$queryParams = [];
    Config::$queryParams['tables'] = $table;
    Config::$queryParams['keyRif'] = $fieldId;
    Config::$queryParams['whereClause'] = $clause;
    Config::$queryParams['fieldsValues'] = $fieldsValue;

    //ToolsStrings::dump(Config::$queryParams);

    $foo = Sql::checkIfRecordExists();
}

if ($fieldLabel == '') $fieldLabel = $field;
if ($foo > 0) {
    $data['result'] = '1';
    $data['message'] = preg_replace('/%ITEM%/',(string) $fieldLabel,(string) Config::$langVars['Il valore per il campo %ITEM% è già presente nel nostro database!']);
    
} else {
    $data['result'] = '0';
    $data['message'] = preg_replace('/%ITEM%/',(string) $fieldLabel,(string) Config::$langVars['Il valore per il campo %ITEM% è disponibile!']);
}
echo json_encode($data);

?>