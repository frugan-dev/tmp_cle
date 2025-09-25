<?php
/* wscms/contacts/config.inc.php v.3.5.4. 31/07/2019 */

$App->params = new stdClass();
$App->params->label = "Contatti";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',['label','help_small','help'],['contacts'],'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

/* configurazione */
$App->params->applicationName = Core::$request->action;

$App->params->databases = [];
$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPathDirs = [];
$App->params->uploadDirs = [];
$App->params->ordersType = [];

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