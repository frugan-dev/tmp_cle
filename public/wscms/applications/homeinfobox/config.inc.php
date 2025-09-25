<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * wscms/homeinfobox/config.inc.php v.4.0.0. 15/12/2022
*/

$App->params = new stdClass();
$App->params->label = "Info box";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['name','label','help_small','help'],['homeinfobox'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->orderTypes = [];

$App->params->codeVersion = ' 4.0.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'faq';

/* ITEM */
$App->params->orderTypes['item'] = 'ASC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."homeinfobox/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."homeinfobox/";
$App->params->tables['item'] = Config::$DatabaseTables['home info box'];
$App->params->fields['item'] = Config::$DatabaseTablesFields['home info box'];
?>