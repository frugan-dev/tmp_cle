<?php
/* team.php v.3.5.4. 24/07/2019 */

$App->moduleData = new stdClass();

// preleva configurazione modulo
Sql::initQuery(DB_TABLE_PREFIX.'faq_config',array('*'),array(),'');	
$App->moduleConfig = Sql::getRecord();
$App->moduleConfig->title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
$App->moduleConfig->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig,'text_intro_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
$App->moduleConfig->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'page_content_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 

$App->moduleConfig->meta_title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
$App->moduleConfig->meta_description = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_description_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
$App->moduleConfig->meta_keywords = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_keywords_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
//ToolsStrings::dump($App->moduleConfig);

// gestione titolo
$App->moduleData->title = 'F.A.Q.';
if ($App->moduleConfig->title != '') $App->moduleData->title = $App->moduleConfig->title;

// gestione innagine header
$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if ($App->moduleConfig->image_header != '') $App->moduleData->imageheader = $App->moduleConfig->image_header;
if ($App->moduleConfig->org_image_header != '') $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;

if (Core::$resultOp->error == 0) {
	switch (Core::$request->method) {

		case 'aaaaaaadt':
			$id = intval(Core::$request->param);
			if ($id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); die();}

			Sql::initQuery(DB_TABLE_PREFIX.'faq',array('*'),array($id),"active = 1 AND id = ?",'');
			$App->item = Sql::getRecord();
			
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); };
			
			$App->item->content = Multilanguage::getLocaleObjectValue($App->item,'content_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1)); 
			

			$App->item->meta_title = $App->item->name;
			$App->item->meta_description = ToolsStrings::getStringFromTotNumberChar($App->item->content,array('numchars'=>150));
			if ($App->item->meta_description == '' && $App->item->content != '') $App->item->meta_description = ToolsStrings::getStringFromTotNumberChar($App->item->content,array('numchars'=>150));

			$foo = $App->item->meta_description;
			$foo = ToolsStrings::getStringFromTotNumberChar($foo,array('numchars'=>100));
			$foo = explode(' ',$foo);
			$App->item->meta_keywords = implode(', ',$foo);

			$App->breadcrumbs->items[] = array('class'=>'breadcrumb-item ','url'=>URL_SITE.Core::$request->action.'/ls','title'=>strip_tags($App->moduleData->title));				
			$App->breadcrumbs->items[] = array('class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags($App->item->name));				
			$App->breadcrumbs->title = $App->item->name;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,array('template'=>$templateBreadcrumbsBar));	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->item->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage .= ' '.$App->item->meta_description;
			$App->metaKeywordsPage .= $App->item->meta_keywords;

			$App->meta_og_url = $App->meta_og_url = URL_SITE.Core::$request->action.'/dt/'.$App->item->id;
			$App->meta_og_type = 'article';
			$App->meta_og_title = SanitizeStrings::RemoveSpecialChar(
				$App->item->meta_title,
				$listchars=array( '\'', '"', '<', '>' ),
				''
			);
			$App->meta_og_image = '';
			if ($App->moduleData->imageheader != '') $App->meta_og_image = UPLOAD_DIR.'news/'.$App->moduleData->imageheader;
			if ($App->item->filename != '') $App->meta_og_image = UPLOAD_DIR.'news/'.$App->item->filename;
			$App->meta_og_description = $App->item->meta_description;

			$App->view = 'dt';	

		break;

		default:

			$itemsForPage = 6;
			if (!isset($_SESSION[Core::$request->action]['page'])) {
				$_SESSION[Core::$request->action]['page'] = 1;
			}
			if ( isset(Core::$request->page) && Core::$request->page > 0 ) {
				$_SESSION[Core::$request->action]['page'] = Core::$request->page;
			}

			/* preleva le voci */
			//Config::$debugMode = 1;
			$table = DB_TABLE_PREFIX."faq";
			$fields = array("*");
			$fieldsVal = array();
			$clause = "active = 1";		
			$and = ' AND ';
			$App->items = array();
			Sql::initQuery($table,$fields,$fieldsVal,$clause);				
			Sql::setOrder('ordering ASC');
			Sql::setResultPaged(true);
			Sql::setPage($_SESSION[Core::$request->action]['page']);
			Sql::setItemsForPage($itemsForPage);
			$pdoObject = Sql::getPdoObjRecords();
			while ($row = $pdoObject->fetch()) {
				$row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1));
				$row->content = Multilanguage::getLocaleObjectValue($row,'content_',Config::$langVars['user'],array('htmLawed'=>1,'parse'=>1));
				$App->items[] = $row;		
			}
			//ToolsStrings::dump($App->items);
			$App->pagination = Utilities::getPagination($_SESSION[Core::$request->action]['page'],Sql::getTotalsItems(),$itemsForPage);
			$App->pageDataUrl = URL_SITE.Core::$request->action.'/ls';

			$App->breadcrumbs->items[] = array('class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags($App->moduleData->title));				
			$App->breadcrumbs->title = $App->moduleData->title;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,array('template'=>$templateBreadcrumbsBar));	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage .= ' '.$App->moduleConfig->meta_description;
			$App->metaKeywordsPage .= $App->moduleConfig->meta_keywords;

			$App->meta_og_url = URL_SITE.Core::$request->action;
			$App->meta_og_type = 'website';
			$App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
			$App->meta_og_image = '';
			if ($App->moduleData->imageheader != '') $App->meta_og_image = UPLOAD_DIR.'tema/'.$App->moduleData->imageheader;
			$App->meta_og_description = $App->moduleConfig->meta_description;
		break;
	}
}

switch ($App->view) {
	default:
	break;
}
?>