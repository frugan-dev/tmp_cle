<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	partners/config.inc.php v.2.6.3 11/05/2016
*/

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('help_small','help'),array('partners'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && $obj != '') $App->params = $obj;

$App->params->subcategories = 0;
$App->params->categories = 0;
$App->params->item_images = 0;
$App->params->item_files = 0;

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Partners';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Notizie</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."partners/";
$App->params->itemUploadDir = UPLOAD_DIR."partners/";
$App->params->cateUploadPathDir = PATH_UPLOAD_DIR."partners/categories/";
$App->params->cateUploadDir = UPLOAD_DIR."partners/categories/";
$App->params->scatUploadPathDir = PATH_UPLOAD_DIR."partners/subcategories/";
$App->params->scatUploadDir = UPLOAD_DIR."partners/subcategories/";
$App->params->ifilUploadPathDir = PATH_UPLOAD_DIR."partners/files/";
$App->params->ifilUploadDir = UPLOAD_DIR."partners/files/";

$App->params->orderingType = 'ASC';
$App->params->targets = array('_self','_blank','_parent','_top');

$App->params->labels['item'] = array('item'=>'partner','itemSex'=>'o','items'=>'partners','itemsSex'=>'i','owner'=>'Categoria','ownerSex'=>'a','owners'=>'Categorie','ownersSex'=>'e');
$App->params->labels['cate'] = array('item'=>'categoria','itemSex'=>'a','items'=>'categorie','itemsSex'=>'e','son'=>'partner','sonSex'=>'o','sons'=>'partners','sonsSex'=>'i','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['ifil'] = array('item'=>'file','itemSex'=>'o','items'=>'files','itemsSex'=>'i','owner'=>'notizia','ownerSex'=>'a','owners'=>'notizie','ownersSex'=>'e');


$App->params->tableItem = DB_TABLE_PREFIX.'partners';
$App->params->tableCate = DB_TABLE_PREFIX.'partners_cat';
$App->params->tableIfil = DB_TABLE_PREFIX.'partners_files';

$App->params->fieldsItem = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_cat'=>array('label'=>'ID Cat','required'=>false,'type'=>'int'),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'org_filename'=>array('label'=>'','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>'Ordine','searchTable'=>false,'required'=>false,'type'=>'int'),
	'url'=>array('URL'=>'Alias','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'target'=>array('label'=>'Target','searchTable'=>false,'required'=>false,'type'=>'varchar'),
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
	$App->params->fieldsItem['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	$App->params->fieldsItem['content_'.$lang] = array('label'=>'Contenuto '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text');
	}
	
$App->params->fieldsCate = array(
		'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),		
		'created'=>array('label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'),
		'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
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
?>