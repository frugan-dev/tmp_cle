<?php

/* wscms/galleriesimages/config.inc.php v.4.0.0. 06/12/2021 */

$App->params = new stdClass();
$App->params->label = 'Gallery';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules', ['name','label','help_small','help'], ['galleriesimages'], 'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) {
    $App->params = $obj;
}

$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->orderTypes = [];
$App->params->tables = [];
$App->params->fields = [];

$App->params->codeVersion = ' v.4.0.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'galleriesimages';

$App->params->orderTypes['cate'] = 'DESC';
$App->params->tables['cate'] = Config::$DatabaseTables['galleriesimages categories'];
$App->params->fields['cate'] = Config::$DatabaseTablesFields['galleriesimages categories'];

$App->params->orderTypes['imag'] = 'DESC';
$App->params->uploadPaths['imag'] = ADMIN_PATH_UPLOAD_DIR.'galleriesimages/';
$App->params->uploadDirs['imag'] = UPLOAD_DIR.'galleriesimages/';
$App->params->orderTypes['imag'] = 'DESC';
$App->params->tables['imag'] = Config::$DatabaseTables['galleriesimages'];
$App->params->fields['imag'] = Config::$DatabaseTablesFields['galleriesimages'];
//ToolsStrings::dump($App->params->fields['imag']);

$App->params->orderTypes['tags'] = 'DESC';
$App->params->tables['tags'] = Config::$DatabaseTables['galleriesimages tags'];
$App->params->fields['tags'] = Config::$DatabaseTablesFields['galleriesimages tags'];
