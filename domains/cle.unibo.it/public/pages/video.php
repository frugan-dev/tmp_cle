<?php
/* video.php v.3.5.4. 08/05/2019 */

//Sql::setDebugMode(1);

/* se ha dati pagina li carica */
$App->modulePageData = new stdClass();
Sql::initQuery(Sql::getTablePrefix().'pages',['*'],['video'],'active = 1 AND (alias LIKE ?)');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->modulePageData = $obj;

$App->breadcrumbs = new stdClass();
$App->breadcrumbs->items = [];
$App->breadcrumbs->items[] = ['class'=>'','url'=>URL_SITE,'title'=>ucfirst((string) $_lang['home'])];

/* preleva eventuali breadcrumbs superiori */
if (isset($dataMenuPages[$App->modulePageData->alias]->breadcrumbs) && is_array($dataMenuPages[$App->modulePageData->alias]->breadcrumbs) && count($dataMenuPages[$App->modulePageData->alias]->breadcrumbs) > 0) array_pop($dataMenuPages[$App->modulePageData->alias]->breadcrumbs);
/* aggiorna breadcrumbs */		
if (isset($dataMenuPages[$App->modulePageData->alias]->breadcrumbs)) $breadcrumbs = $dataMenuPages[$App->modulePageData->alias]->breadcrumbs;
$x = 1;	
if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0) {							
	foreach ($breadcrumbs AS $key=>$value) {
	$url = URL_SITE.$value['alias'];
	if ($value['type'] == 'label') $url = "javascript:void(0);";					
		$App->breadcrumbs->items[$x] = ['class'=>'','url'=>$url,'title'=>$value['title_it']];
		$x++;
	}
}

/* gestione immagine top e bottom pagina */
$App->modulePageData->image_top =  UPLOAD_DIR.'pages/default/default-image-top-pages.jpg';
$App->modulePageData->image_bottom = UPLOAD_DIR.'pages/default/default-image-bottom-pages.jpg';
if (is_object($App->modulePageData)) {
	if (isset($App->modulePageData->filename) && $App->modulePageData->filename != '') $App->modulePageData->image_top =  UPLOAD_DIR.'pages/'.$App->modulePageData->filename;
	if (isset($App->modulePageData->filename1) && $App->modulePageData->filename1 != '') $App->modulePageData->image_bottom =  UPLOAD_DIR.'pages/'.$App->modulePageData->filename1;
	}
/* gestione titoli pagina */ 
$App->titles = Utilities::getTitlesPage(ucfirst((string) $_lang['galleria']),$App->modulePageData,$_lang['user'],[]);
//print_r($App->titles);

if (Core::$resultOp->error == 0) {
	switch (Core::$request->method) {								
		default:
		
			/* preleva levoci */
			$arr = [];
			if (Core::$resultOp->error == 0) {
				Sql::initQuery(DB_TABLE_PREFIX.'video',['*'],[],'active = 1','ordering ASC');
				$obj = Sql::getRecords();
				if (is_array($obj) && is_array($obj) && count($obj) > 0) {
					foreach ($obj AS $value) {		
						$value->title = Multilanguage::getLocaleObjectValue($value,'title_',$_lang['user'],[]);
						$value->content = Multilanguage::getLocaleObjectValue($value,'content_',$_lang['user'],[]);
						$value->tags = str_replace(',',' ',$value->id_tags);
					
						preg_match_all('/<iframe[^>]+src="([^"]+)"/', (string) $value->embedded, $match);
						if (isset($match[1][0])) $value->embedded_url = $match[1][0];
						
						$value->url_image_preview = '';
						if ($value->filename != '') $value->url_image_preview = UPLOAD_DIR.'video/'.$value->filename;
						if ($value->url_video_image_preview != '') $value->url_image_preview = $value->url_video_image_preview;
						
						$arr[] = $value;
					}
				}
			}	
			$App->items = $arr;
			
		
			if (Core::$resultOp->error == 0) {				
				/* gestione titolo pagina */
				$App->titlepage = $App->titles['title'];
				$App->pageUrl = URL_SITE.Core::$request->action.'/ls';
				
				/* gestione breadcrumbs */
				$App->breadcrumbs->title = $App->titles['title'];
				$App->breadcrumbs->items[] = ['class'=>'active','title'=>$App->titles['titleSeo']];				
			   /* SEO **/
				$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->titles['titleMeta'].$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
				$App->metaDescriptionPage .= '';
				$App->metaKeywordsPage .= '';
				$App->view = '';
			} else {
				ToolsStrings::redirect(URL_SITE.'error/db');
				die();
			}	
						
		break;
	
		}
	}	
	
/* SEZIONE VIEW */

switch ($App->view) {
	default:
	
	break;
	}
