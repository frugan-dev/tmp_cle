<?php

/*	framework siti html-PHP-Mysql	copyright 2011 Roberto Mantovani	http://www.robertomantovani.vr;it	email: me@robertomantovani.vr.it	slides-home-rev/index.php v.2.6.3. 11/04/2016
*/

Sql::setDebugMode(0);

include_once(PATH.'application/'.Core::$request->action.'/config.inc.php');
include_once(PATH.'application/'.Core::$request->action.'/module.class.php');

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb .= $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->table = $App->params->table;
$App->fields = $App->params->fields;

$App->orderingType =  $App->params->orderingType;
$App->labels =  $App->params->labels;

$App->uploadPathDir = $App->params->uploadPathDir;
$App->uploadDir = $App->params->uploadDir;

$Module = new Module(Core::$request->action, $App->table);

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) {
    $App->id = intval($_POST['id']);
}

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

switch (Core::$request->method) {
    case 'moreOrdering':
        $Utilities::increaseFieldOrdering($App->id, ['table' => $App->table,'orderingType' => $App->orderingType,'parent' => false,'sexSuffix' => $App->labels['main']['itemSex'],'labelItem' => ucfirst((string) $App->labels['main']['item'])]);
        $App->viewMethod = 'list';
        break;
    case 'lessOrdering':
        $Utilities::decreaseFieldOrdering($App->id, ['table' => $App->table,'orderingType' => $App->orderingType,'parent' => false,'sexSuffix' => $App->labels['main']['itemSex'],'labelItem' => ucfirst((string) $App->labels['main']['item'])]);
        $App->viewMethod = 'list';
        break;

    case 'active':
    case 'disactive':
        Sql::manageFieldActive(Core::$request->method, $App->table, $App->id, ucfirst((string) $App->labels['main']['item']));
        $App->viewMethod = 'list';
        break;

    case 'delete':
        if ($App->id > 0) {
            if (!isset($App->itemOld)) {
                $App->itemOld = new stdClass();
            }
            Sql::initQuery($App->table, ['filename'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                Sql::initQuery($App->table, ['id'], [$App->id], 'id = ?');
                Sql::deleteRecord();
                if (Core::$resultOp->error == 0) {
                    if (isset($App->itemOld->filename) && file_exists($App->uploadPathDir.$App->itemOld->filename)) {
                        @unlink($App->uploadPathDir.$App->itemOld->filename);
                    }
                    Core::$resultOp->message = ucfirst((string) $App->labels['main']['item']).' cancellat'.$App->labels['main']['itemSex'].'!';
                }
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'new':
        $App->pageSubTitle = 'inserisci '.$App->labels['main']['item'];
        $App->viewMethod = 'formNew';
        break;

    case 'insert':
        if ($_POST) {
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['ordering'])) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->table, 'ordering', '') + 1;
            }
            /* preleva il filename dal form */
            ToolsUpload::setFilenameFormat(['jpg','png']);
            ToolsUpload::getFilenameFromForm();
            $_POST['filename'] = ToolsUpload::getFilenameMd5();
            $_POST['org_filename'] = ToolsUpload::getOrgFilename();
            if (Core::$resultOp->error == 0) {
                /* controlla i campi obbligatori */
                Sql::checkRequireFields($App->fields);
                if (Core::$resultOp->error == 0) {
                    Sql::stripMagicFields($_POST);
                    /* memorizza nel db */
                    Sql::insertRawlyPost($App->fields, $App->table);
                    if (Core::$resultOp->error == 0) {
                        /* sposto il file */
                        if ($_POST['filename'] != '') {
                            move_uploaded_file(ToolsUpload::getTempFilename(), $App->uploadPathDir.$_POST['filename']) or die('Errore caricamento file');
                        }
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'inserisci '.$App->labels['main']['item'];
            $App->viewMethod = 'formNew';
        } else {
            $App->viewMethod = 'list';
            Core::$resultOp->message = ucfirst((string) $App->labels['main']['item']).' inserit'.$App->labels['main']['itemSex'].'!';
        }
        break;

    case 'modify':
        $App->pageSubTitle = 'modifica '.$App->labels['main']['item'];
        $App->viewMethod = 'formMod';
        break;

    case 'update':
        if ($_POST) {
            $App->itemOld = new stdClass();
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['ordering'])) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->table, 'ordering', '') + 1;
            }
            /* preleva filename vecchio */
            Sql::initQuery($App->table, ['filename','org_filename'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                /* preleva il filename dal form */
                ToolsUpload::setFilenameFormat(['jpg','png']);
                ToolsUpload::getFilenameFromForm();
                if (Core::$resultOp->error == 0) {
                    $_POST['filename'] = ToolsUpload::getFilenameMd5();
                    $_POST['org_filename'] = ToolsUpload::getOrgFilename();
                    $uploadFilename = $_POST['filename'];
                    /* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
                    if ($_POST['filename'] == '' && $App->itemOld->filename != '') {
                        $_POST['filename'] = $App->itemOld->filename;
                    }
                    if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') {
                        $_POST['org_filename'] = $App->itemOld->org_filename;
                    }
                    /* controlla i campi obbligatori */
                    Sql::checkRequireFields($App->fields);
                    if (Core::$resultOp->error == 0) {
                        Sql::stripMagicFields($_POST);
                        Sql::updateRawlyPost($App->fields, $App->table, 'id', $App->id);
                        if (Core::$resultOp->error == 0) {
                            if ($uploadFilename != '') {
                                move_uploaded_file(ToolsUpload::getTempFilename(), $App->uploadPathDir.$uploadFilename) or die('Errore caricamento file');
                                /* cancella l'immagine vecchia */
                                if (isset($App->itemOld->filename) && file_exists($App->uploadPathDir.$App->itemOld->filename)) {
                                    @unlink($App->uploadPathDir.$App->itemOld->filename);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error == 1) {
            $App->pageSubTitle = 'modifica '.$App->labels['main']['item'];
            $App->viewMethod = 'formMod';
        } else {
            if (isset($_POST['submitForm'])) {
                $App->viewMethod = 'list';
                Core::$resultOp->message = ucfirst((string) $App->labels['main']['item']).' modificat'.$App->labels['main']['itemSex'].'!';
            } else {
                if (isset($_POST['id'])) {
                    $App->id = $_POST['id'];
                    $App->pageSubTitle = 'modifica '.$App->labels['main']['item'];
                    $App->viewMethod = 'formMod';
                    Core::$resultOp->message = 'Modifiche applicate!';
                } else {
                    $App->viewMethod = 'formNew';
                    $App->pageSubTitle = 'inserisci '.$App->labels['main']['item'];
                }
            }
        }
        break;

    case 'page':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->action, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'message':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'list':
        $App->viewMethod = 'list';
        break;

    default:
        $App->viewMethod = 'list';
        break;
}

switch ((string)$App->viewMethod) {
    case 'formNew':
        $App->item = new stdClass();
        $App->item->created = $App->nowDateTime;
        $App->item->active = 1;
        $App->item->ordering = Sql::getMaxValueOfField($App->table, 'ordering', '') + 1;
        $App->item->filenameRequired = true;
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->fields);
        }
        $App->templatePage = 'form.tpl.php';
        $App->methodForm = 'insert';
        break;

    case 'formMod':
        $App->item = new stdClass();
        Sql::initQuery($App->table, ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->fields);
        }
        $App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
        $App->templatePage = 'form.tpl.php';
        $App->methodForm = 'update';
        break;

    case 'list':
        $App->items = new stdClass();
        $App->item = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields = ['*'];
        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        $clause = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->fields, '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $sessClause;
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($App->table, $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        Sql::setOrder('ordering '.$App->orderingType);
        if (Core::$resultOp->error <> 1) {
            $App->items = Sql::getRecords();
        }
        $App->pagination = Utilities::getPagination($App->page, Sql::getTotalsItems(), $App->itemsForPage);
        $App->pageSubTitle = 'lista delle '.$App->labels['main']['items'];
        $App->templatePage = 'list.tpl.php';
        break;

    default:
        break;
}

/* imposta le variabili Savant */
$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'application/'.Core::$request->action.'/module.js"></script>';
