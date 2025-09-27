<?php

/* wscms/users/config.inc.php v.3.5.4. 28/03/2019 */

$App->params = new stdClass();
$App->params->label = 'Utenti';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules', ['label','help_small','help'], ['users'], 'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) {
    $App->params = $obj;
}

$App->params->tables = [];
$App->params->fields = [];
$App->params->uploadPathDirs = [];
$App->params->uploadDirs = [];
$App->params->ordersType = [];

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';
$App->params->tables['item'] = DB_TABLE_PREFIX.'users';
$App->params->fields['item'] = [
'id' => ['label' => 'ID','required' => false,'type' => 'autoinc','primary' => true],
    'username' => ['label' => 'Username','searchTable' => true,'required' => true,'type' => 'varchar'],
    'password' => ['label' => 'Password','searchTable' => false,'required' => false,'type' => 'password'],
    'name' => ['label' => 'Nome','searchTable' => true,'required' => false,'type' => 'varchar'],
    'surname' => ['label' => 'Cognome','searchTable' => true,'required' => false,'type' => 'varchar'],
    'street' => ['label' => 'Via','searchTable' => false,'required' => false,'type' => 'varchar'],
    'city' => ['label' => 'Città','searchTable' => false,'required' => false,'type' => 'varchar'],
    'zip_code' => ['label' => 'C.A.P.','searchTable' => false,'required' => false,'type' => 'varchar'],
    'province' => ['label' => 'Provincia','searchTable' => false,'required' => false,'type' => 'varchar'],
    'state' => ['label' => 'Stato','searchTable' => false,'required' => false,'type' => 'varchar'],
    'telephone' => ['label' => 'Telefono','searchTable' => false,'required' => false,'type' => 'varchar'],
    'email' => ['label' => 'Email','searchTable' => true,'required' => true,'type' => 'varchar'],
    'mobile' => ['label' => 'Cellulare','searchTable' => true,'required' => false,'type' => 'varchar'],
    'fax' => ['label' => 'Fax','searchTable' => true,'required' => false,'type' => 'varchar'],
    'skype' => ['label' => 'Skype','searchTable' => true,'required' => false,'type' => 'varchar'],
    'template' => ['label' => 'Template','searchTable' => true,'type' => 'varchar'],
    'avatar' => ['label' => 'Avatar','searchTable' => false,'type' => 'blob'],
    'avatar_info' => ['label' => 'Avatar Info','searchTable' => false,'type' => 'varchar'],
    'levels_id' => ['label' => 'Livello','searchTable' => false,'type' => 'ind'],
    'is_root' => ['label' => 'Root','searchTable' => false,'type' => 'varchar','defValue' => 0],
    'hash' => ['label' => 'Hash','searchTable' => false,'type' => 'varchar'],
    'created'									        => [
        'label'									        => Config::$langVars['creazione'],
        'searchTable'							        => false,
        'required'								        => false,
        'type'									        => 'datatime',
        'defValue'								        => Config::$nowDateTimeIso,
        'validate'								        => 'datetimeiso',
        'forcedValue'              				        => Config::$nowDateTimeIso,
    ],
    'active'									        => [
        'label'									        => Config::$langVars['attiva'],
        'required'								        => false,
        'type'									        => 'int|1',
        'validate'			    				        => 'int',
        'defValue'								        => '0',
        'forcedValue'              				        => 1,
    ],
];
