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
Sql::initQuery(DB_TABLE_PREFIX.'modules',['name','help_small','help'],['site_images'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && is_array($obj)) $App->params = $obj;

$App->params->multicategories = 0;

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Immagini Sito';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Immagini Sito</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-media/images/";
$App->params->itemUploadDir = UPLOAD_DIR."site-media/images/";

$App->params->orderingType = 'DESC';

$App->params->labels['fold'] = ['item'=>'cartella','itemSex'=>'a','items'=>'cartelle','itemsSex'=>'e','son'=>'immagine','sonSex'=>'a','sons'=>'immagini','sonsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>''];
$App->params->labels['item'] =  ['item'=>'immagine','itemSex'=>'a','items'=>'immagini','itemsSex'=>'e','son'=>'','sonSex'=>'','sons'=>'','sonsSex'=>'','owner'=>'cartella','ownerSex'=>'a','owners'=>'cartelle','ownersSex'=>'e'];

$App->params->tableItem = DB_TABLE_PREFIX.'site_images';
$App->params->tableFold = DB_TABLE_PREFIX.'site_images_folders';

$App->params->fieldsItem = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true],
	'id_folder'=>['label'=>'IDFolder','required'=>false,'searchTable'=>false,'type'=>'int'],
	'folder_name'=>['label'=>'Cartella','required'=>false,'searchTable'=>false,'type'=>'varchar'],
	'filename'=>['label'=>'Immagine','searchTable'=>true,'required'=>true,'type'=>'varchar'],
	'org_filename'=>['label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'],
	'created'                                   =>  [
        'label'                                 => Config::$langVars['creazione'],
        'searchTable'                           => false,
        'required'                              => false,
        'type'                                  => 'datatime',
        'defValue'                              => Config::$nowDateTimeIso,
        'forcedValue'                           => Config::$nowDateTimeIso
    ],
    'active'                                    =>  [
        'label'                                 => Config::$langVars['attiva'],
        'required'                              => false,
        'type'                                  => 'int|1',
        'defValue'                              => 1,
        'forcedValue'                           => 1
    ],   
	];	
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsItem['title_'.$lang] = ['label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar'];
	}

$App->params->fieldsFold = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true],		
	'folder_name'=>['label'=>'Nome Cartella','searchTable'=>false,'required'=>false,'type'=>'varchar'],
	'created'                                   =>  [
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	],
	'active'                                    =>  [
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	]   
];
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsFold['title_'.$lang] = ['label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar'];
	}
?>