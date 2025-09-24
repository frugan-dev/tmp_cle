<?php
/* home.php v.4.0.0. 22/12/2021 */

$App->moduleData = new stdClass();
$App->moduleConfig = new stdClass();

$App->moduleConfig->meta_title = Config::$langVars['intro section title'];
$App->moduleConfig->meta_description = Config::$langVars['intro section text'];
$App->moduleConfig->meta_keywords = '';

$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if (isset($App->moduleConfig->image_header) && $App->moduleConfig->image_header != '') $App->moduleData->imageheader = $App->moduleConfig->image_header;
if (isset($App->moduleConfig->org_image_header) && $App->moduleConfig->org_image_header != '') $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;


// preleva le sliders - modulo tbl_slides_home_rev

$layerTemplatedefault = array(
    'it' => '<div class="container"><div class="row"><div class="col-lg-8"><h1>%TITLE%</h1><p class="hero-text pr-5">%CONTENT%</p><div class="CTAs"><a href="%URL%" target="%TARGET%" class="btn btn-outline-light%URLCLASS%">'.Config::$langVars['Read More'].'</a></div></div></div></div>',
  
    'en' => '<div class="container"><div class="row"><div class="col-lg-8"><h1>%TITLE%</h1><p class="hero-text pr-5">%CONTENT%</p><div class="CTAs"><a href="%URL%" target="%TARGET%" class="btn btn-outline-light%URLCLASS%">'.Config::$langVars['Read More'].'</a></div></div></div></div>',
  
    'fr' => '<div class="container"><div class="row"><div class="col-lg-8"><h1>%TITLE%</h1><p class="hero-text pr-5">%CONTENT%</p><div class="CTAs"><a href="%URL%" target="%TARGET%" class="btn btn-outline-light%URLCLASS%">'.Config::$langVars['Read More'].'</a></div></div></div></div>',
  
    'el' => '<div class="container"><div class="row"><div class="col-lg-8"><h1>%TITLE%</h1><p class="hero-text pr-5">%CONTENT%</p><div class="CTAs"><a href="%URL%" target="%TARGET%" class="btn btn-outline-light%URLCLASS%">'.Config::$langVars['Read More'].'</a></div></div></div></div>',
  
);

Sql::initQuery(Config::$dbTablePrefix.'slides_home_rev',array('*'),array(),'active = 1',' ordering ASC');
$pdoObject = Sql::getPdoObjRecords();
$arr = array();
while ($row = $pdoObject->fetch()) {		
    $row->li_data  = preg_replace('/%TITLE%/',$row->title,$row->li_data);					
    $row->layers = array();
    Sql::initQuery(Config::$dbTablePrefix.'slides_home_rev_layers',array('*'),array($row->id),'slide_id = ? AND active = 1',' ordering ASC');
    $layers = Sql::getRecords();
    $arrL = array();

    $row->li_data = preg_replace('/%SLIDEIMAGE%/',UPLOAD_DIR.'slides-home-rev/'.$row->filename,$row->li_data);	
    
    // ipmosta l'immagine header come prima innagine delle slider
    if ($App->moduleData->imageheader == '') {
        $App->moduleData->imageheader = UPLOAD_DIR.'slides-home-rev/'.$row->filename;
        $App->moduleData->orgImageheader = $row->org_filename;
    }

    if (is_array($layers) && is_array($layers) && count($layers) > 0) {
        foreach ($layers AS $valueL) {	

            $valueL->title = MultiLanguage::getLocaleObjectValue($valueL,'title_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1));

            $valueL->content = MultiLanguage::getLocaleObjectValue($valueL,'content_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1));
            $valueL->contentNoP = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $valueL->content);

            $valueL->url = MultiLanguage::getLocaleObjectValue($valueL,'url_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
            $valueL->target = MultiLanguage::getLocaleObjectValue($valueL,'target_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
            if($valueL->target == '') $valueL->target = '_blank';
            
            $field = 'template_'.Config::$langVars['user'];
            $valueL->$field = '';
            if ($valueL->$field != '') {
                $valueL->template = MultiLanguage::getLocaleObjectValue($valueL,'template_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
            } else {
                $valueL->template = $layerTemplatedefault[Config::$langVars['user']];
            }
             				
            $valueL->template = preg_replace('/%TITLE%/',$valueL->title,$valueL->template);	
            $valueL->template = preg_replace('/%CONTENT%/',$valueL->contentNoP,$valueL->template);	
            $valueL->template = preg_replace('/%URL%/',$valueL->url,$valueL->template);
            $valueL->template = preg_replace('/%TARGET%/',$valueL->target,$valueL->template);   
            
            // nascond il link 
            // usa la classe '.d-none' di bootstrap 4 per nascondere l'elemento
            if ($valueL->url == '') {
                $valueL->template = preg_replace('/%URLCLASS%/',' d-none',$valueL->template);
            } else {
                $valueL->template = preg_replace('/%URLCLASS%/','',$valueL->template);
            }

            $arrL[] = $valueL;				
        }
    }			
    $row->layers = $arrL;						
    $arr[] = $row;
}
$App->homeSliders = $arr;
//ToolsStrings::dump($App->homeSliders);


// INFOBOX
$App->homeinfobox = array();
Sql::initQuery(DB_TABLE_PREFIX.'homeinfobox',array('*'),array(),'active = 1',' id ASC');
$pdoObject = Sql::getPdoObjRecords();
while ($row = $pdoObject->fetch()) {
    
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $row->content = Multilanguage::getLocaleObjectValue($row,'content_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 

    $row->content = preg_replace("/(<\s*\/?\s*\ba\b[^>]*\/?\s*>)/i", "", $row->content);

    $row->url = Multilanguage::getLocaleObjectValue($row,'url_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
    
    $target = Multilanguage::getLocaleObjectValue($row,'target_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>0)); 
    $row->target = ($target != '' ? $target : '_self' );
    
    if ($row->url == '') {
        $row->url = "javascript:void(0);";
        $row->target = '';
    }

    //ToolsStrings::dump($row);

    $App->homeinfobox[] = $row;		
}
//ToolsStrings::dump($App->homeinfobox);die();

//PARTNERS
$App->partners = array();
Sql::initQuery(DB_TABLE_PREFIX.'partners',array('*'),array(),'active = 1',' ordering ASC');
$pdoObject = Sql::getPdoObjRecords();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $row->target = ($row->target != '' ? $row->target : '_self' );
    $App->partners[] = $row;		
}
//ToolsStrings::dump($App->partners);

// SPONSOR
$App->sponsor = array();
Sql::initQuery(DB_TABLE_PREFIX.'sponsor',array('*'),array(),'active = 1',' ordering ASC');
$pdoObject = Sql::getPdoObjRecords();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $row->target = ($row->target != '' ? $row->target : '_self' );
    $App->sponsor[] = $row;		
}
//ToolsStrings::dump($App->partners);


//ToolsStrings::dump(Core::$request);

// NEWS
if (!isset($_SESSION['home_news_categories_id'])) {
    $_SESSION['home_news_categories_id'] = 0;
    //echo '<br>imposta sessione cat-id';
}
if (Core::$request->method == 'ncid' && Core::$request->param != '') {
    $_SESSION['home_news_categories_id'] = intval(Core::$request->param);
    //echo '<br>cambia sessione cat-id';
}
//echo '<br>cat_id'.$_SESSION['home_news_categories_id'];

if (!isset($_SESSION['home_news_page'])) {
    $_SESSION['home_news_page'] = 1;
   //echo '<br>imposta sessione news page';
}
if (Core::$request->method == 'ncpg' && Core::$request->param != '') {
    $_SESSION['home_news_page'] = intval(Core::$request->param);
    //echo '<br>cambia sessione news page';
}
//echo '<br>page'.$_SESSION['home_news_page'];

// categories news
$App->news_categories = array();
Sql::initQuery(DB_TABLE_PREFIX.'news_cat',array('*'),array(),'active = 1',' ordering ASC');
$pdoObject = Sql::getPdoObjRecords();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $App->news_categories[] = $row;		
}
//ToolsStrings::dump($App->news_categories);

// news
//Config::$debugMode = 1;
/*
$itemsForPage = 3;

$App->news = array();
Config::initQueryParams();
Config::$queryParams['tables'] = DB_TABLE_PREFIX.'news';
Config::$queryParams['fields'] = array('*');
Config::$queryParams['fieldsVal'] = array();
Config::$queryParams['where'] = 'active = 1';
Config::$queryParams['and'] = ' and ';
if ($_SESSION['home_news_categories_id'] > 0) {
    Config::$queryParams['where'] .=  Config::$queryParams['and'].'id_cat = ?';
    Config::$queryParams['fieldsVal'][] = $_SESSION['home_news_categories_id'];
    Config::$queryParams['and'] = ' and ';
}
Sql::initQuery(Config::$queryParams['tables'],Config::$queryParams['fields'],Config::$queryParams['fieldsVal'],Config::$queryParams['where'],' datatimeins DESC');
Sql::setPage($_SESSION['home_news_page']);
Sql::setItemsForPage($itemsForPage);	
Sql::setResultPaged(true);	
$pdoObject = Sql::getPdoObjRecords();
$App->news_pagination = Utilities::getPagination($_SESSION['home_news_page'],Sql::getTotalsItems(),$itemsForPage);
//ToolsStrings::dump($App->news_pagination); die();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $row->summary = Multilanguage::getLocaleObjectValue($row,'summary_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1)); 
    $row->dataformatted = DateFormat::getDateTimeIsoFormatString($row->datatimeins,'%DAY% %STRINGMONTH% %YEAR%',array()); 
    // preleva la categoria
    Sql::initQuery(DB_TABLE_PREFIX.'news_cat',array('*'),array($row->id_cat),'id = ?','');
    $foo = Sql::getRecord();
    $row->category = Multilanguage::getLocaleObjectValue($foo,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
    $App->news[] = $row;		
}
//ToolsStrings::dump($App->news); die();
*/

$App->css[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/css/home.css">';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/js/home.js"></script>';

// SEO 

$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
$App->metaDescriptionPage = $App->moduleConfig->meta_description;
$App->metaKeywordsPage = $App->moduleConfig->meta_keywords;

$App->meta_og_url = URL_SITE.Core::$request->action;
$App->meta_og_type = 'website';
$App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
$App->meta_og_image = '';
if ($App->moduleData->imageheader != '') $App->moduleData->imageheader;
$App->meta_og_description = $App->moduleConfig->meta_description;


// BREADCRUMBS
$App->breadcrumbs->title = '';
$App->breadcrumbs->items = array();

// messaggi 
$App->messagesCore =  Utilities::getHTMLMessagesCore(Core::$resultOp,array('template'=>$templateMessagesBar));
/*
echo '<br>bb'.$App->moduleData->imageheader;
echo '<br>aa'.$App->meta_og_image;
*/
?>