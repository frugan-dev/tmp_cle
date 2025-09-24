<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/faq/config.inc.php v.4.5.1. 21/11/2018
*/

$App->params = new stdClass();
$App->params->label = "Domande poste frequentemente";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('faq'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

$App->params->codeVersion = ' 4.5.1.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->module_has_config = 1;

$App->params->tableRif =  DB_TABLE_PREFIX.'faq';

/* ITEM */
$App->params->ordersType['item'] = 'ASC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."faq/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."faq/";
$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'users_id'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_cat'=>array('label'=>'ID Cat','required'=>false,'type'=>'int|8','defValue'=>'0'),
	'ordering'=>array('label'=>$_lang['ordinamento'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>0),
	'tags_id'=>array('label'=>'Id Tags','searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_read'=>array('label'=>$_lang['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>$_lang['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	
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
	$required = ($lang == $_lang['user'] ? true : false);
	$App->params->fields['item']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar|255');
	$App->params->fields['item']['content_'.$lang] = array('label'=>'Contenuto '.$lang,'searchTable'=>true,'required'=>false,'type'=>'mediumtext');
}

// CONFIGURAZIONE
$App->params->uploadPaths['conf'] = ADMIN_PATH_UPLOAD_DIR."faq/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."faq/";
$App->params->tables['conf'] = Config::$DatabaseTables['faq config'];
$App->params->fields['conf'] = Config::$DatabaseTablesFields['faq config'];
?>