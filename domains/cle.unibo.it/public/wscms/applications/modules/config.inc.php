<?php
/*	wscms/modules/config.inc.php v.3.5.4. 08/01/2020 */

$App->params = new stdClass();
$App->params->label = "Moduli";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('modules'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

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
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'name'=>array('required'=>true,'label'=>ucfirst($_lang['nome']),'searchTable'=>true,'type'=>'varchar|255'),
	'label'=>array('required'=>true,'label'=>ucfirst($_lang['etichetta']),'searchTable'=>true,'type'=>'varchar|255'),
	'alias'=>array('required'=>true,'label'=>ucfirst($_lang['alias']),'searchTable'=>true,'type'=>'varchar|100'),
	'content'=>array('label'=>ucfirst($_lang['contenuto']),'searchTable'=>true,'type'=>'text'),
	'code_menu'=>array('label'=> $_lang['codice menu'],'searchTable'=>true,'type'=>'text','validate'=>'json'),
	'ordering'=>array('label'=>ucfirst($_lang['ordine']),'searchTable'=>false,'type'=>'int|8','defValue'=>'0'),
	'section'=>array('label'=>ucfirst($_lang['sezione']),'searchTable'=>false,'type'=>'int|1'),
	'help_small'=>array('label'=>ucfirst($_lang['aiuto breve']),'searchTable'=>false,'type'=>'varchar|255'),
	'help'=>array('label'=>ucfirst($_lang['aiuto']),'searchTable'=>false,'type'=>'text'),
	'active'									        => array(
		'label'									        => Config::$langVars['attiva'],
		'required'								        => false,
		'type'									        => 'int|1',
		'validate'			    				        => 'int',
		'defValue'								        => '0',
		'forcedValue'              				        => 1
	)
);		
?>