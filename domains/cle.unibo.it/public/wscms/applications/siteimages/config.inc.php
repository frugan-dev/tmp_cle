<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	site-images/config.inc.php v.2.6.3. 05/04/2016
*/

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('site_images'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && is_array($obj)) $App->params = $obj;

$App->params->multicategories = 0;

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Immagini Sito';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Immagini Sito</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-media/images/";
$App->params->itemUploadDir = UPLOAD_DIR."site-media/images/";

$App->params->orderingType = 'DESC';

$App->params->labels['fold'] = array('item'=>'cartella','itemSex'=>'a','items'=>'cartelle','itemsSex'=>'e','son'=>'immagine','sonSex'=>'a','sons'=>'immagini','sonsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['item'] =  array('item'=>'immagine','itemSex'=>'a','items'=>'immagini','itemsSex'=>'e','son'=>'','sonSex'=>'','sons'=>'','sonsSex'=>'','owner'=>'cartella','ownerSex'=>'a','owners'=>'cartelle','ownersSex'=>'e');

$App->params->tableItem = DB_TABLE_PREFIX.'site_images';
$App->params->tableFold = DB_TABLE_PREFIX.'site_images_folders';

$App->params->fieldsItem = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_folder'=>array('label'=>'IDFolder','required'=>false,'searchTable'=>false,'type'=>'int'),
	'folder_name'=>array('label'=>'Cartella','required'=>false,'searchTable'=>false,'type'=>'varchar'),
	'filename'=>array('label'=>'Immagine','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'created'                                   => array (
        'label'                                 => Config::$langVars['creazione'],
        'searchTable'                           => false,
        'required'                              => false,
        'type'                                  => 'datatime',
        'defValue'                              => Config::$nowDateTimeIso,
        'forcedValue'                           => Config::$nowDateTimeIso
    ),
    'active'                                    => array (
        'label'                                 => Config::$langVars['attiva'],
        'required'                              => false,
        'type'                                  => 'int|1',
        'defValue'                              => 1,
        'forcedValue'                           => 1
    ),   
	);	
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsItem['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}

$App->params->fieldsFold = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),		
	'folder_name'=>array('label'=>'Nome Cartella','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	)   
);
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsFold['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}
?>