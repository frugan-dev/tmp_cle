<?php

/* wscms/site-pages/index.php v.1.0.1. 07/09/2016 */

if (isset($_POST['itemsforpage'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'ifp', $_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable'])) {
    $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'srcTab', $_POST['searchFromTable']);
}

/* preleva i link moduli (alias) */

Sql::initQuery(Sql::getTablePrefix().'modules', ['id','alias'], [], "alias <> ''");
Sql::setOrder('ordering DESC');
$App->modules = Sql::getRecords();
if (Core::$resultOp->error == 1) {
    die('Errore database tabella moduli');
}
//ToolsStrings::dump($App->modules);

switch (Core::$request->method) {
    case 'moreOrdering':
        $Utilities::increaseFieldOrdering($App->id, ['table' => $table,'orderingType' => $App->orderingType,'parent' => true,'sexSuffix' => 'a','labelItem' => 'Pagina']);
        $App->viewMethod = 'list';
        break;
    case 'lessOrdering':
        $Utilities::decreaseFieldOrdering($App->id, ['table' => $table,'orderingType' => $App->orderingType,'parent' => true,'sexSuffix' => 'a','labelItem' => 'Pagina']);
        $App->viewMethod = 'list';
        break;

    case 'active':
    case 'disactive':
        Sql::manageFieldActive(Core::$request->method, $table, $App->id, 'Pagina');
        $App->viewMethod = 'list';
        break;

    case 'delete':
        if ($App->id > 0) {
            $Module->deletePage($tableRif, $App->id);
            if ($Module->error == 0) {
                Core::$resultOp->message = 'Pagina cancellata!';
            } else {
                Core::$resultOp->message = $Module->message;
                Core::$resultOp->error = 1;
            }
        }
        $App->viewMethod = 'list';
        break;

    case 'modify':
        $App->pageSubTitle = 'modifica pagina';
        $App->viewMethod = 'formMod';
        break;

    case 'new':
        $App->pageSubTitle = 'inserisci pagina';
        $App->viewMethod = 'formNew';

        break;

    case 'insert':
        if ($_POST) {
            $App->templateItem = new stdClass();
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['menu'])) {
                $_POST['menu'] = 0;
            }
            if (!isset($_POST['title_it']) || $_POST['title_it'] == '') {
                $_POST['title_it'] = 'Pagina Vuota';
                $_POST['active'] = 0;
            }
            foreach ($globalSettings['languages'] as $lang) {
                if ($_POST['title_seo_'.$lang] == '') {
                    $_POST['title_seo_'.$lang] = SanitizeStrings::cleanTitleUrl($_POST['title_'.$lang]);
                }
                if ($_POST['title_meta_'.$lang] == '') {
                    $_POST['title_meta_'.$lang] = SanitizeStrings::cleanTitleUrl($_POST['title_'.$lang]);
                }
            }

            /* gestione automatica dell'ordering de in input = 0 */
            $_POST['ordering'] = Sql::getMaxValueOfField($table, 'ordering', 'parent = '.intval($_POST['parent'])) + 1;

            /* imposta alias */
            $_POST['alias'] = $Module->getAlias('', $_POST['alias'], $_POST['title_it']);

            /* imposta url se presente */
            switch ($_POST['type']) {
                case 'module':
                    if ($_POST['module'] != '') {
                        $_POST['url'] = $_POST['module'];
                        $_POST['alias'] = $_POST['module'];
                    }
                    break;
                default:
                    break;
            }

            if (!isset($_POST['updated'])) {
                $_POST['updated'] = $App->nowDateTime;
            } else {
                $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['updated']);
                $errors = DateTime::getLastErrors();
                if ($errors['error_count'] > 0) {
                    $_POST['updated'] = $App->nowDateTime;
                } else {
                    $_POST['updated'] = $date->format('Y-m-d H:i:s');
                }
            }

            /* controlla i campi obbligatori */
            Sql::checkRequireFields($fields);
            if (Core::$resultOp->error == 0) {
                Sql::stripMagicFields($_POST);
                Sql::insertRawlyPost($fields, $table);
                $App->id = Sql::getLastInsertedIdVar();
                if (Core::$resultOp->error == 0) {
                    $App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
                    /* prende le opzioni template */
                    $App->templateItem = $Module->getTemplatePredefinito($_POST['id_template']);
                    /* modifica i contenuti associati */
                    if ($App->templateItem->contents_html > 0) {
                        $Module->updatePageContents($tableRif, $App->id, $App->templateItem->contents_html, $globalSettings['languages']);
                    }
                    /* images */
                    if ($App->templateItem->images > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageImages');
                    }
                    /* file */
                    if ($App->templateItem->files > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageFiles');
                    }
                    /* gallerie */
                    if ($App->templateItem->galleries > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageGalleries');
                    }
                    /* blocks */
                    if ($App->templateItem->blocks > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageBlocks');
                    }
                }
            }
        } else {
            Core::$resultOp->error = 1;
        }
        if (Core::$resultOp->error == 1) {
            $App->pageSubTitle = 'inserisci pagina';
            $App->viewMethod = 'formNew';
        } else {
            $App->viewMethod = 'list';
            Core::$resultOp->message = 'Pagina inserita!';
        }
        break;

    case 'update':
        $App->templateItem = new stdClass();
        $App->itemOld = new stdClass();
        if ($_POST) {
            if (!isset($_POST['created'])) {
                $_POST['created'] = $App->nowDateTime;
            }
            if (!isset($_POST['active'])) {
                $_POST['active'] = 0;
            }
            if (!isset($_POST['menu'])) {
                $_POST['menu'] = 0;
            }
            if (!isset($_POST['title_it']) || $_POST['title_it'] == '') {
                $_POST['title_it'] = 'Pagina Vuota';
                $_POST['active'] = 0;
            }
            foreach ($globalSettings['languages'] as $lang) {
                if ($_POST['title_seo_'.$lang] == '') {
                    $_POST['title_seo_'.$lang] = SanitizeStrings::cleanTitleUrl($_POST['title_'.$lang]);
                }
                if ($_POST['title_meta_'.$lang] == '') {
                    $_POST['title_meta_'.$lang] = SanitizeStrings::cleanTitleUrl($_POST['title_'.$lang]);
                }
            }

            /* preleva dati vecchio */
            Sql::initQuery($table, ['alias,parent'], [$App->id], 'id = ?');
            $App->itemOld = Sql::getRecord();

            /* imposta alias */
            $_POST['alias'] = $Module->getAlias($App->itemOld->alias, $_POST['alias'], $_POST['title_it']);

            /* imposta url se presente */
            switch ($_POST['type']) {
                case 'module':
                    if ($_POST['module'] != '') {
                        $_POST['url'] = $_POST['module'];
                        $_POST['alias'] = $_POST['module'];
                    }
                    break;
                default:
                    break;
            }

            /* se cambia parent aggiorna l'ordering */
            if ($_POST['parent'] != $App->itemOld->parent) {
                $_POST['ordering'] = Sql::getMaxValueOfField($table, 'ordering', 'parent = '.intval($_POST['parent'])) + 1;
            }

            if (!isset($_POST['updated'])) {
                $_POST['updated'] = $App->nowDateTime;
            } else {
                $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['updated']);
                $errors = DateTime::getLastErrors();
                if ($errors['error_count'] > 0) {
                    $_POST['updated'] = $App->nowDateTime;
                } else {
                    $_POST['updated'] = $date->format('Y-m-d H:i:s');
                }
            }

            /* controlla i campi obbligatori */
            Sql::checkRequireFields($fields);
            if (Core::$resultOp->error == 0) {
                Sql::stripMagicFields($_POST);
                Sql::updateRawlyPost($fields, $table, 'id', $App->id);
                if (Core::$resultOp->error == 0) {
                    /* sistema i parent se ne è stata selezionato uno diverso */
                    if ($_POST['parent'] != $_POST['bk_parent']) {
                        $Module->manageParentField();
                    }
                    /* prende le opzioni template */
                    $App->templateItem = $Module->getTemplatePredefinito($_POST['id_template']);
                    /* modifica i contenuti associati */
                    if ($App->templateItem->contents_html > 0) {
                        $Module->updatePageContents($tableRif, $App->id, $App->templateItem->contents_html, $globalSettings['languages']);
                    }
                    /* images */
                    if ($App->templateItem->images > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageImages');
                    }
                    /* file */
                    if ($App->templateItem->files > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageFiles');
                    }
                    /* gallerie */
                    if ($App->templateItem->galleries > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageGalleries');
                    }
                    /* blocks */
                    if ($App->templateItem->blocks > 0) {
                        $Module->updatePageItems($tableRif, $App->id, 'pageBlocks');
                    }
                }
            }
        } else {
            $App->error = 1;
        }
        if (Core::$resultOp->error == 1) {
            if (isset($_POST['id'])) {
                $App->id = $_POST['id'];
            }
            $App->pageSubTitle = 'modifica pagina';
            $App->viewMethod = 'formMod';
        } else {
            if (isset($_POST['submitForm'])) {
                $App->viewMethod = 'list';
                Core::$resultOp->message = 'Pagina modificata!';
            } else {
                if (isset($_POST['id'])) {
                    $App->id = $_POST['id'];
                    $App->pageSubTitle = 'modifica pagina';
                    $App->viewMethod = 'formMod';
                    Core::$resultOp->message = 'Modifiche applicate!';
                } else {
                    $App->viewMethod = 'formNew';
                    $App->pageSubTitle = 'inserisci pagina';
                }
            }
        }
        break;

    case 'page':
        $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS, $App->sessionName, 'page', $App->id);
        $App->viewMethod = 'list';
        break;

    default:
        $App->viewMethod = 'list';
        break;

    case 'message':
        Core::$resultOp->error = $App->id;
        Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
        $App->viewMethod = 'list';
        break;

    case 'ajaxLoadTemplateData':
        $template = $Module->getTemplatePredefinito(0);
        if (isset($template->id) && (int)$template->id > 0) {
            include_once(PATH.'application/'.Core::$request->action.'/templates/'.$App->templateUser.'/formTemplatesData.tpl.php');
        }
        $renderTpl = false;
        die();
        break;

    case 'ajaxReloadTemplateData':
        if ($App->id > 0) {
            $template = $Module->getTemplatePredefinito($App->id);
            if (isset($template->id) && (int)$template->id > 0) {
                include_once(PATH.'application/'.Core::$request->action.'/templates/'.$App->templateUser.'/formTemplatesData.tpl.php');
            }
        }
        $renderTpl = false;
        die();
        break;
}

/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch ((string)$App->viewMethod) {
    case 'formNew':
        $App->item = new stdClass();
        $App->item->updated = Config::$nowDateTimeIso;
        $App->item->created = Config::$nowDateTimeIso;
        $App->templateItem = new stdClass();
        $App->subCategories = new stdClass();

        /* select per parent */
        $opz = ['tableCat' => $table,'getbreadcrumbs' => 1];
        $App->subCategories = $CategoriesCle->getObjFromSubCategories($opz);
        //ToolsStrings::dump($App->subCategories);

        /* carica i dati del template */
        $App->templateItem = $Module->getTemplatePredefinito(0);
        if (!isset($App->templateItem->id) || (isset($App->templateItem->id) && (int)$App->templateItem->id == 0)) {
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/message/1/'.urlencode('Devi creare od attivare almeno un template!'));
        }
        //ToolsStrings::dump($App->templateItem);

        $App->templateItem->images = 3;
        $App->templateItem->files = 3;
        $App->templateItem->galleries = 3;
        $App->templateItem->blocks = 3;

        /* select per i template */
        $App->templatesItem = $Module->getTemplatesPage();
        $App->item->active = 1;
        $App->item->menu = 1;
        $App->item->alias = '';
        $App->item->parent = 0;
        $App->item->ordering = 0;

        /* prende i dati associati pagina->template */
        /* contenuti */
        if ($App->templateItem->contents_html > 0) {
            $App->item->pageContents =  $Module->getEmptyPageContents($App->templateItem->contents_html, 'content_it', $globalSettings['languages']);
        }

        /* file */
        if ($App->templateItem->files > 0) {
            $App->selectPageFiles = $Module->getSelectPageItems('pageFiles');
            $App->item->pageFiles = [];
        }

        /* images */
        if ($App->templateItem->images > 0) {
            if (!isset($App->selectPageImages)) {
                $App->selectPageImages = new stdClass();
            }
            $App->selectPageImages = $Module->getSelectPageItems('pageImages');
            $App->item->pageImages = [];
        }
        //ToolsStrings::dump($App->selectPageImages);

        /* galleries */
        if ($App->templateItem->galleries > 0) {
            $App->selectPageGalleries = $Module->getSelectPageItems('pageGalleries');
            $App->item->pageGalleries = [];
        }

        /* blocks */
        if ($App->templateItem->blocks > 0) {
            $App->selectPageBlocks = $Module->getSelectPageItems('pageBlocks');
            $App->item->pageBlocks = [];
        }

        if ($Module->error == 1) {
            Utilities::setItemDataObjWithPost($App->item, $fields);
        }

        $App->templateApp = 'form.tpl.php';
        $App->methodForm = 'insert';
        $App->defaultJavascript = "var moduleName = '".Core::$request->action."';";
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/pagesForm.js" type="text/javascript"></script>';

        break;

    case 'formMod':
        $App->item = new stdClass();
        $App->templateItem = new stdClass();
        $App->subCategories = new stdClass();

        /* select per parent */
        $opz = [
            'tableCat' => $table,
            'hideId' => true,
            'hideSons' => true,
            'rifId' => 'id',
            'rifIdValue' => $App->id,
            ];
        $App->subCategories = $CategoriesCle->getObjFromSubCategories($opz);

        ToolsStrings::dump($App->subCategories);

        Sql::initQuery($table, ['*'], [$App->id], 'id = ?');
        $App->item = Sql::getRecord();
        if (Core::$resultOp->error == 1) {
            Utilities::setItemDataObjWithPost($App->item, $fields);
        }

        /* carica i dati del template */

        $App->templateItem = $Module->getTemplatePredefinito($App->item->id_template);
        if (!isset($App->templateItem->id) || (isset($App->templateItem->id) && (int)$App->templateItem->id == 0)) {
            ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/message/1/'.urlencode('Devi creare od attivare almeno un template!'));
        }

        /* select per i template */
        $App->templatesItem = $Module->getTemplatesPage();

        /* prende i dati associati pagina->template */
        if ($App->templateItem->contents_html > 0) {
            $App->item->pageContents = $Module->getPageContents($tableRif, $App->id, $App->templateItem->contents_html, 'content_html_it', 'content_it', $globalSettings['languages']); /* id pagina,numero voci,nome input form,nome campo db */
        }

        /* file */
        if ($App->templateItem->files > 0) {
            if (!isset($App->selectPageFiles)) {
                $App->selectPageFiles = new stdClass();
            }
            $App->item->pageFiles = new stdClass();
            $App->selectPageFiles = $Module->getSelectPageItems('pageFiles');
            $App->item->pageFiles = $Module->getPageItems($tableRif, $App->id, 'pageFiles');
        }

        /* images */
        if ($App->templateItem->images > 0) {
            if (!isset($App->selectPageImages)) {
                $App->selectPageImages = new stdClass();
            }
            $App->item->pageImages = new stdClass();
            $App->selectPageImages = $Module->getSelectPageItems('pageImages');
            $App->item->pageImages = $Module->getPageItems($tableRif, $App->id, 'pageImages');
        }

        /* galleries */
        if ($App->templateItem->galleries > 0) {
            if (!isset($App->selectPageGalleries)) {
                $App->selectPageGalleries = new stdClass();
            }
            $App->item->pageGalleries = new stdClass();
            $App->selectPageGalleries = $Module->getSelectPageItems('pageGalleries');
            $App->item->pageGalleries = $Module->getPageItems($tableRif, $App->id, 'pageGalleries');
        }

        /* blocks */
        if ($App->templateItem->blocks > 0) {
            if (!isset($App->selectPageBloks)) {
                $App->selectPageBloks = new stdClass();
            }
            $App->item->pageBlocks = new stdClass();
            $App->selectPageBlocks = $Module->getSelectPageItems('pageBlocks');
            $App->item->pageBlocks = $Module->getPageItems($tableRif, $App->id, 'pageBlocks');
        }

        if (Core::$resultOp->error == 1) {
            Utilities::setItemDataObjWithPost($App->item, $fields);
        }

        $App->templateApp = 'form.tpl.php';
        $App->methodForm = 'update';
        $App->defaultJavascript = "var moduleName = '".Core::$request->action."';";
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/pagesForm.js" type="text/javascript"></script>';
        break;

    case 'list':
        $App->item = new stdClass();
        $App->item->updated = Config::$nowDateTimeIso;
        $App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 10);
        $App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
        Sql::setItemsForPage($App->itemsForPage);
        $Module->setMySessionApp($_MY_SESSION_VARS[$App->sessionName]);
        $opz = ['files' => $App->params->item_files,'tablefiles' => $App->tableIfil];
        $Module->listMainData($fields, $App->page, $App->itemsForPage, $globalSettings['languages'], $opz);
        $App->items = $Module->getMainData();
        //ToolsStrings::dump($App->items);

        $App->pagination = $Module->getPagination();
        $App->pageSubTitle = 'lista delle pagine dinamiche del sito';
        $App->templateApp = 'list.tpl.php';
        $App->css[] = '<link href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.css" rel="stylesheet">';
        $App->css[] = '<link href="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/pagesList.css" rel="stylesheet">';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.cookie/jquery.cookie.js" type="text/javascript"></script>';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.min.js" type="text/javascript"></script>';
        $App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'.Core::$request->action.'/pages.js" type="text/javascript"></script>';

        // no break
    default:
        break;
}
