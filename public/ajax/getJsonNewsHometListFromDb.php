<?php

/* ajax/getJsonNewsHometListFromDb.php v.4.0.0. 20/11/2018 */
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH', '../');

include_once(PATH.'wscms/include/configuration.inc.php');

// autoload by composer
//include_once(PATH."wscms/classes/class.Config.php");
//include_once(PATH."wscms/classes/class.Core.php");
//include_once(PATH."wscms/classes/class.Sessions.php");
//include_once(PATH."wscms/classes/class.Sql.php");
//include_once(PATH."wscms/classes/class.SanitizeStrings.php");
//include_once(PATH."wscms/classes/class.Permissions.php");
//include_once(PATH."wscms/classes/class.Utilities.php");
//include_once(PATH."wscms/classes/class.ToolsStrings.php");
//include_once(PATH."wscms/classes/class.Multilanguage.php");
//include_once(PATH."wscms/classes/class.DateFormat.php");

Core::setDebugMode(1);

Config::setGlobalSettings($globalSettings);
Config::init();
Config::$defPath = '../wscms/';

/* variabili globali */
$App = new stdClass();
define('DB_TABLE_PREFIX', Sql::getTablePrefix());

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME, SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;

$lang = 'fr';
if (isset($_REQUEST['lang'])) {
    $lang = $_REQUEST['lang'];
}

//echo '<br>lingua :'.$lang;
Config::loadLanguageVars($_SESSION['lang']);
setlocale(LC_TIME, Config::$langVars['lista lingue abbreviate'][Config::$langVars['user']], Config::$langVars['charset date']);
$_lang = Config::$langVars;

setlocale(LC_TIME, 'ita', 'it_IT');
Config::setLangVars($_lang);
Config::initDatabaseTables('../');

//$data = array();

$home_news_categories_id = 0;
$home_news_page = 1;
$itemsForPage = 3;

if (isset($_REQUEST['categories_id'])) {
    $home_news_categories_id = intval($_REQUEST['categories_id']);
}
if (isset($_REQUEST['page'])) {
    $home_news_page = intval($_REQUEST['page']);
}

//_SESSION['home_news_page']

$news = [];
Config::initQueryParams();
Config::$queryParams['tables'] = DB_TABLE_PREFIX.'news';
Config::$queryParams['fields'] = ['*'];
Config::$queryParams['fieldsVal'] = [];
Config::$queryParams['where'] = 'active = 1';
Config::$queryParams['and'] = ' and ';
/*
if ($_SESSION['home_news_categories_id'] > 0) {
    Config::$queryParams['where'] .=  Config::$queryParams['and'].'id_cat = ?';
    Config::$queryParams['fieldsVal'][] = $_SESSION['home_news_categories_id'];
    Config::$queryParams['and'] = ' and ';
}
*/
Sql::initQuery(Config::$queryParams['tables'], Config::$queryParams['fields'], Config::$queryParams['fieldsVal'], Config::$queryParams['where'], ' datatimeins DESC');
Sql::setPage($_SESSION['home_news_page']);
Sql::setItemsForPage($itemsForPage);
Sql::setResultPaged(true);
$pdoObject = Sql::getPdoObjRecords();
$news_pagination = Utilities::getPagination($home_news_page, Sql::getTotalsItems(), $itemsForPage);
//ToolsStrings::dump($App->news_pagination); die();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row, 'title_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
    $row->summary = Multilanguage::getLocaleObjectValue($row, 'summary_', Config::$langVars['user'], ['htmLawed' => 1,'parse' => 1]);
    $row->dataformatted = DateFormat::getDateTimeIsoFormatString($row->datatimeins, '%DAY% %STRINGMONTH% %YEAR%', []);
    // preleva la categoria
    Sql::initQuery(DB_TABLE_PREFIX.'news_cat', ['*'], [$row->id_cat], 'id = ?', '');
    $foo = Sql::getRecord();
    $row->category = Multilanguage::getLocaleObjectValue($foo, 'title_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
    $news[] = $row;
}
//ToolsStrings::dump($news); die();

$data = $news;

echo json_encode($data);
die();
