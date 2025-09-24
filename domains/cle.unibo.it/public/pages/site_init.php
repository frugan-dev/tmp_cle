<?php
/**
 * Framework Site PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * pages/site_init.php v.4.0.0. 05/11/2022
*/

//Sql::setDebugMode(1);

$App->pageActive = 'home';
$dataMainMenu = Menu::setMenuTreeData(['langUser'=>$_lang['user']]);
//ToolsStrings::dump($dataMainMenu);

// menu pages
$dataMenuPages = Pages::setMainTreePagesData([
	'table'=>'pages',
	'getbreadcrumbs'=>'1',
	'hideMenu' => 1
]);
//ToolsStrings::dump($dataMenuPages);die();
$App->pageAlias = Core::$request->page_alias;
$App->pageID = Core::$request->page_id;
if (Core::$request->page_alias != '') $App->pageActive = Core::$request->page_alias;
if (isset($dataMenuPages[Core::$request->page_alias]->breadcrumbs[0]['parent'])) {
	$App->pageActive = $dataMenuPages[Core::$request->page_alias]->breadcrumbs[0]['alias'];
	$App->pageID = $dataMenuPages[Core::$request->page_alias]->breadcrumbs[0]['parent'];
}

$dataMenuSitePages = Pages::setMainTreePagesDataCle([
	'table'=>'site_pages',
	'getbreadcrumbs'=>'1',
	'table template'=>'site_templates'
]);
//ToolsStrings::dump($dataMenuSitePages);


// lingue
$App->languagesBar = Utilities::generateLanguageBar($templateLanguagesBar,$_SESSION['lang']);
//ToolsStrings::dump($App->languagesBar );
//die();

/* gestione immagine top e bottom pagina */
$App->page_image_top =  UPLOAD_DIR.'pages/default/default-image-top-pages.png';
$App->page_image_top_bottom = UPLOAD_DIR.'pages/default/default-image-bottom-pages.png';

/* carica i dati di contatto per la home */
$App->config = new stdClass();
Sql::initQuery(Sql::getTablePrefix().'contacts_config',['*'],[],'id = 1');
$App->contact_config = Sql::getRecord();

//print_r($App->config);
$App->addPageJavascriptIniBody = "
	var gLatitude = ".$App->contact_config->map_latitude.";
	var gLongitude = ".$App->contact_config->map_longitude.";
	var gTitle = '".addslashes((string) Config::$globalSettings['azienda referente'])."';
";


// SEO
$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
$App->metaDescriptionPage = $globalSettings['meta tags page']['description'];
$App->metaKeywordsPage = $globalSettings['meta tags page']['keyword'];

$App->meta_og_url = '';
$App->meta_og_type = '';
$App->meta_og_title = '';
$App->meta_og_image = '';
$App->meta_og_description = '';

// BREADCRUMBS
$App->breadcrumbs = new stdClass();
$App->breadcrumbs->items = [];
$App->breadcrumbs->items[] = ['class'=>'breadcrumb-item','url'=>URL_SITE,'title'=>'Home'];

// gestione chhokie terze parti
$App->cookiesThirdyParts = false;
if (isset($_COOKIE[$globalSettings['cookiesterzeparti']]) && $_COOKIE[Config::$globalSettings['cookiesterzeparti']] == 1) $App->cookiesThirdyParts = true;

Sql::initQuery(DB_TABLE_PREFIX.'contacts_config',['*'],[],'');	
$App->moduleConfig = Sql::getRecord();
$App->urlprivacypolicypage = ToolsStrings:: parseHtmlContent($App->moduleConfig->url_privacy_page,[]);
$App->urlcookiepolicypage = URL_SITE.'pages/2/cookie-policy';

$App->view = '';
?>