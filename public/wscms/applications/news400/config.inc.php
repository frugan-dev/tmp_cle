<?php

/* wscms/news/config.inc.php v.3.5.4. 10/09/2019 */

$App->params = new stdClass();
$App->params->label = 'Notizie';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules', ['label','help_small','help'], ['news'], 'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) {
    $App->params = $obj;
}

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->ordersType = [];

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'news';
/* ITEM */
$App->params->ordersType['item'] = 'DESC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR.'news/';
$App->params->uploadDirs['item'] = UPLOAD_DIR.'news/';
$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'id_user' => ['label' => $_lang['proprietario'],'searchTable' => false,'required' => true,'type' => 'int|8','defValue' => $App->userLoggedData->id],
    'id_cat' => ['label' => 'ID Cat','required' => false,'type' => 'int|8','defValue' => '0'],
    'datatimeins' => ['label' => $_lang['data'],'searchTable' => false,'required' => true,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'convertdatatimeformattoiso'],
    'datatimescaini' => ['label' => $_lang['inizio scadenza'],'searchTable' => false,'required' => false,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'convertdatatimeformattoiso'],
    'datatimescaend' => ['label' => $_lang['fine scadenza'],'searchTable' => false,'required' => false,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'convertdatatimeformattoiso'],
    'filename' => ['label' => 'Nome File','searchTable' => false,'required' => false,'type' => 'varchar|255'],
    'org_filename' => ['label' => '','searchTable' => true,'required' => false,'type' => 'varchar|255'],
    'embedded' => ['label' => 'Embedded','searchTable' => false,'required' => false,'type' => 'text','defValue' => ''],
    'scadenza' => ['label' => 'Scadenza','searchTable' => false,'required' => false,'type' => 'int|1','defValue' => '0'],
    'access_type' => ['label' => $_lang['tipo accesso'],'searchTable' => false,'required' => false,'type' => 'int|1','defValue' => '0'],
    'access_read' => ['label' => $_lang['accesso lettura'],'searchTable' => false,'required' => false,'type' => 'text','defValue' => 'none'],
    'access_write' => ['label' => $_lang['accesso scrittura'],'searchTable' => false,'required' => false,'type' => 'text','defValue' => 'none'],
    'created' => ['label' => $_lang['creazione'],'searchTable' => false,'required' => false,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'datatimeiso'],
    'active' => ['label' => 'Attiva','required' => false,'type' => 'int','validate' => 'int|1','defValue' => '0'],
    ];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == $_lang['user'] ? true : false);
    $App->params->fields['item']['meta_title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];
    $App->params->fields['item']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];
    $App->params->fields['item']['meta_keyword_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];
    $App->params->fields['item']['title_seo_'.$lang] = ['label' => 'Titolo SEO '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];

    $App->params->fields['item']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar|255'];
    $App->params->fields['item']['summary_'.$lang] = ['label' => 'Sommario '.$lang,'searchTable' => true,'required' => false,'type' => 'text'];
    $App->params->fields['item']['content_'.$lang] = ['label' => 'Contenuto '.$lang,'searchTable' => true,'required' => false,'type' => 'mediumtext'];
}

/* CATEGORIE */
$App->params->ordersType['cate'] = 'ASC';
$App->params->uploadPaths['cate'] = ADMIN_PATH_UPLOAD_DIR.'news/categories/';
$App->params->uploadDirs['cate'] = UPLOAD_DIR.'news/categories/';
$App->params->tables['cate'] = $App->params->tableRif.'_categories';
$App->params->fields['cate'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'alias' => ['label' => 'Alias','searchTable' => true,'required' => false,'type' => 'varchar|255'],
    'ordering' => ['label' => 'Ord','required' => false,'type' => 'int|8','defValue' => 1],
    'filename' => ['label' => 'Immagine','searchTable' => true,'required' => false,'type' => 'varchar|255'],
    'org_filename' => ['label' => 'Nome Originale','searchTable' => true,'required' => false,'type' => 'varchar|255'],
    'id_tags' => ['label' => 'Id Tags','searchTable' => true,'required' => false,'type' => 'text'],
    'created' => ['label' => $_lang['creazione'],'searchTable' => false,'required' => false,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'datatimeiso'],
    'active' => ['label' => $_lang['attivazione'],'required' => false,'type' => 'int|1','validate' => 'int','defValue' => '0'],
    ];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == 'it' ? true : false);
    $App->params->fields['cate']['title_meta_'.$lang] = ['label' => 'Titolo META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];
    $App->params->fields['cate']['title_seo_'.$lang] = ['label' => 'Titolo SEO '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar'];
    $App->params->fields['cate']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar'];
}

/* TAGS */
$App->params->ordersType['tags'] = 'ASC';
$App->params->tables['tags'] = $App->params->tableRif.'_tags';
$App->params->fields['tags'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'ordering' => ['label' => $_lang['ordinamento'],'required' => false,'type' => 'int|8','defValue' => 1],
    'created' => ['label' => $_lang['creazione'],'searchTable' => false,'required' => false,'type' => 'datatime','defValue' => $App->nowDateTime,'validate' => 'datatimeiso'],
    'active' => ['label' => $_lang['attivazione'],'required' => false,'type' => 'int|1','validate' => 'int','defValue' => '0'],
];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == $_lang['user'] ? true : false);
    $App->params->fields['tags']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar|255'];
}

/* RESOURCES */
$App->params->tables['resources'] = $App->params->tableRif.'_resources';
$App->params->fields['resources'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'autoinc','primary' => true],
    'id_owner' => ['label' => 'IDOwner','required' => true,'searchTable' => false,'type' => 'int'],
    'resource_type' => ['label' => 'Type resource','required' => true,'searchTable' => false,'type' => 'int'],
    'filename' => ['label' => 'File','searchTable' => false,'required' => true,'type' => 'varchar'],
    'org_filename' => ['label' => 'Nome Originale','searchTable' => true,'required' => false,'type' => 'varchar'],
    'extension' => ['label' => 'Ext','searchTable' => false,'required' => false,'type' => 'varchar40'],
    'code' => ['label' => 'Code','searchTable' => false,'required' => false,'type' => 'text'],
    'size_file' => ['label' => 'Dimensione','searchTable' => false,'required' => false,'type' => 'varchar20'],
    'size_image' => ['label' => 'Dimensione','searchTable' => false,'required' => false,'type' => 'varchar40'],
    'type' => ['label' => 'Tipo','searchTable' => true,'required' => false,'type' => 'varchar100'],
    'ordering' => ['label' => 'Ordinamento','searchTable' => false,'required' => false,'type' => 'int'],
    'created' => ['label' => 'Creazione','searchTable' => false,'required' => false,'type' => 'datatime'],
    'active' => ['label' => 'Attiva','required' => false,'type' => 'int','defValue' => 0],
    ];
foreach ($globalSettings['languages'] as $lang) {
    $searchTable = true;
    $required = ($lang == $_lang['user'] ? true : false);
    $App->params->fields['resources']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => $searchTable,'required' => $required,'type' => 'varchar'];
    $App->params->fields['resources']['content_'.$lang] = ['label' => $_lang['contenuto'].'  '.$lang,'searchTable' => true,'required' => false,'type' => 'text','defValue' => ''];
}
/* ITEM IMAGES  type = 1 */
$App->params->uploadPaths['iimg'] = ADMIN_PATH_UPLOAD_DIR.'news/images/';
$App->params->uploadDirs['iimg'] = UPLOAD_DIR.'news/images/';
$App->params->ordersType['iimg'] = 'ASC';

/* ITEM FILES type = 2 */
$App->params->uploadPaths['ifil'] = ADMIN_PATH_UPLOAD_DIR.'news/files/';
$App->params->uploadDirs['ifil'] = UPLOAD_DIR.'news/files/';
$App->params->ordersType['ifil'] = 'ASC';

/* ITEM GALLERY type = 3 */
$App->params->uploadPaths['igal'] = ADMIN_PATH_UPLOAD_DIR.'news/images/';
$App->params->uploadDirs['igal'] = UPLOAD_DIR.'news/images/';
$App->params->ordersType['igal'] = 'ASC';

/* ITEM VIDEO  type = 4*/
$App->params->ordersType['ivid'] = 'ASC';
