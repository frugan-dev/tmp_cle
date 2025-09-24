<?php
/* wscms/pagetemplates/config.inc.php v.3.5.4. 28/03/2019 */

$App->params = new stdClass();
$App->params->label = "Template pagine";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('pagetemplates'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;


$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

$App->params->uploadDirs['page'] = UPLOAD_DIR."pages/";

$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."pages/templates/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."pages/templates/";
$App->params->ordersType['item'] = 'ASC';
$App->params->tables['item'] = DB_TABLE_PREFIX.'pagetemplates';
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'title'=>array('label'=>'Titolo','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'content'=>array('label'=>$_lang['contenuto'],'searchTable'=>true,'required'=>false,'type'=>'text'),
	'template'=>array('label'=>'Template','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>'Ordine','searchTable'=>false,'required'=>false,'type'=>'int'),
	'predefinito'=>array('label'=>'Predefinito','required'=>false,'type'=>'int','validate'=>'int','defValue'=>'0'),
	'css_links'=>array('label'=>'Css link','required'=>false,'type'=>'varchar','defValue'=>''),
	'jscript_init_code'=>array('label'=>'Codice Javascript inizio BODY','required'=>false,'type'=>'varchar','defValue'=>''),
	'jscript_links'=>array('label'=>'Javascrip link','required'=>false,'type'=>'varchar','defValue'=>''),
	'jscript_last_links'=>array('label'=>'Ultimi Javascrips links','required'=>false,'type'=>'int','defValue'=>''),
	'base_tpl_page'=>array('label'=>'Template di base','required'=>false,'type'=>'int','defValue'=>''),
	'created'=>array('label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTimeIso,'validate'=>'datatimeiso'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','validate'=>'int','defValue'=>'0')
	);	
?>