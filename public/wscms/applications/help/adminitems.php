<?php

/*	wscms/help/items.php v.3.5.4. 30/07/2019 */

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

$App->id_cat = 0;

switch (Core::$request->method) {

    case 'moreOrderingItem':
        Utilities::increaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['item'],'orderingType' => $App->params->ordersType['item'],'parent' => 0,'parentField' => '','label' => ucfirst((string) $_lang['voce']).' '.$_lang['spostata']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
        break;
    case 'lessOrderingItem':
        Utilities::decreaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['item'],'orderingType' => $App->params->ordersType['item'],'parent' => 0,'parentField' => '','label' => ucfirst((string) $_lang['voce']).' '.$_lang['spostata']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
        break;

    case 'activeItem':
    case 'disactiveItem':
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -4), $App->params->tables['item'], $App->id, ['label' => $_lang['voce'],'attivata' => $_lang['attivato'],'disattivata' => $_lang['disattivato']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
        break;

    case 'deleteItem':
        if ($App->id > 0) {
            Sql::initQuery($App->params->tables['item'], ['id'], [$App->id], 'id = ?');
            Sql::deleteRecord();
            if (Core::$resultOp->error == 0) {
                Core::$resultOp->message = ucfirst((string) $_lang['voce cancellata']).'!';
            } else {
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'newItem':
        $App->pageSubTitle = $_lang['inserisci voce'];
        $App->viewMethod = 'formNew';
        break;

    case 'insertItem':
        if ($_POST) {
            if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'], 'ordering', '') + 1;
            }

            Sql::setDebugMode(1);
            ToolsStrings::dump($_POST);

            Form::parsePostByFields($App->params->fields['item'], $_lang, []);

            ToolsStrings::dump($_POST);

            if (Core::$resultOp->error == 0) {
                Sql::insertRawlyPost($App->params->fields['item'], $App->params->tables['item']);
                if (Core::$resultOp->error == 0) {
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        [$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getInsertRecordFromPostResults(0, Core::$resultOp, $_lang);
        break;

    case 'modifyItem':
        $App->pageSubTitle = $_lang['modifica voce'];
        $App->viewMethod = 'formMod';
        break;

    case 'updateItem':
        if ($_POST) {
            if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) {
                $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'], 'ordering', '') + 1;
            }
            /* parsa i post in base ai campi */
            Form::parsePostByFields($App->params->fields['item'], $_lang, []);
            if (Core::$resultOp->error == 0) {
                Sql::updateRawlyPost($App->params->fields['item'], $App->params->tables['item'], 'id', $App->id);
                if (Core::$resultOp->error == 0) {
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        [$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getUpdateRecordFromPostResults($App->id, Core::$resultOp, $_lang);
        break;

    case 'pageItem':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'messageItem':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'listItem':
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
        $App->item->active = 1;
        $App->item->created = Config::$nowDateTimeIso;
        $App->item->ordering = 0;
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['item']);
        }
        $App->templateApp = 'formItem.html';
        $App->methodForm = 'insertItem';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
        break;

    case 'formMod':
        $App->item = new stdClass();
        Sql::initQuery($App->params->tables['item'], ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['item']);
        }
        $App->templateApp = 'formItem.html';
        $App->methodForm = 'updateItem';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
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
        $and = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->params->fields['item'], '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $and.'('.$sessClause.')';
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($App->params->tables['item'], $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        Sql::setOrder('ordering '.$App->params->ordersType['item']);
        if (Core::$resultOp->error <> 1) {
            $obj = Sql::getRecords();
        }
        /* sistemo i dati */
        $arr = [];
        if (is_array($obj) && is_array($obj) && count($obj) > 0) {
            foreach ($obj as $value) {
                $field = 'title_'.$_lang['user'];
                $value->title = $value->$field;
                $field = 'content_'.$_lang['user'];
                $value->content = $value->$field;
                $arr[] = $value;
            }
        }
        $App->items = $arr;
        $App->pagination = Utilities::getPagination($App->page, Sql::getTotalsItems(), $App->itemsForPage);
        $App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
        $App->paginationTitle = preg_replace('/%START%/', (string) $App->pagination->firstPartItem, (string) $App->paginationTitle);
        $App->paginationTitle = preg_replace('/%END%/', (string) $App->pagination->lastPartItem, (string) $App->paginationTitle);
        $App->paginationTitle = preg_replace('/%ITEM%/', (string) $App->pagination->itemsTotal, (string) $App->paginationTitle);

        $App->pageSubTitle = $_lang['lista delle voci'];
        $App->templateApp = 'listadminItems.html';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listadminItems.js"></script>';
        break;

    default:
        break;
}
