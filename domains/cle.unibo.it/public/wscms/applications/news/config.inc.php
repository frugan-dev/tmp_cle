<?php
/* wscms/news/config.inc.php 06/06/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('news'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->subcategories = 0;
$App->params->categories = 1;
$App->params->item_images = 0;
$App->params->item_files = 1;

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Notizie';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Notizie</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."news/";
$App->params->itemUploadDir = UPLOAD_DIR."news/";
$App->params->cateUploadPathDir = ADMIN_PATH_UPLOAD_DIR."news/categories/";
$App->params->cateUploadDir = UPLOAD_DIR."news/categories/";
$App->params->scatUploadPathDir = ADMIN_PATH_UPLOAD_DIR."news/subcategories/";
$App->params->scatUploadDir = UPLOAD_DIR."news/subcategories/";
$App->params->ifilUploadPathDir = ADMIN_PATH_UPLOAD_DIR."news/files/";
$App->params->ifilUploadDir = UPLOAD_DIR."news/files/";

$App->params->orderingType = 'DESC';
$App->params->labels['item'] = array('item'=>'notizia','itemSex'=>'a','items'=>'notizie','itemsSex'=>'e','owner'=>'Categoria','ownerSex'=>'a','owners'=>'Categorie','ownersSex'=>'e');
$App->params->labels['cate'] = array('item'=>'categoria','itemSex'=>'a','items'=>'categorie','itemsSex'=>'e','son'=>'notizia','sonSex'=>'a','sons'=>'notizie','sonsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['ifil'] = array('item'=>'file','itemSex'=>'o','items'=>'files','itemsSex'=>'i','owner'=>'notizia','ownerSex'=>'a','owners'=>'notizie','ownersSex'=>'e');

$App->params->module_has_config = 1;

$App->params->tableItem = DB_TABLE_PREFIX.'news';
$App->params->tableCate = DB_TABLE_PREFIX.'news_cat';
$App->params->tableIfil = DB_TABLE_PREFIX.'news_files';

$App->params->tables = array();
$App->params->tables['news'] = DB_TABLE_PREFIX.'news';

$App->params->fieldsItem = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_cat'=>array('label'=>'ID Cat','required'=>false,'type'=>'int'),
	'datatimeins'								=> array(
		'label'									=>'Data',
		'searchTable'							=> false,
		'required'								=> true,
		'type'									=> 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso,
		'validate'								=> 'datetimepicker'
	),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'org_filename'=>array('label'=>'','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'embedded'=>array('label'=>'Embedded','searchTable'=>false,'required'=>false,'type'=>'text'),
	'created'=>array('label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
	);		
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsItem['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	$App->params->fieldsItem['summary_'.$lang] = array('label'=>'Sommario '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar');
	$App->params->fieldsItem['content_'.$lang] = array('label'=>'Contenuto '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text');
	}
	
$App->params->fieldsCate = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'ordering'=>array('label'=>'Ordine','searchTable'=>false,'required'=>false,'type'=>'int'),
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
	$App->params->fieldsCate['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}
	
$App->params->fieldsIfil = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>false,'searchTable'=>false,'type'=>'int'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'extension'=>array('label'=>'Ext','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'size'=>array('label'=>'Dimensione','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'type'=>array('label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar'),
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
	$App->params->fieldsIfil['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}


$App->params->uploadPaths['conf'] = ADMIN_PATH_UPLOAD_DIR."news/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."news/";
$App->params->tables['conf'] = Config::$DatabaseTables['news config'];
$App->params->fields['conf'] = Config::$DatabaseTablesFields['news config'];

// resources
$App->params->tables['item resources owner'] = DB_TABLE_PREFIX.'news';
$App->params->uploadDirs['item resources owner'] = UPLOAD_DIR."news/";

$App->params->tables['item resources'] = DB_TABLE_PREFIX.'news_resources';
$App->params->fields['item resources'] = Config::$DatabaseTablesFields['resources for item'];
$App->params->uploadPaths['item resources'] = ADMIN_PATH_UPLOAD_DIR."news/files/";
$App->params->uploadDirs['item resources'] = UPLOAD_DIR."news/files/";
$App->params->orderTypes['item resources'] = 'ASC';

?>