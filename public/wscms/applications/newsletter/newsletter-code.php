<?php

/* wscms/newsletter/newsletter-code.php v.3.1.0. 10/01/2017 */

//Core::setDebugMode(1);

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

if (isset($_POST['newsTemplate'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'newsTemplate', $_POST['newsTemplate']);
}
if (Core::$request->method == 'listNewCode' && $App->id > 0) {
    /* prende i dati della news */
    Sql::initQuery($App->tableNew, ['*'], [$App->id], 'id = ?');
    $App->item_news = Sql::getRecord();
    if (isset($App->item_news->template)) {
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'newsTemplate', $App->item_news->template);
    }

}

$App->item = new stdClass();
$App->item->templatesAvaiable = $Module->getTemplatesArray($App->templatesFolder);

/* gestione template di default */
if (!is_array($App->item->templatesAvaiable) || (is_array($App->item->templatesAvaiable) && count($App->item->templatesAvaiable) == 0)) {
    ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageNew/2/'.urlencode('Devi creare  almeno file template!'));
    die();
}

$App->newsTemplate = '';
if (isset($_MY_SESSION_VARS[$App->sessionName]['newsTemplate']) && $_MY_SESSION_VARS[$App->sessionName]['newsTemplate'] != '') {
    $App->newsTemplate = $_MY_SESSION_VARS[$App->sessionName]['newsTemplate'];
}
if ($App->newsTemplate == '') {
    $App->newsTemplate = $App->item->templatesAvaiable[0];
}

switch (Core::$request->method) {
    case 'activeNewCode':
    case 'disactiveNewCode':
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -7), $App->tableNewCode, $App->id, ucfirst((string) $App->labels['code']['item']));
        $App->viewMethod = 'list';
        break;

    case 'deleteNewCode':
        if ($App->id > 0) {
            Sql::initQuery($App->tableNewCode, [], [$App->id], 'id = ?');
            Sql::deleteRecord();
            if (Core::$resultOp->error == 0) {
                Core::$resultOp->message = ucfirst((string) $App->labels['code']['item']).' cancellat'.$App->labels['code']['itemSex'].'!';
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'newNewCode':
        $App->pageSubTitle = 'inserisci '.$App->labels['code']['item'];
        $App->viewMethod = 'formNewCode';
        break;

    case 'insertNewCode':
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }

            /* controlla i campi obbligatori */
            Sql::checkRequireFields($App->fieldsNewCode);
            if (Core::$resultOp->error == 0) {
                Sql::stripMagicFields($_POST);
                Sql::insertRawlyPost($App->fieldsNewCode, $App->tableNewCode);
                if (Core::$resultOp->error == 0) {

                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'inserisci '.$App->labels['code']['item'];
            $App->viewMethod = 'formNewCode';
        } else {
            $App->viewMethod = 'list';
            Core::$resultOp->message = ucfirst((string) $App->labels['code']['item']).' inserit'.$App->labels['code']['itemSex'].'!';
        }
        break;

    case 'modifyNewCode':
        $App->pageSubTitle = 'modifica '.$App->labels['code']['item'];
        $App->viewMethod = 'formMod';
        break;

    case 'updateNewCode':
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            /* controlla i campi obbligatori */
            Sql::checkRequireFields($App->fieldsNewCode);
            if (Core::$resultOp->error == 0) {
                $_POST['content_it'] = ToolsStrings::parseHtmlContent($_POST['content_it'], ['customtag' => '{{FOLDERSITE}}','customtagvalue' => FOLDER_SITE]);
                Sql::stripMagicFields($_POST);
                Sql::updateRawlyPost($App->fieldsNewCode, $App->tableNewCode, 'id', $App->id);
                if (Core::$resultOp->error == 0) {
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'modifica '.$App->labels['code']['item'];
            $App->viewMethod = 'formMod';
        } else {
            if (isset($_POST['submitForm'])) {
                $App->viewMethod = 'list';
                Core::$resultOp->message = ucfirst((string) $App->labels['code']['item']).' modificat'.$App->labels['code']['itemSex'].'!';
            } else {
                if (isset($_POST['id'])) {
                    $App->id = $_POST['id'];
                    $App->pageSubTitle = 'modifica '.$App->labels['code']['item'];
                    $App->viewMethod = 'formMod';
                    Core::$resultOp->message = 'Modifiche applicate!';
                } else {
                    $App->viewMethod = 'formNewCode';
                    $App->pageSubTitle = 'inserisci '.$App->labels['code']['item'];
                }
            }
        }
        break;

    case 'previewNewCode':
        Sql::initQuery($App->tableNewCode, ['*'], [$App->id], 'id = ?');
        $App->item_code = Sql::getRecord();
        if (Core::$resultOp->error == 0) {
            $App->item->finalOutput = '';
            $file = PATH_UPLOAD_DIR.$App->templatesFolder.$App->newsTemplate;
            $urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;
            $App->item_code->content_it = ToolsStrings::parseHtmlContent($App->item_code->content_it, ['customtag' => '{{PATHNEWSLETTER}}','customtagvalue' => UPLOAD_DIR.$App->templatesFolder]);
            if (file_exists($file) == true) {
                $App->item->finalOutput = file_get_contents($file);
                $App->item->finalOutput = preg_replace('/{{PATHNEWSLETTER}}/', UPLOAD_DIR.$App->templatesFolder, $App->item->finalOutput);
                $App->item->finalOutput = preg_replace('/{{CONTENT}}/', (string) $App->item_code->content_it, (string) $App->item->finalOutput);
                $App->item->finalOutput = preg_replace('/{{URLDELETE}}/', $urldelete, (string) $App->item->finalOutput);
            } else {
                $App->item->finalOutput = $App->item_code->content_it;
            }
            /* INIZIO LAYOUT */
            echo $App->item->finalOutput;
            $renderTpl = false;
            $App->viewMethod = 'NULL';
        } else {
            $App->viewMethod = 'list';
        }
        break;

    case 'pageNewCode':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        break;

    case 'messageNewCode':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'listNewCode':
        $App->viewMethod = 'list';
        break;

    default:
        $App->viewMethod = 'list';
        break;
}

switch ((string)$App->viewMethod) {
    case 'formNewCode':
        $App->item->created = $App->nowDateTime;
        $App->item->active = 1;
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->fieldsNewCode);
        }
        $App->templateApp = 'formNewCode.html';
        $App->methodForm = 'insertNewCode';
        break;

    case 'formMod':
        Sql::initQuery($App->tableNewCode, ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->fieldsNewCode);
        }
        $App->item->content_it = ToolsStrings::parseHtmlContent($App->item->content_it, ['customtag' => '{{URLSITE}}','customtagvalue' => URL_SITE]);
        $App->templateApp = 'formNewCode.html';
        $App->methodForm = 'updateNewCode';
        break;

    case 'list':
        $App->items = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields = ['*'];
        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        $clause = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->fieldsNewCode, '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $sessClause;
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($App->tableNewCode, $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        if (Core::$resultOp->error == 0) {
            $App->items = Sql::getRecords();
        }
        $App->pagination = Utilities::getPagination($App->page, Sql::getTotalsItems(), $App->itemsForPage);
        $App->pageSubTitle = 'la lista dei '.$App->labels['code']['items'].' da aggiungere al testo della newsletter';
        $App->templateApp = 'listNewCode.html';
        break;

    default:
        break;
}
