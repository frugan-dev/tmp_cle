<?php

/* wscms/news/categories.php v.3.5.4. 10/09/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

if (Core::$request->method == 'listCate' && $App->id > 0) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'id_cat', $App->id);
}

/* GESTIONE TAG */
$App->tags = new stdClass();
Sql::initQuery($App->params->tables['tags'], ['*'], [], '');
Sql::setOptions(['fieldTokeyObj' => 'id']);
Sql::setOrder('ordering ASC');
$obj = Sql::getRecords();
if (Core::$resultOp->error > 0) {
    echo Core::$resultOp->message;
    die;
}
/* sistemo i dati */
$arr = [];
if (is_array($obj) && is_array($obj) && count($obj) > 0) {
    foreach ($obj as $key => $value) {
        $field = 'title_'.$_lang['user'];
        $value->title = $value->$field;
        $arr[$key] = $value;
    }
}
$App->tags = $arr;

switch (Core::$request->method) {
    case 'moreOrderingCate':
        Utilities::increaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['cate'],'orderingType' => $App->params->ordersType['cate'],'parent' => 0,'parentField' => 'id_cat','label' => ucfirst((string) $_lang['categoria']).' '.$_lang['spostata']]);
        $App->viewMethod = 'list';
        break;
    case 'lessOrderingCate':
        Utilities::decreaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['cate'],'orderingType' => $App->params->ordersType['cate'],'parent' => 0,'parentField' => 'id_cat','label' => ucfirst((string) $_lang['categoria']).' '.$_lang['spostata']]);
        $App->viewMethod = 'list';
        break;

    case 'activeCate':
    case 'disactiveCate':
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -4), $App->params->tables['cate'], $App->id, ['label' => $_lang['categoria'],'attivata' => $_lang['attivata'],'disattivata' => $_lang['disattivata']]);
        $App->viewMethod = 'list';
        break;

    case 'deleteCate':
        if ($App->id > 0) {
            $delete = true;

            /* controlla se ha voci associate */
            Sql::initQuery($App->params->tables['item'], ['id'], [$App->id], 'id_cat = ?');
            $count = Sql::countRecord();
            if ($count > 0) {
                Core::$resultOp->error = 2;
                Core::$resultOp->message = ucfirst((string) preg_replace('/%ITEM%/', (string) $_lang['voci'], (string) $_lang['Ci sono ancora %ITEM% associate!']));
                $delete = false;
            }

            if ($delete == true && Core::$resultOp->error == 0) {
                $App->itemOld = new stdClass();
                Sql::initQuery($App->params->tables['cate'], ['filename'], [$App->id], 'id = ?');
                $App->itemOld = Sql::getRecord();
                if (Core::$resultOp->error == 0) {
                    Sql::initQuery($App->params->tables['cate'], ['id'], [$App->id], 'id = ?');
                    Sql::deleteRecord();
                    if (Core::$resultOp->error == 0) {
                        if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['cate'].$App->itemOld->filename)) {
                            @unlink($App->params->uploadPaths['cate'].$App->itemOld->filename);
                        }
                        Core::$resultOp->message = ucfirst((string) preg_replace('/%ITEM%/', (string) $_lang['categoria'], (string) $_lang['%ITEM% cancellata'])).'!';
                    }
                }
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'newCate':
        $App->pageSubTitle = preg_replace('/%ITEM%/', (string) $_lang['categoria'], (string) $_lang['inserisci %ITEM%']);
        $App->viewMethod = 'formNew';
        break;

    case 'insertCate':
        if ($_POST) {
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            /* gestione automatica dell'ordering de in input = 0 */
            if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['cate'], 'ordering', '') + 1;
            }
            /* imposta alias */
            $_POST['alias'] = $Module->getAlias($App->id, $_POST['alias'], $_POST['title_'.$_lang['user']]);

            /* tagsId */
            if (isset($_POST['id_tags']) && is_array($_POST['id_tags'])) {
                $_POST['id_tags'] = implode(',', $_POST['id_tags']);
            } else {
                $_POST['id_tags'] = '';
            }
            /* end tagsId */

            /* preleva il filename dal form */
            ToolsUpload::setFilenameFormat($globalSettings['image type available']);
            ToolsUpload::getFilenameFromForm();
            $_POST['filename'] = ToolsUpload::getFilenameMd5();
            $_POST['org_filename'] = ToolsUpload::getOrgFilename();
            if (Core::$resultOp->error == 0) {

                /* parsa i post in base ai campi */
                Form::parsePostByFields($App->params->fields['cate'], $_lang, []);
                if (Core::$resultOp->error == 0) {
                    Sql::insertRawlyPost($App->params->fields['cate'], $App->params->tables['cate']);
                    if (Core::$resultOp->error == 0) {
                        //$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
                        /* sposto il file */
                        if ($_POST['filename'] != '') {
                            move_uploaded_file(ToolsUpload::getTempFilename(), $App->params->uploadPaths['cate'].$_POST['filename']) or die('Errore caricamento file');
                        }
                    }
                }

            }
        } else {
            Core::$resultOp->error = 1;
        }
        [$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getInsertRecordFromPostResults(
            0,
            Core::$resultOp,
            ''
        );
        break;

    case 'modifyCate':
        $App->pageSubTitle = preg_replace('/%ITEM%/', (string) $_lang['categoria'], (string) $_lang['modifica %ITEM%']);
        $App->viewMethod = 'formMod';
        break;

    case 'updateCate':
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            /* gestione automatica dell'ordering de in input = 0 */
            if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['cate'], 'ordering', '') + 1;
            }
            /* imposta alias */
            $_POST['alias'] = $Module->getAlias(0, $_POST['alias'], $_POST['title_'.$_lang['user']]);

            /* tagsId */
            if (isset($_POST['id_tags']) && is_array($_POST['id_tags'])) {
                $_POST['id_tags'] = implode(',', $_POST['id_tags']);
            } else {
                $_POST['id_tags'] = '';
            }
            /* end tagsId */

            /* preleva filename vecchio */
            Sql::initQuery($App->params->tables['cate'], ['filename','org_filename'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                /* preleva il filename dal form */
                ToolsUpload::setFilenameFormat($globalSettings['image type available']);
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
                    /* opzione cancella immagine */
                    if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == 1) {
                        if (file_exists($App->params->uploadPaths['prod'].$App->itemOld->filename)) {
                            @unlink($App->params->uploadPaths['prod'].$App->itemOld->filename);
                        }
                        $_POST['filename'] = '';
                        $_POST['org_filename'] = '';
                    }

                    /* parsa i post in base ai campi */
                    Form::parsePostByFields($App->params->fields['cate'], $_lang, []);
                    if (Core::$resultOp->error == 0) {
                        Sql::updateRawlyPost($App->params->fields['cate'], $App->params->tables['cate'], 'id', $App->id);
                        if (Core::$resultOp->error == 0) {
                            if ($uploadFilename != '') {
                                move_uploaded_file(ToolsUpload::getTempFilename(), $App->params->uploadPaths['cate'].$uploadFilename) or die('Errore caricamento file');
                                /* cancella l'immagine vecchia */
                                if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['cate'].$App->itemOld->filename)) {
                                    @unlink($App->params->uploadPaths['cate'].$App->itemOld->filename);
                                }
                            }
                        }
                    }

                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        [$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getUpdateRecordFromPostResults(
            $App->id,
            Core::$resultOp,
            ''
        );
        break;

    case 'pageCate':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'messageCate':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'listCate':
        $App->viewMethod = 'list';
        break;

    default:
        $App->viewMethod = 'list';
        break;
}

/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch ((string)$App->viewMethod) {
    case 'formNew':
        $App->item = new stdClass();
        $App->itemTags = [];
        $App->item->active = 1;
        $App->item->ordering = 0;
        $App->item->created = $App->nowDateTime;
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['cate']);
        }
        $App->templateApp = 'formCategory.html';
        $App->methodForm = 'insertCate';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formCategory.js"></script>';
        break;

    case 'formMod':
        $App->item = new stdClass();
        Sql::initQuery($App->params->tables['cate'], ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        $App->itemTags = [];
        if ($App->item->id_tags != '') {
            $App->itemTags = explode(',', (string) $App->item->id_tags);
        }
        if (Core::$resultOp->error == 1) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['cate']);
        }
        $App->templateApp = 'formCategory.html';
        $App->methodForm = 'updateCate';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formCategory.js"></script>';
        break;

    case 'list':
        $App->items = new stdClass();
        $App->item = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields = [];
        $qryFields[] = 'cat.*,(SELECT COUNT(ite.id) FROM '.$App->params->tables['item'].' AS ite WHERE ite.id_cat = cat.id) AS items';
        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        $clause = '';
        $and = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->params->fields['cate'], '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $and.'('.$sessClause.')';
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($App->params->tables['cate'].' AS cat', $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        Sql::setOrder('ordering '.$App->params->ordersType['cate']);
        //Sql::setOrder('datatimeins '.$App->params->ordersType['prod']);
        if (Core::$resultOp->error <> 1) {
            $obj = Sql::getRecords();
        }
        /* sistemo i dati */
        $arr = [];
        if (is_array($obj) && is_array($obj) && count($obj) > 0) {
            foreach ($obj as $value) {
                $field = 'title_'.$_lang['user'];
                $value->title = $value->$field;
                $arr[] = $value;
            }
        }
        $App->items = $arr;

        $App->pagination = Utilities::getPagination($App->page, Sql::getTotalsItems(), $App->itemsForPage);
        $App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
        $App->paginationTitle = preg_replace('/%START%/', (string) $App->pagination->firstPartItem, (string) $App->paginationTitle);
        $App->paginationTitle = preg_replace('/%END%/', (string) $App->pagination->lastPartItem, (string) $App->paginationTitle);
        $App->paginationTitle = preg_replace('/%ITEM%/', (string) $App->pagination->itemsTotal, (string) $App->paginationTitle);

        $App->pageSubTitle = preg_replace('/%ITEM%/', (string) $_lang['categorie'], (string) $_lang['lista delle %ITEM%']);
        $App->templateApp = 'listCategories.html';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listCategories.js"></script>';
        break;

    default:
        break;
}
