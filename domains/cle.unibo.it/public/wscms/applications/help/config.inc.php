<?php
/*	wscms/help/config.inc.php v.3.5.4. 30/07/2019 */

$App->params = new stdClass();
$App->params->label = 'Aiuto';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','label','help_small','help'),array('help'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj; 

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPathDirs = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

/* ITEM */
$App->params->ordersType['item'] = 'ASC';
$App->params->tables['item'] = DB_TABLE_PREFIX.'help';
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'ordering'=>array('label'=>'Ordine','searchTable'=>false,'required'=>false,'type'=>'int|8'),
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
    ),   
);		
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fields['item']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'text');
	$App->params->fields['item']['content_'.$lang] = array('label'=>$_lang['contenuto'].'  '.$lang,'searchTable'=>true,'required'=>false,'type'=>'mediumtext');
	}
?>