<?php
/*	wscms/modules/config.inc.php v.3.5.4. 08/01/2020 */

$App->params = new stdClass();
$App->params->label = "Moduli";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['label','help_small','help'],['modules'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->ordersType = [];

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'modules';

$App->sections = $globalSettings['module sections'];

/* ITEM */
$App->params->ordersType['item'] = 'ASC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."modules/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."modules/";
$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = [
	'id'=>['label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true],
	'name'=>['required'=>true,'label'=>ucfirst((string) $_lang['nome']),'searchTable'=>true,'type'=>'varchar|255'],
	'label'=>['required'=>true,'label'=>ucfirst((string) $_lang['etichetta']),'searchTable'=>true,'type'=>'varchar|255'],
	'alias'=>['required'=>true,'label'=>ucfirst((string) $_lang['alias']),'searchTable'=>true,'type'=>'varchar|100'],
	'content'=>['label'=>ucfirst((string) $_lang['contenuto']),'searchTable'=>true,'type'=>'text'],
	'code_menu'=>['label'=> $_lang['codice menu'],'searchTable'=>true,'type'=>'text','validate'=>'json'],
	'ordering'=>['label'=>ucfirst((string) $_lang['ordine']),'searchTable'=>false,'type'=>'int|8','defValue'=>'0'],
	'section'=>['label'=>ucfirst((string) $_lang['sezione']),'searchTable'=>false,'type'=>'int|1'],
	'help_small'=>['label'=>ucfirst((string) $_lang['aiuto breve']),'searchTable'=>false,'type'=>'varchar|255'],
	'help'=>['label'=>ucfirst((string) $_lang['aiuto']),'searchTable'=>false,'type'=>'text'],
	'active'									        => [
		'label'									        => Config::$langVars['attiva'],
		'required'								        => false,
		'type'									        => 'int|1',
		'validate'			    				        => 'int',
		'defValue'								        => '0',
		'forcedValue'              				        => 1
	]
];		
?>