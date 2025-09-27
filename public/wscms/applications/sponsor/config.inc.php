<?php

/* wscms/sponsor/config.inc.php v.1.0.0 28/06/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules', ['help_small','help'], ['sponsor'], 'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) {
    $App->params = $obj;
}

$App->params->subcategories = 0;
$App->params->categories = 0;
$App->params->item_images = 0;
$App->params->item_files = 0;

$App->params->codeVersion = ' 1.0.0.';
$App->params->pageTitle = 'Sponsor';
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> Sponsor</li>';

$App->params->itemUploadPathDir = ADMIN_PATH_UPLOAD_DIR.'sponsor/';
$App->params->itemUploadDir = UPLOAD_DIR.'sponsor/';
$App->params->cateUploadPathDir = PATH_UPLOAD_DIR.'sponsor/categories/';
$App->params->cateUploadDir = UPLOAD_DIR.'sponsor/categories/';
$App->params->scatUploadPathDir = PATH_UPLOAD_DIR.'sponsor/subcategories/';
$App->params->scatUploadDir = UPLOAD_DIR.'sponsor/subcategories/';
$App->params->ifilUploadPathDir = PATH_UPLOAD_DIR.'sponsor/files/';
$App->params->ifilUploadDir = UPLOAD_DIR.'sponsor/files/';

$App->params->orderingType = 'ASC';
$App->params->targets = ['_self','_blank','_parent','_top'];

$App->params->labels['item'] = ['item' => 'sponsor','itemSex' => 'o','items' => 'sponsor','itemsSex' => 'i','owner' => 'Categoria','ownerSex' => 'a','owners' => 'Categorie','ownersSex' => 'e'];
$App->params->labels['cate'] = ['item' => 'categoria','itemSex' => 'a','items' => 'categorie','itemsSex' => 'e','son' => 'partner','sonSex' => 'o','sons' => 'sponsor','sonsSex' => 'i','owner' => '','ownerSex' => '','owners' => '','ownersSex' => ''];
$App->params->labels['ifil'] = ['item' => 'file','itemSex' => 'o','items' => 'files','itemsSex' => 'i','owner' => 'sponsor','ownerSex' => 'a','owners' => 'notizie','ownersSex' => 'e'];

$App->params->tableItem = DB_TABLE_PREFIX.'sponsor';
$App->params->tableCate = DB_TABLE_PREFIX.'sponsor_cat';
$App->params->tableIfil = DB_TABLE_PREFIX.'sponsor_files';

$App->params->fieldsItem = [
    'id' => ['label' => 'ID','required' => false,'type' => 'autoinc','primary' => true],
    'id_cat' => ['label' => 'ID Cat','required' => false,'type' => 'int'],
    'filename' => ['label' => 'Nome File','searchTable' => false,'required' => false,'type' => 'varchar'],
    'org_filename' => ['label' => '','searchTable' => true,'required' => false,'type' => 'varchar'],
    'ordering' => ['label' => 'Ordine','searchTable' => false,'required' => false,'type' => 'int'],
    'url' => ['URL' => 'Alias','searchTable' => true,'required' => false,'type' => 'varchar'],
    'target' => ['label' => 'Target','searchTable' => false,'required' => false,'type' => 'varchar'],
    'created' => ['label' => 'Creazione','searchTable' => false,'required' => false,'type' => 'datatime'],
    'active' => ['label' => 'Attiva','required' => false,'type' => 'int','defValue' => 0],
    ];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == 'it' ? true : false);
    $App->params->fieldsItem['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar'];
    $App->params->fieldsItem['content_'.$lang] = ['label' => 'Contenuto '.$lang,'searchTable' => true,'required' => false,'type' => 'text'];
}

$App->params->fieldsCate = [
        'id' => ['label' => 'ID','required' => false,'type' => 'autoinc','primary' => true],
        'created' => ['label' => 'Creazione','searchTable' => false,'required' => false,'type' => 'datatime'],
        'active' => ['label' => 'Attiva','required' => false,'type' => 'int','defValue' => 0],
        ];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == 'it' ? true : false);
    $App->params->fieldsCate['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar'];
}

$App->params->fieldsIfil = [
    'id' => ['label' => 'ID','required' => false,'type' => 'autoinc','primary' => true],
    'id_owner' => ['label' => 'IDOwner','required' => false,'searchTable' => false,'type' => 'int'],
    'filename' => ['label' => 'File','searchTable' => false,'required' => true,'type' => 'varchar'],
    'org_filename' => ['label' => 'Nome Originale','searchTable' => true,'required' => false,'type' => 'varchar'],
    'extension' => ['label' => 'Ext','searchTable' => true,'required' => false,'type' => 'varchar'],
    'size' => ['label' => 'Dimensione','searchTable' => true,'required' => false,'type' => 'varchar'],
    'type' => ['label' => 'Tipo','searchTable' => true,'required' => false,'type' => 'varchar'],
    'created' => ['label' => 'Creazione','searchTable' => false,'required' => false,'type' => 'datatime'],
    'active' => ['label' => 'Attiva','required' => false,'type' => 'int','defValue' => 0],
    ];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == 'it' ? true : false);
    $App->params->fieldsIfil['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar'];
}
