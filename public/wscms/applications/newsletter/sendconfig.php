<?php

/* wscms/newsletter/config.php v.1.0.1. 14/08/2016 */

if (Core::$request->method == 'updateConfig') {
    if (!isset($App->items)) {
        $App->items = new stdClass();
    }
    Sql::initQuery($App->tableConf, ['*']);
    Sql::setClause('active = 1');
    Sql::setOrder('ordering ASC');
    $App->items = Sql::getRecords();
    if (Core::$resultOp->error == 0) {
        if (is_array($App->items) && count($App->items) > 0) {
            foreach ($App->items as $value) {
                if ($value->type == 'input') {
                    $key = $value->name;
                    $id = $value->id;

                    $fields = [];
                    $fieldsValues = [];
                    foreach ($globalSettings['languages'] as $lang) {
                        $stringFieldValueRif = 'value_'.$lang;
                        $fieldValue = $value->$stringFieldValueRif;
                        if (isset($_POST[$stringFieldValueRif][$key])) {
                            $fieldValue = $_POST[$stringFieldValueRif][$key];
                        }
                        $fields[] = $stringFieldValueRif;
                        $fieldsValues[] = $fieldValue;
                    }
                    $fieldsValues[] = $id;
                    /* aggiorna db */
                    Sql::initQuery($App->tableConf, $fields, $fieldsValues, 'id = ?');
                    Sql::updateRecord();
                    if (Core::$resultOp->error == 0) {
                        Core::$resultOp->message = 'Impostazioni aggiornate!';
                    }
                }
            }
        }
    }
}

/* legge la tabella */
$App->items = new stdClass();
Sql::initQuery($App->tableConf, ['*']);
Sql::setClause('active = 1');
Sql::setOrder('ordering ASC');
if (Core::$resultOp->error <> 1) {
    $App->items = Sql::getRecords();
}
//ToolsStrings::dump($App->items);
$App->pageSubTitle = 'la configurazione per gestione newsletter';
$App->templateApp = 'formCono.tpl.php';
