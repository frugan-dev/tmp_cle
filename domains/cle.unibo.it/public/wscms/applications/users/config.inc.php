<?php
/* wscms/users/config.inc.php v.3.5.4. 28/03/2019 */

$App->params = new stdClass();
$App->params->label = "Utenti";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('users'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj; 

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPathDirs = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';
$App->params->tables['item'] = DB_TABLE_PREFIX.'users';
$App->params->fields['item'] = array(
'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'username'=>array('label'=>'Username','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'password'=>array('label'=>'Password','searchTable'=>false,'required'=>false,'type'=>'password'),
	'name'=>array('label'=>'Nome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'surname'=>array('label'=>'Cognome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'street'=>array('label'=>'Via','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'city'=>array('label'=>'Città','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'zip_code'=>array('label'=>'C.A.P.','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'province'=>array('label'=>'Provincia','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'state'=>array('label'=>'Stato','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'telephone'=>array('label'=>'Telefono','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>'Email','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'mobile'=>array('label'=>'Cellulare','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'fax'=>array('label'=>'Fax','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'skype'=>array('label'=>'Skype','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'template'=>array('label'=>'Template','searchTable'=>true,'type'=>'varchar'),
	'avatar'=>array('label'=>'Avatar','searchTable'=>false,'type'=>'blob'),
	'avatar_info'=>array('label'=>'Avatar Info','searchTable'=>false,'type'=>'varchar'),
	'levels_id'=>array('label'=>'Livello','searchTable'=>false,'type'=>'ind'),
	'is_root'=>array('label'=>'Root','searchTable'=>false,'type'=>'varchar','defValue'=>0),
	'hash'=>array('label'=>'Hash','searchTable'=>false,'type'=>'varchar'),
	'created'									        => array(
		'label'									        => Config::$langVars['creazione'],
		'searchTable'							        => false,
		'required'								        => false,
		'type'									        => 'datatime',
		'defValue'								        => Config::$nowDateTimeIso,
		'validate'								        => 'datetimeiso',
		'forcedValue'              				        => Config::$nowDateTimeIso
	),
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