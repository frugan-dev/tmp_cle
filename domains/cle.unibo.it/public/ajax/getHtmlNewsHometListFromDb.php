<?php
/* ajax/getHtmlNewsHometListFromDb.php v.4.0.0. 20/11/2018 */
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH','../');

include_once(PATH."wscms/include/configuration.inc.php");
include_once(PATH."wscms/classes/class.Config.php");
include_once(PATH."wscms/classes/class.Core.php");
include_once(PATH."wscms/classes/class.Sessions.php");
include_once(PATH."wscms/classes/class.Sql.php");
include_once(PATH."wscms/classes/class.SanitizeStrings.php");
include_once(PATH."wscms/classes/class.Permissions.php");
include_once(PATH."wscms/classes/class.Utilities.php");
include_once(PATH."wscms/classes/class.ToolsStrings.php");
include_once(PATH."wscms/classes/class.Multilanguage.php");
include_once(PATH."wscms/classes/class.DateFormat.php");

Core::setDebugMode(1);

Config::setGlobalSettings($globalSettings);
Config::init();
Config::$defPath = '../wscms/';

/* variabili globali */
$App = new stdClass;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = [];
$_MY_SESSION_VARS = $my_session->my_session_read();
$App->mySessionVars = $_MY_SESSION_VARS;


$lang = 'fr';
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang'];

//echo '<br>lingua :'.$lang;
Config::loadLanguageVars($_SESSION['lang']);
setlocale(LC_TIME,Config::$langVars['lista lingue abbreviate'][Config::$langVars['user']], Config::$langVars['charset date']);
setlocale(LC_TIME, 'ita', 'it_IT');
Config::initDatabaseTables('../');

$home_news_categories_id = 0;
$home_news_page = 1;
$itemsForPage = 3;

if (isset($_REQUEST['categories_id'])) $home_news_categories_id = intval($_REQUEST['categories_id']);
if (isset($_REQUEST['page'])) $home_news_page = intval($_REQUEST['page']);

$news = [];
Config::initQueryParams();
Config::$queryParams['tables'] = DB_TABLE_PREFIX.'news';
Config::$queryParams['fields'] = ['*'];
Config::$queryParams['fieldsVal'] = [];
Config::$queryParams['where'] = 'active = 1';
Config::$queryParams['and'] = ' and ';

if ($home_news_categories_id > 0) {
    Config::$queryParams['where'] .=  Config::$queryParams['and'].'id_cat = ?';
    Config::$queryParams['fieldsVal'][] = $home_news_categories_id;
    Config::$queryParams['and'] = ' and ';
}

Sql::initQuery(Config::$queryParams['tables'],Config::$queryParams['fields'],Config::$queryParams['fieldsVal'],Config::$queryParams['where'],' datatimeins DESC');
Sql::setPage($home_news_page);
Sql::setItemsForPage($itemsForPage);	
Sql::setResultPaged(true);	
$pdoObject = Sql::getPdoObjRecords();
$news_pagination = Utilities::getPagination($home_news_page,Sql::getTotalsItems(),$itemsForPage);
//ToolsStrings::dump($App->news_pagination); die();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
    $row->summary = Multilanguage::getLocaleObjectValue($row,'summary_',Config::$langVars['user'],['htmLawed'=>1,'parse'=>1]); 
    $row->dataformatted = DateFormat::getDateTimeIsoFormatString($row->datatimeins,'%DAY% %STRINGMONTH% %YEAR%',[]); 
    // preleva la categoria
    Sql::initQuery(DB_TABLE_PREFIX.'news_cat',['*'],[$row->id_cat],'id = ?','');
    $foo = Sql::getRecord();
    $row->category = Multilanguage::getLocaleObjectValue($foo,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 

    // modifica dati embedded
    $row->video = '';
    if (isset($row->embedded) && $row->embedded != '') {
        $row->video = $row->embedded;
        $row->video = preg_replace('/(width)="\d*"\s/',"",(string) $row->video);
        $row->video = preg_replace('/(height)="\d*"\s/',"",$row->video);
        $row->video = preg_replace('/iframe/','iframe width="100%" height="100%"',$row->video);

    }

    $news[] = $row;		
}
//ToolsStrings::dump($news[0]); die();

$html = ' <div class="row">';

if (is_array($news) && count($news) > 0) {
    foreach ($news As $value) {
        $html .= '<div class="col-lg-4"><div class="blog-post">';
        
        if ($value->filename != '') {
            $html .= '<div class="image text-center"><img src="'.UPLOAD_DIR.'news/'.$value->filename.'" alt="'.$value->org_filename.'">';
        } else if ($value->video != '') {
            $html .= '<div class="image text-center">'.$value->video;
        } else {
            $html .= '<div class="image text-center"><img src="'.UPLOAD_DIR.'default/image.png" alt="'.$value->title.'">';
        }
        
        if ($value->video == '') {
            $html .= '<div class="overlay d-flex align-items-center justify-content-center"><a href="'.URL_SITE.'news/dt/'.$value->id.'" title="'.$value->title.'" class="btn btn-outline-light">'.Config::$langVars['Read More'].'</a></div>';
        }
        $html .= '</div>';
        $html .= '<div class="text"><a href="'.URL_SITE.'news/dt/'.$value->id.'" title="'.$value->title.'">
        <h4 class="text-this">'.$value->title.'</h4>
        </a>';
        $html .= '<ul class="post-meta list-inline">
        <li class="list-inline-item"><i class="icon-clock-1"></i> '.$value->dataformatted.'</li>
        <li class="list-inline-item"><i class="icon-chat"></i>'.$value->category.'</li>
        </ul>';
        $html .= '<p>'.$value->summary.'</p>';
        $html .= '</div></div></div>';
    }   
}

$html .= '</div>';

$html .= '<nav aria-label="Page navigation example" class="d-flex justify-content-center">
<ul class="pagination">';


$html .= '<li class="page-item">
<a data-rif="'.$news_pagination->itemPrevious.'" href="javascript:void(0);" 
title="'.Config::$langVars['precedente'].'" aria-label="Previous" class="setHomeNewsPage page-link"><span aria-hidden="true">«</span><span class="sr-only">'.Config::$langVars['precedente'].'</span></a>
</li>';

if (is_array($news_pagination->pagePrevious) && count($news_pagination->pagePrevious) >0) {
    foreach($news_pagination->pagePrevious AS $value) {
        $label = preg_replace('/%ITEM%/',(string) $value,(string) Config::$langVars['vai alla pagina %ITEM%']);
        $html .= '<li class="page-item"><a data-rif="'.$value.'" class="setHomeNewsPage page-link" title="'.$label.'" href="javascript:void(0);">'.$value.'</a>
        </li>';
    }
}

$html .= '<li class="page-item active"><a class="page-link" href="javascript:void(0);" title="'.Config::$langVars['pagina'].' '.$news_pagination->page.'">'.$news_pagination->page.'</a></li>';

if (is_array($news_pagination->pageNext) && count($news_pagination->pageNext) >0) {
    foreach($news_pagination->pageNext AS $value) {
        $label = preg_replace('/%ITEM%/',(string) $value,(string) Config::$langVars['vai alla pagina %ITEM%']);
        $html .= '<li class="page-item"><a data-rif="'.$value.'" class="setHomeNewsPage page-link" title="'.$label.'" href="javascript:void(0);">'.$value.'</a>
        </li>';
    }
}

$html .= '<li class="page-item">
<a data-rif="'.$news_pagination->itemNext.'" href="javascript:void(0);" title="'.Config::$langVars['successiva'].'" aria-label="Next" class="setHomeNewsPage page-link"><span aria-hidden="true">»</span><span class="sr-only">'.Config::$langVars['successiva'].'</span></a>
</li>';



$html .= '</ul>
</nav>';



echo $html;
die();
?>