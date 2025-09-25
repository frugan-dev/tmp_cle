<?php
/* wscms/slides-topsite/config.php 06/06/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(Sql::getTablePrefix().'site_modules',['help_small','help'],['slides-home-rev'],'name = ? AND active = 1');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$globalSettings['module sections'][] = 'site-pagelist';

$App->params->codeVersion = ' 2.6.3.';
$App->params->pageTitle = 'Slides top sito';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Slide top sito</li>';

$App->modules = [];

$obj = new stdClass();
$obj->id = 19;
$obj->name = 'site-pageslist';
$obj->label = 'Pagine con Blocchi';
$obj->alias = 'site-pageslist';
$obj->ordering = 2;
$obj->section = 1;
$App->modules[$obj->alias] = $obj;

foreach($globalSettings['module sections'] AS $key=>$value) {
	if ($key > 1) {
		if(isset($App->site_modules[$key]) && is_array($App->site_modules[$key]) && count($App->site_modules[$key]) > 0) {
			foreach ($App->site_modules[$key] AS $value) {
				$obj = new stdClass();
				$obj->id = $value->id;
				$obj->name =  $value->name;
				$obj->label =  $value->label;
				$obj->alias = $value->alias;
				$obj->ordering =  $value->ordering;
				$obj->section =  $value->section;
				$App->modules[$value->alias] = $obj;
				}								
			}
		}
	}
	
//print_r($App->modules);

$App->params->orderingType = 'DESC';

$App->params->uploadPathDir = PATH_UPLOAD_DIR."slides-topsite/";
$App->params->uploadDir = UPLOAD_DIR."slides-topsite/";

$App->params->labels['main'] = ['item'=>'slide','itemSex'=>'a','items'=>'slides','itemsSex'=>'e'];

$App->params->table = DB_TABLE_PREFIX.'slides_topsite';


$App->params->fields = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true],
	'filename'=>['label'=>'Immagine','searchTable'=>true,'required'=>true,'type'=>'varchar'],
	'org_filename'=>['label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'],
	'modulo'=>['label'=>'Modulo/Pagina','required'=>false,'type'=>'varchar',],
	'ordering'=>['label'=>'Ord','required'=>false,'type'=>'int','defValue'=>1],
	'created'=>['label'=>'Creazione','searchTable'=>false,'required'=>false,'type'=>'datatime'],
	'active'=>['label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0]
	];
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fields['title_'.$lang] = ['label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar'];	
	$App->params->fields['content_'.$lang] = ['label'=>'Contenuto '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar'];
	}
?>