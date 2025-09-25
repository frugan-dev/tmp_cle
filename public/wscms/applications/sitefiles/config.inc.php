<?php
/* wscms/site-files/config.inc.php v.1.0.0. 14/06/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['name','help_small','help'],['site-files'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->multicategories = 0;

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Files Sito';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Files Sito</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-media/files/";
$App->params->itemUploadDir = UPLOAD_DIR."site-media/files/";

$App->params->orderingType = 'DESC';

$App->params->labels['fold'] = ['item'=>'cartella','itemSex'=>'a','items'=>'cartelle','itemsSex'=>'e','son'=>'file','sonSex'=>'o','sons'=>'files','sonsSex'=>'i','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>''];
$App->params->labels['item'] =  ['item'=>'file','itemSex'=>'o','items'=>'files','itemsSex'=>'i','son'=>'','sonSex'=>'','sons'=>'','sonsSex'=>'','owner'=>'cartella','ownerSex'=>'a','owners'=>'cartelle','ownersSex'=>'e'];

$App->params->tableItem = DB_TABLE_PREFIX.'site_files';
$App->params->tableFold = DB_TABLE_PREFIX.'site_files_folders';

$App->params->fieldsItem = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true],
	'id_folder'=>['label'=>'IDFolder','required'=>false,'searchTable'=>false,'type'=>'int'],
	'folder_name'=>['label'=>'Cartella','required'=>false,'searchTable'=>false,'type'=>'varchar'],
	'filename'=>['label'=>'File','searchTable'=>true,'required'=>true,'type'=>'varchar'],
	'org_filename'=>['label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'],
	'extension'=>['label'=>'Ext','searchTable'=>true,'required'=>false,'type'=>'varchar'],
	'size'=>['label'=>'Dimensione','searchTable'=>true,'required'=>false,'type'=>'varchar'],
	'type'=>['label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar'],
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