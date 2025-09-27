<?php

/*
    framework siti html-PHP-Mysql
    copyright 2011 Roberto Mantovani
    http://www.robertomantovani.vr;it
    email: me@robertomantovani.vr.it
    site-images/folders.php v.2.6.3. 05/04/2016
*/
if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

switch (Core::$request->method) {

    case 'activeFold':
    case 'disactiveFold':
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -4), $tableFold, $App->id, ['label' => $_lang['categoria'],'attivata' => $_lang['attivata'],'disattivata' => $_lang['disattivata']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listFold');
        die();
        break;

    case 'deleteFold':
        if ($App->id > 0) {

            Sql::initQuery($App->tableItem, ['id'], [$App->id], 'id_folder = ?');
            $count = Sql::countRecord();
            if ($count > 0) {
                $_SESSION['message'] = '1|'.'Errore! La '.$App->labels['fold']['item'].' ha ancora '.$App->labels['fold']['sons'].' associat'.$App->labels['fold']['sonsSex'].'!';
                ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listFold');
                die();
            }

            /* preleva il titolo_it per cancellare la cartella */
            Sql::initQuery($App->tableFold, ['*'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            Sql::initQuery($tableFold, ['id'], [$App->id], 'id = ?');
            Sql::deleteRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }
            // cancella la cartella galleria
            if (file_exists($App->itemUploadPathDir.$App->itemOld->folder_name)) {
                rmdir($App->itemUploadPathDir.$App->itemOld->folder_name) or die('impossibile cancellare la cartella'.$App->itemUploadPathDir.$App->itemOld->folder_name);
            }
            $_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/', (string) $_lang['categoria'], (string) $_lang['%ITEM% cancellata'])).'!';
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listFold');

        } else {
            ToolsStrings::redirect(URL_SITE.'error/404');
        }
        break;

    case 'newFold':
        $App->pageSubTitle = 'inserisci '.$App->labels['fold']['item'];
        $App->viewMethod = 'formNew';
        break;

    case 'insertFold':
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            /* crea il folder name */
            $folder_name = SanitizeStrings::cleanTitleUrl($_POST['title_it']);
            /* controlla se esiste già una cartella con lo stesso nome */
            Sql::initQuery($tableFold, ['id'], [$folder_name], 'folder_name = ?');
            $count = Sql::countRecord();
            if (Core::$resultOp->error == 0) {
                if ($count == 0) {
                    if (!isset($_POST['active'])) {
                        $_POST['active'] = 1;
                    }
                    $_POST['folder_name'] = $folder_name;
                    /* controlla i campi obbligatori */
                    Sql::checkRequireFields($fieldsFold);
                    if (Core::$resultOp->error == 0) {
                        Sql::stripMagicFields($_POST);
                        /* memorizza nel db */
                        Sql::insertRawlyPost($fieldsFold, $tableFold);
                        if (Core::$resultOp->error == 0) {
                            /* crea la cartella galleria */
                            if (!file_exists($App->itemUploadPathDir.$folder_name)) {
                                mkdir($App->itemUploadPathDir.$folder_name) or die('impossibile creare la cartella'.$App->itemUploadPathDir.$folder_name);
                            }
                            @chmod($App->itemUploadPathDir.$folder_name, 0755);
                        }
                    }
                } else {
                    Core::$resultOp->message = 'Esiste già una '.$App->labels['fold']['item'].' con lo stesso nome!';
                    Core::$resultOp->error = 2;
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'inserisci '.$App->labels['fold']['item'];
            $App->viewMethod = 'formNew';
        } else {
            $App->viewMethod = 'list';
            Core::$resultOp->message = ucfirst((string) $App->labels['fold']['item']).' inserit'.$App->labels['fold']['itemSex'].'!';
        }
        break;

    case 'modifyFold':
        $App->pageSubTitle = 'modifica '.$App->labels['fold']['item'];
        $App->viewMethod = 'formMod';
        break;

    case 'updateFold':
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            $App->itemOld = new stdClass();
            /* crea il folder name */
            $newFold_name = SanitizeStrings::cleanTitleUrl($_POST['title_it']);
            /* preleva il folder name del categoria memorizzato prima */
            Sql::initQuery($tableFold, ['*'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                $oldFold_name = $App->itemOld->folder_name;
                /* controlla se esiste già una cartella con lo stesso nome */
                Sql::initQuery($tableFold, ['id'], [$newFold_name], 'folder_name = ?');
                $count = Sql::countRecord();
                if (Core::$resultOp->error == 0) {
                    if ($oldFold_name == $newFold_name || ($oldFold_name != $newFold_name && $count == 0)) {
                        if (!isset($_POST['active'])) {
                            $_POST['active'] = 0;
                        }
                        $_POST['folder_name'] = $newFold_name;
                        /* controlla i campi obbligatori */
                        Sql::checkRequireFields($fieldsFold);
                        if (Core::$resultOp->error == 0) {
                            Sql::stripMagicFields($_POST);
                            /* memorizza nel db */
                            Sql::updateRawlyPost($fieldsFold, $tableFold, 'id', $App->id);
                            if (Core::$resultOp->error == 0) {
                                /* rinomina la cartella */
                                if ($oldFold_name != '') {
                                    if ($oldFold_name != '' && file_exists($App->itemUploadPathDir.$oldFold_name)) {
                                        rename($App->itemUploadPathDir.$oldFold_name, $App->itemUploadPathDir.$newFold_name) or die('impossibile rinominare la cartella');
                                    }
                                }
                                /* se cambia il folder name lo cambia nelle immagini associate */
                                if ($oldFold_name != $newFold_name) {
                                    $oldfolder = $oldFold_name.'/';
                                    $newfolder = $newFold_name.'/';
                                    Sql::initQuery($App->tableItem, ['folder_name'], [$newfolder,$oldfolder], 'folder_name = ?');
                                    Sql::updateRecord();
                                    if (Core::$resultOp->error == 0) {
                                        Core::$resultOp->messages[] = 'Ho cambiato il folder name alle '.$App->labels['fold']['sons'].' associat'.$App->labels['fold']['sonsSex'].'!';
                                    }
                                }
                            }
                        }
                    } else {
                        Core::$resultOp->message = 'Esiste già una '.$App->labels['fold']['item'].' con lo stesso nome!';
                        Core::$resultOp->error = 2;
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }

        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'modifica '.$App->labels['fold']['item'];
            $App->viewMethod = 'formMod';
        } else {
            if (isset($_POST['submitForm'])) {
                $App->viewMethod = 'list';
                Core::$resultOp->message = ucfirst((string) $App->labels['fold']['item']).' modificat'.$App->labels['fold']['itemSex'].'!';
            } else {
                if (isset($_POST['id'])) {
                    $App->id = $_POST['id'];
                    $App->pageSubTitle = 'modifica '.$App->labels['fold']['item'];
                    $App->viewMethod = 'formMod';
                    Core::$resultOp->message = 'Modifiche applicate!';
                } else {
                    $App->viewMethod = 'formNew';
                    $App->pageSubTitle = 'inserisci '.$App->labels['fold']['item'];
                }
            }
        }
        break;

    case 'pageFold':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'messageFold':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'listFold':
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
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $fieldsFold);
        }
        $App->templateApp = 'formFolders.tpl.php';
        $App->methodForm = 'insertFold';
        break;

    case 'formMod':
        $App->item = new stdClass();
        Sql::initQuery($tableFold, ['*'], [$App->id], 'id = ?');
        if (Core::$resultOp->error == 0) {
            $App->item = Sql::getRecord();
        }
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $fieldsFold);
        }
        $App->templateApp = 'formFolders.tpl.php';
        $App->methodForm = 'updateFold';
        break;

    case 'list':

        $App->items = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields = ['c.*','(SELECT COUNT(i.id) FROM '.$App->tableItem.' AS i WHERE i.id_folder = c.id) AS numimages'];
        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        $clause = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $fieldsFold, '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $sessClause;
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($tableFold.' AS c', $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        if (Core::$resultOp->error <> 1) {
            $App->items = Sql::getRecords();
        }
        $App->pagination = Utilities::getPagination($App->page, Sql::getTotalsItems(), $App->itemsForPage);
        $App->pageSubTitle = 'lista delle '.$App->labels['fold']['items'].' del sito';
        $App->templateApp = 'listFolders.tpl.php';
        break;

    default:
        break;
}
