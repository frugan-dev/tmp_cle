<?php
/* wscms/contacts/config.inc.php v.3.5.4. 31/07/2019 */

$App->params = new stdClass();
$App->params->label = "Contatti";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('contacts'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

/* configurazione */
$App->params->applicationName = Core::$request->action;

$App->params->databases = array();
$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPathDirs = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

/* CONFIGURATIONS */

// CONFIGURAZIONE
$App->params->uploadPaths['conf'] = ADMIN_PATH_UPLOAD_DIR."contacts/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."contacts/";
$App->params->ordersType['conf'] = 'DESC';
$App->params->tables['conf'] = Config::$DatabaseTables['contacts config'];
$App->params->fields['conf'] = Config::$DatabaseTablesFields['contacts config'];

?>