<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *  ajax/getComuneFromDbId.php v.1.4.0. 08/02/20121
*/

error_reporting(E_ALL);
ini_set('display_errors', 0);
define('PATH','../');

include_once(PATH."include/configuration.inc.php");

// autoload by composer
//include_once(PATH."classes/class.Config.php");
//include_once(PATH."classes/class.Core.php");
//include_once(PATH."classes/class.Sessions.php");
//include_once(PATH."classes/class.Sql.php");
//include_once(PATH."classes/class.SanitizeStrings.php");
//include_once(PATH."classes/class.ToolsStrings.php");
//include_once(PATH."classes/class.Applications.php");

setlocale(LC_TIME, 'ita', 'it_IT');

//Sql::setDebugMode(1);

Config::setGlobalSettings($globalSettings);
Config::init();
Config::initDatabaseTables();
Core::init();

/* variabili globali */
$App = new stdClass;
$_lang = Config::$langVars;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;

$App->params = new stdClass;
$App->params->tables['users']  = DB_TABLE_PREFIX.'users';
$App->params->tables['levels'] = DB_TABLE_PREFIX.'levels';
$App->params->tables['caus'] = DB_TABLE_PREFIX.'users_categories';

$levels_id = ($_POST['levels_id'] ?? 10);
$users_categories_id = (isset($_POST['']) ? $_POST['users_categories_id'] : 3);
$levels_id_alias = '';

// trova levels_alias_id in base al livello
Sql::initQuery($App->params->tables['levels'],['*'],[$levels_id],'id = ?');
$foo = Sql::getRecord();
if (isset($foo->id_alias)) $levels_id_alias = $foo->id_alias;

// prelevo le categorie
/*
echo '$levels_id: '.$levels_id;
echo '$users_categories_id: '.$users_categories_id;
echo '$levels_id_alias:'.$levels_id_alias;
*/

//Core::setDebugMode(1);
$queryVars = Applications::resetDataTableArrayVars();
$queryVars['table'] = $App->params->tables['caus'];
$queryVars['fields'][] = '*';
$queryVars['fieldsValue'] = [];
if ($levels_id_alias != '') {
    $queryVars['where'] = 'levels_id_alias = ?';
    $queryVars['fieldsValue'][] = intval($levels_id_alias);
    $queryVars['and'] = ' AND ';
}
Sql::initQuery($queryVars['table'],$queryVars['fields'],$queryVars['fieldsValue'],$queryVars['where']);
$obj = Sql::getRecords();
echo json_encode($obj);
die();
?>