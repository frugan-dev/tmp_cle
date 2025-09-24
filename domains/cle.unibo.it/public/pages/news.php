<?php
/* news.php v.3.5.4. 04/04/2019 */

$App->moduleData = new stdClass();

// preleva configurazione modulo
Sql::initQuery(DB_TABLE_PREFIX.'news_config',['*'],[],'');	
$App->moduleConfig = Sql::getRecord();
$App->moduleConfig->title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
$App->moduleConfig->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig,'text_intro_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
$App->moduleConfig->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'page_content_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 

$App->moduleConfig->meta_title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
$App->moduleConfig->meta_description = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_description_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
$App->moduleConfig->meta_keywords = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_keywords_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
//ToolsStrings::dump($App->moduleConfig);

// gestione titolo
$App->moduleData->title = Config::$langVars['contatti'];
if ($App->moduleConfig->title != '') $App->moduleData->title = $App->moduleConfig->title;

// gestione innagine header
$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if ($App->moduleConfig->image_header != '') $App->moduleData->imageheader = $App->moduleConfig->image_header;
if ($App->moduleConfig->org_image_header != '') $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;

// preleva le categorie
Sql::initQuery(DB_TABLE_PREFIX.'news_cat',['*'],[],'active = 1');	
Sql::setOrder('title_'.$_lang['user'].' DESC');
Sql::setOptions(['fieldTokeyObj'=>'id']);
Sql::setOrder('ordering DESC');
$pdoObject = Sql::getPdoObjRecords();
while ($row = $pdoObject->fetch()) {
    $row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
	// preleva i totali elementi per categoria
	Sql::initQuery(DB_TABLE_PREFIX.'news',['*'],[$row->id],'active = 1 AND id_cat = ?','');
	$row->category_news = Sql::countRecord();
    $App->news_categories[] = $row;		
}

// preleva i totali elementi per categoria 0
Sql::initQuery(DB_TABLE_PREFIX.'news',['*'],[],'active = 1','');
$App->category_all_tot_news = Sql::countRecord();


if (Core::$resultOp->error == 0) {
	switch (Core::$request->method) {

		case 'df':
			if (intval(Core::$request->param) > 0) {
				$renderTpl = false;		
				ToolsDownload::downloadFileFromDB2(
					PATH_UPLOAD_DIR."news/files/",
					[
						'table'				=> DB_TABLE_PREFIX.'news_resources',
						'valuesClause'		=> [intval(Core::$request->param)],
						'whereClause'		=> 'id = ? AND resource_type = 2'
					]
				);	
				
				if (Core::$resultOp->error == 1) ToolsStrings::redirect(URL_SITE.'error/404');					
			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}
			die();
		break;
		
		case 'dt':
			$id = intval(Core::$request->param);
			if ($id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); die();}

			Sql::initQuery(DB_TABLE_PREFIX.'news',['*'],[$id],"active = 1 AND id = ?",'');
			$App->item = Sql::getRecord();
			
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); };
			
			$App->item->title = Multilanguage::getLocaleObjectValue($App->item,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
			$App->item->content = Multilanguage::getLocaleObjectValue($App->item,'content_',Config::$langVars['user'],['htmLawed'=>1,'parse'=>1]); 
			$App->item->summary = Multilanguage::getLocaleObjectValue($App->item,'summary_',Config::$langVars['user'],['htmLawed'=>1,'parse'=>1]); 
			$App->item->dataformatted = DateFormat::getDateTimeIsoFormatString($App->item->datatimeins,'%DAY% %STRINGMONTH% %YEAR%',[]); 

			$App->item->meta_title = $App->item->title;
			$App->item->meta_description = ToolsStrings::getStringFromTotNumberChar($App->item->summary,['numchars'=>150]);
			if ($App->item->meta_description == '' && $App->item->content != '') $App->item->meta_description = ToolsStrings::getStringFromTotNumberChar($App->item->content,['numchars'=>150]);

			$foo = $App->item->summary;
			$foo = ToolsStrings::getStringFromTotNumberChar($App->item->summary,['numchars'=>100]);
			$foo = explode(' ',(string) $foo);
			$App->item->meta_keywords = implode(', ',$foo);

			

			// requpera i file associati item
			$App->item->files = [];
			Sql::initQuery(DB_TABLE_PREFIX.'news_resources',['*'],[$App->item->id],"active = 1 AND id_owner = ? AND resource_type = 2",'');
			Sql::setOrder('ordering ASC');			
			$obj = Sql::getRecords();
			if (Core::$resultOp->error == 1) break;						
			$arr = [];
			if (is_array($obj) && is_array($obj) && count($obj) > 0) {
				foreach ($obj AS $value) {		
					$value->title =  Multilanguage::getLocaleObjectValue($value,'title_',$_lang['user'],[]);
					$value->image =  ToolsDownload::getFileIcon($value->filename,[]);
					$value->url = URL_SITE.'news/df/'.$value->id.'/'.$value->org_filename;		
					$arr[] = $value;
				}
			}
			$App->item->files = $arr;

		



			$App->breadcrumbs->items[] = ['class'=>'breadcrumb-item ','url'=>URL_SITE.Core::$request->action.'/ls','title'=>strip_tags((string) $App->moduleData->title)];				
			$App->breadcrumbs->items[] = ['class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags((string) $App->item->title)];				
			$App->breadcrumbs->title = $App->item->title;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,['template'=>$templateBreadcrumbsBar]);	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->item->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage .= ' '.$App->item->meta_description;
			$App->metaKeywordsPage .= $App->item->meta_keywords;

			$App->meta_og_url = $App->meta_og_url = URL_SITE.Core::$request->action.'/dt/'.$App->item->id;
			$App->meta_og_type = 'article';
			$App->meta_og_title = SanitizeStrings::RemoveSpecialChar(
				$App->item->meta_title,
				$listchars=[ '\'', '"', '<', '>' ],
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

			/* preleva le news */
			$table = DB_TABLE_PREFIX."news AS n LEFT JOIN ".DB_TABLE_PREFIX."news_cat AS nc ON (n.id_cat = nc.id)";
			$fields = ["n.*","nc.title_it AS category_title_it"];
			$fieldsVal = [];
			$clause = "n.active = 1";		
			$and = ' AND ';

			if (!isset($_SESSION[Core::$request->action]['id_cat']))  $_SESSION[Core::$request->action]['id_cat'] = 0;
			if (isset(Core::$request->method) && Core::$request->method == 'idcat') {
				if (isset(Core::$request->param) && Core::$request->param != '') {
					$_SESSION[Core::$request->action]['id_cat'] = intval(Core::$request->param);
				}
			}
				
			if ($_SESSION[Core::$request->action]['id_cat'] > 0) {
				$clause .= $and.' id_cat =  ?';
				$fieldsVal[] = $_SESSION[Core::$request->action]['id_cat'];
			}

			$App->items = [];
			Sql::initQuery($table,$fields,$fieldsVal,$clause);				
			Sql::setOrder('datatimeins DESC');
			Sql::setResultPaged(true);
			Sql::setPage($_SESSION[Core::$request->action]['page']);
			Sql::setItemsForPage($itemsForPage);
			$pdoObject = Sql::getPdoObjRecords();
			while ($row = $pdoObject->fetch()) {
				$row->title = Multilanguage::getLocaleObjectValue($row,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
				$row->summary = Multilanguage::getLocaleObjectValue($row,'summary_',Config::$langVars['user'],['htmLawed'=>1,'parse'=>1]); 
				$row->dataformatted = DateFormat::getDateTimeIsoFormatString($row->datatimeins,'%DAY% %STRINGMONTH% %YEAR%',[]); 
				// preleva la categoria
				Sql::initQuery(DB_TABLE_PREFIX.'news_cat',['*'],[$row->id_cat],'id = ?','');
				$foo = Sql::getRecord();
				$row->category = Multilanguage::getLocaleObjectValue($foo,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
				$row->urlItem = URL_SITE.Core::$request->action.'/dt/'.$row->id;

				// modifica dati embedded
				$row->video = '';
				if (isset($row->embedded) && $row->embedded != '') {
					$row->video = $row->embedded;
					$row->video = preg_replace('/(width)="\d*"\s/',"",(string) $row->video);
					$row->video = preg_replace('/(height)="\d*"\s/',"",$row->video);
					$row->video = preg_replace('/iframe/','iframe width="100%" height="100%"',$row->video);
				}

				$App->items[] = $row;		
			}

			//ToolsStrings::dump($App->items);die();
		
			$App->pagination = Utilities::getPagination($_SESSION[Core::$request->action]['page'],Sql::getTotalsItems(),$itemsForPage);
			$App->pageDataUrl = URL_SITE.Core::$request->action.'/ls';
			
			$App->breadcrumbs->items[] = ['class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags((string) $App->moduleData->title)];				
			$App->breadcrumbs->title = $App->moduleData->title;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,['template'=>$templateBreadcrumbsBar]);	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage .= ' '.$App->moduleConfig->meta_description;
			$App->metaKeywordsPage .= $App->moduleConfig->meta_keywords;

			$App->meta_og_url = URL_SITE.Core::$request->action;
			$App->meta_og_type = 'website';
			$App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
			$App->meta_og_image = '';
			if ($App->moduleData->imageheader != '') $App->meta_og_image = UPLOAD_DIR.'news/'.$App->moduleData->imageheader;
			$App->meta_og_description = $App->moduleConfig->meta_description;
		break;
	}
}	
	
/* SEZIONE VIEW */

switch ($App->view) {
	case 'dt':
		$App->templateApp = 'new';
	break;

	default:
	break;
}