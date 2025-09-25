<?php
/* wscms/pagetemplates/config.inc.php v.3.5.4. 28/03/2019 */

$App->params = new stdClass();
$App->params->label = "Template pagine";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['label','help_small','help'],['pagetemplates'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;


$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->ordersType = [];

$App->params->uploadDirs['page'] = UPLOAD_DIR."pages/";

$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."pages/templates/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."pages/templates/";
$App->params->ordersType['item'] = 'ASC';
$App->params->tables['item'] = DB_TABLE_PREFIX.'pagetemplates';
$App->params->fields['item'] = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true],
	'title'=>['label'=>'Titolo','searchTable'=>true,'required'=>true,'type'=>'varchar'],
	'content'=>['label'=>$_lang['contenuto'],'searchTable'=>true,'required'=>false,'type'=>'text'],
	'template'=>['label'=>'Template','searchTable'=>true,'required'=>true,'type'=>'varchar'],
	'filename'=>['label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar'],
	'ordering'=>['label'=>'Ordine','searchTable'=>false,'required'=>false,'type'=>'int'],
	'predefinito'=>['label'=>'Predefinito','required'=>false,'type'=>'int','validate'=>'int','defValue'=>'0'],
	'css_links'=>['label'=>'Css link','required'=>false,'type'=>'varchar','defValue'=>''],
	'jscript_init_code'=>['label'=>'Codice Javascript inizio BODY','required'=>false,'type'=>'varchar','defValue'=>''],
	'jscript_links'=>['label'=>'Javascrip link','required'=>false,'type'=>'varchar','defValue'=>''],
	'jscript_last_links'=>['label'=>'Ultimi Javascrips links','required'=>false,'type'=>'int','defValue'=>''],
	'base_tpl_page'=>['label'=>'Template di base','required'=>false,'type'=>'int','defValue'=>''],
	'created'=>['label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTimeIso,'validate'=>'datatimeiso'],
	'active'=>['label'=>'Attiva','required'=>false,'type'=>'int','validate'=>'int','defValue'=>'0']
	];	
?>