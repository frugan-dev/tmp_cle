<?php 
/* wscms/site-pageslist/config.inc.php 1.0.0. 13/06/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('site-pageslist'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->subcategories = 0;
$App->params->categories = 1;
$App->params->item_images = 0;
$App->params->item_files = 0;

$App->params->codeVersion = ' 1.0.0.';
$App->params->pageTitle = 'Elenco Pagine';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Elenco Pagine</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-pageslist/";
$App->params->itemUploadDir = UPLOAD_DIR."site-pageslist/";
$App->params->cateUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-pageslist/categories/";
$App->params->cateUploadDir = UPLOAD_DIR."site-pageslist/categories/";
$App->params->scatUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-pageslist/subcategories/";
$App->params->scatUploadDir = UPLOAD_DIR."site-pageslist/subcategories/";
$App->params->ifilUploadPathDir = ADMIN_PATH_UPLOAD_DIR."site-pageslist/files/";
$App->params->ifilUploadDir = UPLOAD_DIR."site-pageslist/files/";

$App->params->orderingType = 'DESC';
$App->params->orderingItemType = 'ASC';

$App->params->labels['item'] = array('item'=>'blocco','itemSex'=>'o','items'=>'blocchi','itemsSex'=>'i','owner'=>'pagina','ownerSex'=>'a','owners'=>'pagine','ownersSex'=>'e');
$App->params->labels['cate'] = array('item'=>'pagina','itemSex'=>'a','items'=>'pagine','itemsSex'=>'e','son'=>'blocco','sonSex'=>'o','sons'=>'blocchi','sonsSex'=>'i','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['ifil'] = array('item'=>'file','itemSex'=>'o','items'=>'files','itemsSex'=>'i','owner'=>'blocco','ownerSex'=>'o','owners'=>'blocchi','ownersSex'=>'i');


$App->params->tableItem = DB_TABLE_PREFIX.'site_pageslist';
$App->params->tableCate = DB_TABLE_PREFIX.'site_pageslist_cat';
$App->params->tableIfil = DB_TABLE_PREFIX.'site_pageslist_files';

$App->params->fieldsItem = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_cat'=>array('label'=>'ID Cat','required'=>false,'type'=>'int'),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'org_filename'=>array('label'=>'','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'embedded'=>array('label'=>'Embedded','searchTable'=>false,'required'=>false,'type'=>'text'),
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
	$App->params->fieldsItem['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	$App->params->fieldsItem['content_'.$lang] = array('label'=>'Contenuto '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text');
	$App->params->fieldsItem['url_'.$lang] = array('label'=>'URL '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar');
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
	'created'=>array('label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
	);	
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsIfil['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}


?>