<?php

/* wscms/menu/config.inc.php v.3.5.4. 08/07/2019 */

$App->params = new stdClass();
$App->params->label = 'Pagine';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules', ['label','help_small','help'], ['menu'], 'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) {
    $App->params = $obj;
}

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPaths = [];
$App->params->uploadDirs = [];
$App->params->orderTypes = [];

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'menu';

/* ITEMS */
$App->params->orderTypes['item'] = 'ASC';

$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'users_id' => ['label' => $_lang['proprietario'],'searchTable' => false,'required' => true,'type' => 'int|8','defValue' => $App->userLoggedData->id],
    'parent' => ['label' => 'Parent','searchTable' => false,'required' => false,'type' => 'int|8','defValue' => 0],
    'ordering' => ['label' => $_lang['ordinamento'],'required' => false,'type' => 'int|8','validate' => 'int','defValue' => 1],
    'type' => ['label' => 'Tipo','searchTable' => false,'required' => false,'type' => 'varchar|50'],
    'alias' => ['label' => 'Alias','searchTable' => true,'required' => true,'type' => 'varchar255'],
    'url' => ['URL' => 'Alias','searchTable' => true,'required' => false,'type' => 'varchar255'],
    'target' => ['label' => 'Target','searchTable' => true,'required' => false,'type' => 'varchar|20'],
    'access_read' => ['label' => $_lang['accesso lettura'],'searchTable' => false,'required' => false,'type' => 'text','defValue' => 'none'],
    'access_write' => ['label' => $_lang['accesso scrittura'],'searchTable' => false,'required' => false,'type' => 'text','defValue' => 'none'],
    'created'                                   =>  [
        'label'                                 => Config::$langVars['creazione'],
        'searchTable'                           => false,
        'required'                              => false,
        'type'                                  => 'datatime',
        'defValue'                              => Config::$nowDateTimeIso,
        'forcedValue'                           => Config::$nowDateTimeIso,
    ],
    'active'                                    =>  [
        'label'                                 => Config::$langVars['attiva'],
        'required'                              => false,
        'type'                                  => 'int|1',
        'defValue'                              => 1,
        'forcedValue'                           => 1,
    ],
];
foreach ($globalSettings['languages'] as $lang) {
    $required = ($lang == $_lang['user'] ? true : false);
    $App->params->fields['item']['title_'.$lang] = ['label' => $_lang['titolo'].' '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar|255'];
}

$App->params->orderTypes['menu'] = $App->params->orderTypes['item'];
$App->params->tables['menu'] = $App->params->tables['item'];
$App->params->fields['menu'] = $App->params->fields['item'];
