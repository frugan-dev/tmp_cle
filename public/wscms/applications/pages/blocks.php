<?php

/* wscms/site/blocks.php v.3.5.4. 02/04/2019 */

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}
if (isset($_POST['id_owner'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'id_owner', $_POST['id_owner']);
}

if (Core::$request->method == 'listIblo' && $App->id > 0) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'id_owner', $App->id);
}

/* gestione sessione -> id_owner */
$App->id_owner = ($_MY_SESSION_VARS[$App->sessionName]['id_owner'] ?? 0);

//echo $App->id_owner;

if ($App->id_owner > 0) {
    Sql::initQuery($App->params->tables['item'], ['*'], [$App->id_owner], 'id = ?');
    Sql::setOptions(['fieldTokeyObj' => 'id']);
    $App->ownerData = Sql::getRecord();
    if (Core::$resultOp->error > 0) {
        echo Core::$resultOp->message;
        die;
    }
    $field = 'title_'.$_lang['user'];
    $App->ownerData->title = $App->ownerData->$field;
}

//print_r($App->ownerData);

if (Core::$resultOp->error > 0) {
    echo Core::$resultOp->message;
    die;
}
if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) {
    //ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageItem/2/'.urlencode($_lang['Devi creare o attivare almeno una pagina!']));
    //die();
}

switch (Core::$request->method) {

    case 'moreOrderingIblo':
        if ($App->id == 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }
        Utilities::increaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['iblo'],'orderingType' => $App->params->orderTypes['item'],'parent' => 1,'parentField' => 'id_owner','label' => ucfirst((string) $_lang['blocco']).' '.$_lang['spostato']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        break;
    case 'lessOrderingIblo':
        if ($App->id == 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }
        Utilities::decreaseFieldOrdering($App->id, $_lang, ['table' => $App->params->tables['iblo'],'orderingType' => $App->params->orderTypes['item'],'parent' => 1,'parentField' => 'id_owner','label' => ucfirst((string) $_lang['blocco']).' '.$_lang['spostato']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        break;

    case 'activeIblo':
    case 'disactiveIblo':
        if ($App->id == 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -4), $App->params->tables['iblo'], $App->id, ['label' => $_lang['blocco'],'attivata' => $_lang['attivato'],'disattivata' => $_lang['disattivato']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        break;

    case 'deleteIblo':
        if ($App->id == 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }

        /* controlla se ha immagini associate */
        Sql::initQuery($App->params->tables['resources'], ['id'], [$App->id], 'id_owner = ? AND resource_type = 1');
        $count = Sql::countRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        if ($count > 0) {
            $_SESSION['message'] = '2|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Core::$langVars['immagini'], (string) Core::$langVars['Ci sono ancora %ITEM% associate!']));
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        }
        /* controlla se ha files associati */
        Sql::initQuery($App->params->tables['resources'], ['id'], [$App->id], 'id_owner = ? AND resource_type = 2');
        $count = Sql::countRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        if ($count > 0) {
            $_SESSION['message'] = '2|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Core::$langVars['files'], (string) Core::$langVars['Ci sono ancora %ITEM% associati!']));
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        }

        /* controlla se ha immagini gallerie associate */
        Sql::initQuery($App->params->tables['resources'], ['id'], [$App->id], 'id_owner = ? AND resource_type = 3');
        $count = Sql::countRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        if ($count > 0) {
            $_SESSION['message'] = '2|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Core::$langVars['gallerie'], (string) Core::$langVars['Ci sono ancora %ITEM% associati!']));
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        }

        $App->itemOld = new stdClass();
        Sql::initQuery($App->params->tables['iblo'], ['filename'], [$App->id], 'id = ?');
        $App->itemOld = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE.'error/db');
        }

        Sql::initQuery($App->params->tables['iblo'], ['id'], [$App->id], 'id = ?');
        Sql::deleteRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE.'error/db');
        }

        if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['iblo'].$App->itemOld->filename)) {
            @unlink($App->params->uploadPaths['iblo'].$App->itemOld->filename);
        }
        $_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Core::$langVars['blocco'], (string) Core::$langVars['%ITEM% cancellato'])).'!';
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        break;

    case 'newIblo':
        $App->item = new stdClass();
        $App->item->created = Config::$nowDateTimeIso;
        $App->item->active = 1;
        $App->item->updated = Config::$nowDateTimeIso;
        $App->item->filenameRequired = false;
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['iblo']);
        }
        $App->pageSubTitle = preg_replace('/%ITEM%/', (string) Config::$langVars['blocco'], (string) Config::$langVars['inserisci %ITEM%']);
        $App->methodForm = 'insertIblo';
        $App->viewMethod = 'form';
        break;

    case 'insertIblo':

        if (!$_POST) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }

        //Config::$debugMode = 1;

        /* gestione automatica dell'ordering de in input = 0 */
        if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == '')) {
            $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['iblo'], 'ordering', 'id_owner = '.$App->id_owner) + 1;
        }

        /* preleva il filename dal form */
        ToolsUpload::setFilenameFormat($globalSettings['image type available']);
        ToolsUpload::getFilenameFromForm();
        $_POST['filename'] = ToolsUpload::getFilenameMd5();
        $_POST['org_filename'] = ToolsUpload::getOrgFilename();
        if (Core::$resultOp->error > 0) {
            $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newIblo');
        }

        /* parsa i post in base ai campi */
        Form::parsePostByFields($App->params->fields['iblo'], $_lang, []);
        if (Core::$resultOp->error > 0) {
            echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newIblo');
        }

        //ToolsStrings::dump($_POST);

        Sql::insertRawlyPost($App->params->fields['iblo'], $App->params->tables['iblo']);
        if (Core::$resultOp->error > 0) {
            die('error insert db');
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');
        }

        $App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */

        // sposto il file
        if ($_POST['filename'] != '') {
            move_uploaded_file(ToolsUpload::getTempFilename(), $App->params->uploadPaths['iblo'].$_POST['filename']) or die('Errore caricamento file');
        }

        $_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Config::$langVars['blocco'], (string) Config::$langVars['%ITEM% inserito'])).'!';
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');

        break;

    case 'modifyIblo':
        $App->item = new stdClass();
        Sql::initQuery($App->params->tables['iblo'], ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            Utilities::setItemDataObjWithPost($App->item, $App->params->fields['iblo']);
        }
        $App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : false);
        $App->pageSubTitle = preg_replace('/%ITEM%/', (string) Config::$langVars['modifica %ITEM%'], (string) Config::$langVars['blocco']);
        $App->methodForm = 'updateIblo';
        $App->viewMethod = 'form';
        break;

    case 'updateIblo':

        if (!$_POST) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }
        if ($App->id == 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
        }

        if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == '')) {
            $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['iblo'], 'ordering', 'id_owner = '.$App->id_owner) + 1;
        }

        $App->itemOld = new stdClass();
        /* preleva Ibloname vecchio */
        Sql::initQuery($App->params->tables['iblo'], ['filename','org_filename'], [$App->id], 'id = ?');
        $App->itemOld = Sql::getRecord();
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');
        }

        /* preleva il filename dal form */
        ToolsUpload::setFilenameFormat($globalSettings['image type available']);
        ToolsUpload::getFilenameFromForm($App->id);
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
        if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
            if (file_exists($App->params->uploadPaths['iblo'].$App->itemOld->filename)) {
                @unlink($App->params->uploadPaths['iblo'].$App->itemOld->filename);
            }
            $_POST['filename'] = '';
            $_POST['org_filename'] = '';
        }

        /* parsa i post in base ai campi */
        Form::parsePostByFields($App->params->fields['iblo'], $_lang, []);
        if (Core::$resultOp->error > 0) {
            $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyIblo');
        }

        /* memorizza nel db */
        Sql::updateRawlyPost($App->params->fields['iblo'], $App->params->tables['iblo'], 'id', $App->id);
        if (Core::$resultOp->error > 0) {
            ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');
        }

        if ($uploadFilename != '') {
            move_uploaded_file(ToolsUpload::getTempFilename(), $App->params->uploadPaths['iblo'].$uploadFilename) or die('Errore caricamento file');
            /* cancella l'immagine vecchia */
            if (file_exists($App->params->uploadPaths['iblo'].$App->itemOld->filename)) {
                @unlink($App->params->uploadPaths['iblo'].$App->itemOld->filename);
            }
        }

        $_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/', (string) Core::$langVars['blocco'], (string) Core::$langVars['%ITEM% modificato'])).'!';
        if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyIblo/'.$App->id);
        } else {
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIblo');
        }

        break;

    case 'pageIblo':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'downloadIblo':
        if ($App->id > 0) {
            $renderTpl = false;
            ToolsUpload::downloadFileFromDB($App->params->uploadPaths['iblo'], $App->params->tables['iblo'], $App->id, 'filename', 'org_filename', '', '');
            die();
        }
        $App->viewMethod = 'list';
        break;

    case 'listIblo':
    default:
        //Config::$debugMode = 1;

        $App->items = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields[] = 'ite.*';

        //$qryFields[] = "(SELECT COUNT(img.id) FROM ".$App->params->tables['resources']." AS img WHERE img.id_owner = ite.id AND resource_type = 1) AS images";
        //$qryFields[] = "(SELECT COUNT(fil.id) FROM ".$App->params->tables['resources']." AS fil WHERE fil.id_owner = ite.id AND resource_type = 2) AS files";
        //$qryFields[] = "(SELECT COUNT(gal.id) FROM ".$App->params->tables['resources']." AS gal WHERE gal.id_owner = ite.id AND resource_type = 3) AS gallery";
        //$qryFields[] = "(SELECT COUNT(vid.id) FROM ".$App->params->tables['resources']." AS vid WHERE vid.id_owner = ite.id AND resource_type = 4) AS videos";

        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        $clause = '';
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->params->fields['iblo'], '');
        }
        if ($App->id_owner > 0) {
            $clause .= 'id_owner = ?';
            $qryFieldsValues[] = $App->id_owner;
            $and = ' AND ';
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= $and.'('.$sessClause.')';
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        Sql::initQuery($App->params->tables['iblo'].' AS ite', $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setOrder('ordering '.$App->params->orderTypes['iblo']);
        Sql::setResultPaged(true);
        if (Core::$resultOp->error <> 1) {
            $obj = Sql::getRecords();
        }

        /* sistemo i dati */
        $arr = [];
        if (isset($obj) && is_array($obj) && is_array($obj) && count($obj) > 0) {
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

        $App->pageSubTitle = Config::$langVars['lista dei blocchi contenuto associati ad una pagina'];
        $App->viewMethod = 'list';

        break;
}

switch ((string)$App->viewMethod) {

    case 'form':
        $App->templateApp = 'formIblo.tpl.php';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formIblo.js" type="text/javascript"></script>';
        break;

    case 'list':

        $App->templateApp = 'listIblo.tpl.php';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listIblo.js" type="text/javascript"></script>';
        break;

    default:
        break;
}
