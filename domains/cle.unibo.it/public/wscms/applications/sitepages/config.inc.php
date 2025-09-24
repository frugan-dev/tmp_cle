<?php
/* wscms/site-pages/config.inc.php v.1.0.1. 07/09/2016 */

/* prende i dati del modulo template dal file conf */
include_once(PATH."applications/oldpagetemplates/config.inc.php");


$App->templateUploadDir = $App->params->itemUploadDir;
$App->templateUploadDirDef = $App->params->defUploadDir;

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('site-pages'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;


$App->params->item_images = 0;
$App->params->item_files = 1;

$App->params->codeVersion = ' 1.0.1.';
$App->params->pageTitle = 'Pagine';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Pagine Sito</li>';

$App->params->uploadPathDir = PATH_UPLOAD_DIR."site-pages/";
$App->params->uploadDir = UPLOAD_DIR."site-pages/";
$App->params->ifilUploadPathDir = PATH_UPLOAD_DIR."site-pages/files/";
$App->params->ifilUploadDir = UPLOAD_DIR."site-pages/files/";

$App->params->orderingType = 'DESC';

$App->params->labels['item'] = array('item'=>'pagina','itemSex'=>'a','items'=>'pagine','itemsSex'=>'e','owner'=>'Categoria','ownerSex'=>'a','owners'=>'Categorie','ownersSex'=>'e');
$App->params->labels['cate'] = array('item'=>'categoria','itemSex'=>'a','items'=>'categorie','itemsSex'=>'e','son'=>'notizia','sonSex'=>'a','sons'=>'notizie','sonsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['ifil'] = array('item'=>'file','itemSex'=>'o','items'=>'files','itemsSex'=>'i','owner'=>$App->params->labels['item']['item'],'ownerSex'=>$App->params->labels['item']['itemSex'],'owners'=>$App->params->labels['item']['items'],'ownersSex'=>$App->params->labels['item']['itemsSex']);

$App->params->tableRif = 'site_pages';
$App->params->table = DB_TABLE_PREFIX.'site_pages';
$App->params->tableIfil = DB_TABLE_PREFIX.'site_pages_att_files';

$App->params->fields = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'parent'=>array('label'=>'Parent','searchTable'=>false,'required'=>false,'type'=>'varchar','defValue'=>0),
	'id_template'=>array('label'=>'Template','searchTable'=>false,'required'=>false,'type'=>'int'),
	'type'=>array('label'=>'Tipo','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>'Ordinamento','searchTable'=>false,'required'=>false,'type'=>'int'),
	'menu'=>array('label'=>'In menu?','searchTable'=>false,'required'=>false,'type'=>'int'),
	'alias'=>array('label'=>'Alias','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'url'=>array('URL'=>'Alias','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'target'=>array('label'=>'Target','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'jscript_init_code'=>array('label'=>'Codice Javascript inizio BODY','required'=>false,'type'=>'varchar','defValue'=>''),
	'created'=>array('label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'updated'=>array('label'=>'Aggiornamento','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
	);	
	
foreach($globalSettings['languages'] AS $lang) {
	$searchTable = true;
	$required = ($lang == 'it' ? true : false);
	$App->params->fields['title_meta_'.$lang] = array('label'=>'Titolo META '.$lang,'searchTable'=>$searchTable,'required'=>false,'type'=>'varchar');
	$App->params->fields['title_seo_'.$lang] = array('label'=>'Titolo SEO '.$lang,'searchTable'=>$searchTable,'required'=>false,'type'=>'varchar');
	$App->params->fields['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>$searchTable,'required'=>$required,'type'=>'varchar');
	$App->params->fields['subtitle_'.$lang] = array('label'=>'Subtitolo '.$lang,'searchTable'=>$searchTable,'required'=>false,'type'=>'varchar');
	}
	
$App->params->fieldsIfil = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>false,'searchTable'=>false,'type'=>'int'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'extension'=>array('label'=>'Ext','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'size'=>array('label'=>'Dimensione','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'type'=>array('label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>'Ordinamento','searchTable'=>false,'required'=>false,'type'=>'int'),
	'created'=>array('label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
	);	
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsIfil['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}

$App->params->typePage = array('default'=>'Default','label'=>'Etichetta','url'=>'URL','module'=>'Link a modulo');
$App->params->targets = array('_self','_blank','_parent','_top');
?>