<?php

/* wscms/newsletter/indirizzi.php v.3.1.0. 10/01/2017 */

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

if (isset($_POST['id_cat']) && (int)$_POST['id_cat'] >= 0) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'id_cat', (int)$_POST['id_cat']);
}

/* GESTIONE CATEGORIE */
$App->id_cat = ($_MY_SESSION_VARS[$App->sessionName]['id_cat'] ?? 0);

if ($App->params->categories == 1) {
    Sql::initQuery($App->tableIndCat, ['id','title_it'], []);
    Sql::setOptions(['fieldTokeyObj' => 'id']);
    $App->item_cats = Sql::getRecords();
    if (Core::$resultOp->error > 0) {
        echo Core::$resultOp->message;
        die;
    }
    if (!is_array($App->item_cats) || (is_array($App->item_cats) && count($App->item_cats) == 0)) {
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageIndCat/2/'.urlencode('Devi creare o attivare almeno un'.$App->labels['ind']['ownerSex'].' '.$App->labels['ind']['owner'].'!'));
        die();
    }
}
//ToolsStrings::dump($App->item_cats);

switch (Core::$request->method) {

    case 'activeInd':
    case 'disactiveInd':
        Sql::manageFieldActive(substr((string) Core::$request->method, 0, -3), $App->tableInd, $App->id, ['label' => $_lang['voce'],'attivata' => $_lang['attivato'],'disattivata' => $_lang['disattivato']]);
        $_SESSION['message'] = '0|'.Core::$resultOp->message;
        ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listInd');
        break;

    case 'deleteInd':
        if ($App->id > 0) {
            Sql::initQuery($App->tableInd, [], [$App->id], 'id = ?');
            Sql::deleteRecord();
            if (Core::$resultOp->error == 0) {
                /* cancella i riferimenti cat->ind */
                Sql::initQuery($App->tableRifCatInd, [], [$App->id], 'id_ind = ?');
                Sql::deleteRecord();
                if (Core::$resultOp->error == 0) {
                    Core::$resultOp->message = ucfirst((string) $App->labels['ind']['item']).' cancellat'.$App->labels['ind']['itemSex'].'!';
                }
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'newInd':
        $App->pageSubTitle = 'inserisci '.$App->labels['ind']['item'];
        $App->viewMethod = 'formNew';
        break;

    case 'insertInd':
        if ($_POST) {
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['created'])) {
                $_POST['created'] = Config::$nowDateTimeIso;
            }
            if (!isset($_POST['dateconfirmed'])) {
                $_POST['dateconfirmed'] = Config::$nowDateTimeIso;
            }

            if ($App->params->categories == 1 && !isset($_POST['id_cats']) || (isset($_POST['id_cats']) && !is_array($_POST['id_cats']))) {
                Core::$resultOp->error = 1;
                Core::$resultOp->message = 'Devi scegliere almeno un'.$App->labels['ownerSex'].' '.$App->labels['owner'].'!';
            } else {
                if ($App->params->categories == 0) {
                    $_POST['id_cats'] = ['0'];
                }
            }

            if (Core::$resultOp->error == 0) {
                /* controlla i campi obbligatori */
                Sql::checkRequireFields($App->fieldsInd);
                if (Core::$resultOp->error == 0) {
                    Sql::stripMagicFields($_POST);
                    $_POST['hash'] = md5(SITE_CODE_KEY.$_POST['name'].$_POST['email'].$_POST['surname']);
                    Sql::insertRawlyPost($App->fieldsInd, $App->tableInd);
                    if (Core::$resultOp->error == 0) {
                        $id_item = Sql::getLastInsertedIdVar();
                        if (isset($_POST['id_cats']) && is_array($_POST['id_cats']) && count($_POST['id_cats']) > 0) {
                            foreach ($_POST['id_cats'] as $value) {
                                /* salva i riferimenti cat->ind */
                                Sql::initQuery($App->tableRifCatInd, ['id_cat','id_ind'], [intval($value),$id_item]);
                                Sql::insertRecord();
                            }
                        }
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'inserisci '.$App->labels['ind']['item'];
            $App->viewMethod = 'formNew';
        } else {
            $App->viewMethod = 'list';
            Core::$resultOp->message = ucfirst((string) $App->labels['ind']['item']).' inserit'.$App->labels['ind']['itemSex'].'!';
        }
        break;

    case 'modifyInd':
        $App->pageSubTitle = 'modifica '.$App->labels['ind']['item'];
        $App->viewMethod = 'formMod';
        break;

    case 'updateInd':
        if ($_POST) {
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['created'])) {
                $_POST['created'] = Config::$nowDateTimeIso;
            }
            if (!isset($_POST['dateconfirmed'])) {
                $_POST['dateconfirmed'] = Config::$nowDateTimeIso;
            }

            if ($App->params->categories == 1 && !isset($_POST['id_cats']) || (isset($_POST['id_cats']) && !is_array($_POST['id_cats']))) {
                Core::$resultOp->error = 1;
                Core::$resultOp->message = 'Devi scegliere almeno un'.$App->labels['ownerSex'].' '.$App->labels['owner'].'!';
            } else {
                if ($App->params->categories == 0) {
                    $_POST['id_cats'] = ['0'];
                }
            }

            if (Core::$resultOp->error == 0) {
                /* controlla i campi obbligatori */
                Sql::checkRequireFields($App->fieldsInd);
                if (Core::$resultOp->error == 0) {
                    Sql::stripMagicFields($_POST);
                    Sql::updateRawlyPost($App->fieldsInd, $App->tableInd, 'id', $App->id);
                    if (Core::$resultOp->error == 0) {
                        /* cancella i vecchi riferimenti */
                        Sql::initQuery($App->tableRifCatInd, [], [$App->id], 'id_ind = ?');
                        Sql::deleteRecord();
                        if (Core::$resultOp->error == 0) {
                            if (isset($_POST['id_cats']) && is_array($_POST['id_cats']) && count($_POST['id_cats']) > 0) {
                                foreach ($_POST['id_cats'] as $value) {
                                    /* salva i riferimenti cat->ind */
                                    Sql::initQuery($App->tableRifCatInd, ['id_cat','id_ind'], [intval($value),$App->id]);
                                    Sql::insertRecord();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error > 0) {
            $App->pageSubTitle = 'modifica '.$App->labelItem;
            $App->viewMethod = 'formMod';
        } else {
            if (isset($_POST['submitForm'])) {
                $App->viewMethod = 'list';
                Core::$resultOp->message = ucfirst((string) $App->labels['ind']['item']).' modificat'.$App->labels['ind']['itemSex'].'!';
            } else {
                if (isset($_POST['id'])) {
                    $App->id = $_POST['id'];
                    $App->pageSubTitle = 'modifica '.$App->labels['ind']['item'];
                    $App->viewMethod = 'formMod';
                    Core::$resultOp->message = 'Modifiche applicate!';
                } else {
                    $App->viewMethod = 'formNew';
                    $App->pageSubTitle = 'inserisci '.$App->labels['ind']['item'];
                }
            }
        }
        break;

    case 'pageInd':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    case 'messageInd':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'listInd':
        $App->viewMethod = 'list';
        break;

    case 'expDBInd':
        // autoload by composer
        //include_once(PATH."classes/class.Dumper.php");
        $user = (Core::$dbConfig['user'] ?? 'nd');
        $password = (Core::$dbConfig['password'] ?? 'nd');
        $host = (Core::$dbConfig['host'] ?? 'nd');
        $name = (Core::$dbConfig['name'] ?? 'nd');

        $filename = $App->params->uploadPathDirs['backup'].'newsletter'.Config::$nowDateIso.'.sql';

        try {
            $world_dumper = Shuttle_Dumper::create([
                'host' => $host,
                'username' => $user,
                'password' => $password,
                'db_name' => $name,
                'include_tables' => [$App->params->tables['indcat'],$App->params->tables['rifcatind'],$App->params->tables['ind']],
                ]);
            $world_dumper->dump($filename);

            if (file_exists($filename)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filename));
                readfile($filename);
                exit;
            }
        } catch (Shuttle_Exception $e) {
            echo "Couldn't dump database: " . $e->getMessage();
        }
        die();
        break;

    case 'expCSVInd':
        ini_set('memory_limit', '-1'); //memoria infinita
        header('Content-type: text/html; charset=utf-8');
        $filename = 'newsletter-indirizzi.csv';
        Sql::initQuery($App->params->tables['ind'], ['*'], []);
        $obj = Sql::getRecords();

        /* sistemo i dati */
        $arr = [];
        foreach ($obj as $value) {
            $categorie = '';
            /* prelevio le categorie associate al indirizzo */
            Sql::initQuery($App->params->tables['rifcatind'], ['*'], [$value->id], 'id_ind = ?');
            $objcat = Sql::getRecords();
            foreach ($objcat as $valuecat) {
                /* preleva i titolo della categoria */
                Sql::initQuery($App->params->tables['indcat'], ['*'], [$valuecat->id_cat], 'id = ?');
                $objcattit = Sql::getRecord();
                if (isset($objcattit->title_it)) {
                    $categorie .= $objcattit->title_it.'; ';
                }
            }
            $value->categoria = rtrim($categorie, '; ');
            $arr[] = $value;
        }
        $data = $arr;
        if (Core::$resultOp->error == 0) {
            $cols = Sql::getTableFields($App->params->tables['ind']);
            /* crea la prina riga */
            $riga0 = [];
            foreach ($cols as $value) {
                $riga0[] = $value['name'];
            }
            $riga0[] = 'categoria';
            $fp = fopen('php://output', 'w');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($fp, $riga0, '|', escape: '\\');
            foreach ($obj as $value) {
                $riga = [];
                foreach ($value as $value1) {
                    $riga[] = ($value1 != '' ? $value1 : 'n.d.');
                }
                fputcsv($fp, $riga, '|', escape: '\\');
            }
        }
        die();
        break;

    default:
        $App->viewMethod = 'list';
        break;
}

switch ((string)$App->viewMethod) {
    case 'formNew':
        $App->item = new stdClass();
        $App->item->created = Config::$nowDateTimeIso;
        $App->item->dateconfirmed = Config::$nowDateTimeIso;
        $App->item->confirmed = 1;
        $App->item->active = 1;

        $App->item->cats = [];

        $App->templateApp = 'formInd.html';
        $App->methodForm = 'insertInd';
        break;

    case 'formMod':
        $App->item = new stdClass();
        Sql::initQuery($App->tableInd, ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();

        $App->item->cats = [];
        $obj = new stdClass();
        Sql::initQuery($App->tableRifCatInd, ['*'], [$App->id], 'id_ind = ?');
        $obj = Sql::getRecords();
        if (isset($obj) && is_array($obj) && count($obj) > 0) {
            foreach ($obj as $value) {
                if (isset($value->id_cat) && $value->id_cat != '') {
                    $App->item->cats[] =  $value->id_cat;
                }
            }
        }

        //ToolsStrings::dump($App->item->cats);

        $App->templateApp = 'formInd.html';
        $App->methodForm = 'updateInd';
        break;

    case 'list':
        //Config::$debugMode = 1;
        $App->items = new stdClass();
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        $qryFields = [' DISTINCT ci.id_ind,i.*'];
        $qryFieldsValues = [];
        $qryFieldsValuesClause = [];
        /* preleva gli indirizzi in base alla categoria selezionata */

        //echo $App->id_cat;

        $clause = 'confirmed = 1';
        if ($App->id_cat > 0) {
            $clause .= ' AND ci.id_cat = ?';
            $qryFieldsValues = [$App->id_cat];
        }
        if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
            [$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'], $App->fieldsInd, '');
        }
        if (isset($sessClause) && $sessClause != '') {
            $clause .= ' AND ('.$sessClause.')';
        }
        if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
            $qryFieldsValues = array_merge($qryFieldsValues, $qryFieldsValuesClause);
        }
        $tables = $App->tableRifCatInd.' AS ci LEFT JOIN '.$App->tableInd.' AS i ON (ci.id_ind = i.id)';
        Sql::initQuery($tables, $qryFields, $qryFieldsValues, $clause);
        Sql::setItemsForPage($App->itemsForPage);
        Sql::setPage($App->page);
        Sql::setResultPaged(true);
        Sql::setOrder('id ASC');
        if (Core::$resultOp->error <> 1) {
            $App->items = Sql::getRecords();
        }
        $App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
        $App->pageSubTitle = 'la lista degli '.$App->labels['ind']['items'].' iscritti alla newsletter';
        $App->templateApp = 'listInd.html';
        break;

    default:
        break;
}
