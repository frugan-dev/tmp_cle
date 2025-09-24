<?php
/* wscms/site-galleries/config.inc.php v.1.0.0. 26/06/2016
*/

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('site-galleries'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' v.2.6.3.';
$App->params->pageTitle = 'Gallerie Immagini';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Gallerie Immagini</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-media/galleries/";
$App->params->itemUploadDir = UPLOAD_DIR."site-media/galleries/";

$App->params->orderingType = 'ASC';

$App->params->labels['cate'] = array('item'=>'galleria','itemSex'=>'a','items'=>'gallerie','itemsSex'=>'e','son'=>'immagine','sonSex'=>'a','sons'=>'immagini','sonsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['item'] =  array('item'=>'immagine','itemSex'=>'a','items'=>'immagini','itemsSex'=>'e','son'=>'','sonSex'=>'','sons'=>'','sonsSex'=>'','owner'=>'cartella','ownerSex'=>'a','owners'=>'cartelle','ownersSex'=>'e');

$App->params->tableItem = DB_TABLE_PREFIX.'site_galleries';
$App->params->tableCate = DB_TABLE_PREFIX.'site_galleries_cat';

$App->params->fieldsItem = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_cat'=>array('label'=>'IDFolder','required'=>false,'searchTable'=>false,'type'=>'int'),
	'folder_name'=>array('label'=>'Folder','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'filename'=>array('label'=>'Immagine','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>'Ordinamento','searchTable'=>false,'required'=>false,'type'=>'int'),
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
	}

$App->params->fieldsCate = array(
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
	$App->params->fieldsCate['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}
?>