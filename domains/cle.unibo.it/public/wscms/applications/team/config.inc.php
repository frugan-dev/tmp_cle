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
$App->params->label = "Staff";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['name','label','help_small','help'],['team'],'name = ?');
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

$App->params->module_has_config = 1;

$App->params->tableRif =  DB_TABLE_PREFIX.'team';

// TEAM
$App->params->orderTypes['team'] = 'ASC';
$App->params->uploadPaths['team'] = ADMIN_PATH_UPLOAD_DIR."team/";
$App->params->uploadDirs['team'] = UPLOAD_DIR."team/";
$App->params->tables['team'] = Config::$DatabaseTables['team'];
$App->params->fields['team'] = Config::$DatabaseTablesFields['team'];

// CONFIGURAZIONE
$App->params->uploadPaths['conf'] = ADMIN_PATH_UPLOAD_DIR."team/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."team/";
$App->params->tables['conf'] = Config::$DatabaseTables['team config'];
$App->params->fields['conf'] = Config::$DatabaseTablesFields['team config'];

//ToolsStrings::dump($App->params->fields['team']);
?>