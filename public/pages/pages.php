<?php

/* page.php v.3.5.4. 06/05/2019 */

//Config::$debugMode = 1;

$App->view = '';

/*
echo 'page_id: '.Core::$request->page_id;
echo '<br>page_alias: '.Core::$request->page_alias;
/*

*/

//ToolsStrings::dump(Core::$request);die();

if (Core::$resultOp->error == 0) {
    switch (Core::$request->method) {

        case 'df':
            if (intval(Core::$request->param) > 0) {
                $renderTpl = false;
                ToolsDownload::downloadFileFromDB2(
                    PATH_UPLOAD_DIR.'pages/',
                    [
                        'table'				=> DB_TABLE_PREFIX.'pages_resources',
                        'valuesClause'		=> [intval(Core::$request->param)],
                        'whereClause'		=> 'id = ? AND resource_type = 2',
                    ]
                );

                if (Core::$resultOp->error == 1) {
                    ToolsStrings::redirect(URL_SITE.'error/404');
                }
            } else {
                ToolsStrings::redirect(URL_SITE.'error/404');
            }
            die();
            break;

        default:

            if (Core::$request->param_id > 0 || Core::$request->param_alias != '') {

                /* preleva i dati della pagina */
                if (Core::$resultOp->error == 0) {

                    $where = 'active = 1 AND ';
                    if (isset(Core::$request->param) && Core::$request->param != '' && Core::$request->param == $globalSettings['site code key']) {
                        $where = '';
                    }

                    //$key = (isset(Core::$request->param) ? Core::$request->param : '');
                    //$auth = (isset(Core::$request->params[1]) ? Core::$request->params[1] : '');

                    //if ($key == $globalSettings['site code key'] && $auth == 'aprew') $where = '';
                    //echo '<br>preleva i dati della pagine';

                    Sql::initQuery(
                        DB_TABLE_PREFIX.'pages',
                        ['*'],
                        [
                        Core::$request->param_alias,
                        Core::$request->param_id,Core::$request->param_alias,Core::$request->param_id],
                        $where.'(alias = ? OR alias = ? OR id = ? OR id = ?)'
                    );
                    $App->pageData = Sql::getRecord();
                    //print_r($App->pageData);
                }

                if (Core::$resultOp->error == 0 && isset($App->pageData->id)) {

                    /* gestione titoli pagina */
                    $App->titles = Utilities::getTitlesPage(ucfirst((string) $_lang['pagina']), $App->pageData, $_lang['user'], []);
                    //print_r($App->titles);

                    /* preveva i dati del template */
                    //echo '<br>preleva i dati del template';
                    Sql::initQuery(DB_TABLE_PREFIX.'pagetemplates', ['*'], [$App->pageData->id_template], 'id = ?');
                    $App->templateData = Sql::getRecord();

                    if (Core::$resultOp->error == 0 && isset($App->templateData->id)) {

                        /* gestione datipagina */
                        $App->pageTitleMeta = strip_tags((string) Multilanguage::getLocaleObjectValue($App->pageData, 'title_meta_', $_lang['user'], []));
                        $App->pageTitleSeo = Multilanguage::getLocaleObjectValue($App->pageData, 'title_seo_', $_lang['user'], []);
                        $App->pageTitle = Multilanguage::getLocaleObjectValue($App->pageData, 'title_', $_lang['user'], []);

                        $App->pageData->content = Multilanguage::getLocaleObjectValue($App->pageData, 'content_', $_lang['user'], []);
                        $App->pageData->contentNoHtml = strip_tags((string) $App->pageData->content);
                        $App->pageData->contentNoP = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', (string) $App->pageData->content);

                        $App->pageData->title_alt = Multilanguage::getLocaleObjectValue($App->pageData, 'title_alt_', $_lang['user'], []);
                        $App->pageData->title_alt1 = Multilanguage::getLocaleObjectValue($App->pageData, 'title_alt1_', $_lang['user'], []);

                        $pagetitleseo = SanitizeStrings::urlslug($App->pageTitleSeo, $options = []);
                        $App->pageDataUrl = URL_SITE.Core::$request->action.'/'.$App->pageData->id.'/'.$pagetitleseo;

                        $App->pageData->updatedFormatted = DateFormat::getDateTimeIsoFormatString($App->pageData->updated, '%DAY% %STRINGMONTH% %YEAR%', []);

                        /* gestione immagine top e bottom pagina */
                        $App->pageData->image_top =  UPLOAD_DIR.'pages/default/default-image-top-pages.jpg';
                        $App->pageData->image_bottom = UPLOAD_DIR.'pages/default/default-image-bottom-pages.jpg';
                        if ($App->pageData->filename != '') {
                            $App->pageData->image_top =  UPLOAD_DIR.'pages/'.$App->pageData->filename;
                        }
                        //if ($App->pageData->filename1 != '') $App->pageData->image_bottom =  UPLOAD_DIR.'pages/'.$App->pageData->filename1;

                        $App->pageData->blocks = [];
                        $App->pageData->blocks_images = [];
                        $arr = [];
                        if (Core::$resultOp->error == 0) {

                            $itemsForPage = 6;
                            if (!isset($_SESSION[Core::$request->action]['page'])) {
                                $_SESSION[Core::$request->action]['page'] = 1;
                            }
                            if (isset(Core::$request->page) && Core::$request->page > 0) {
                                $_SESSION[Core::$request->action]['page'] = Core::$request->page;
                            }
                            //echo '<br>prelevo i contenuti della pagina';

                            //echo $_SESSION[Core::$request->action]['page'];

                            Sql::initQuery(DB_TABLE_PREFIX.'pages_blocks', ['*'], [$App->pageData->id], 'active = 1 AND id_owner= ?');
                            Sql::setOrder('ordering DESC');
                            Sql::setPage($_SESSION[Core::$request->action]['page']);
                            Sql::setItemsForPage($itemsForPage);
                            Sql::setResultPaged(true);
                            $obj = Sql::getRecords();

                            $App->blocks_pagination = Utilities::getPagination($_SESSION[Core::$request->action]['page'], Sql::getTotalsItems(), $itemsForPage);

                            //ToolsStrings::dump($App->blocks_pagination);

                            /* sistema dati singolo blocco */
                            if (is_array($obj) && is_array($obj) && count($obj) > 0) {
                                foreach ($obj as $value) {
                                    $value->title =  Multilanguage::getLocaleObjectValue($value, 'title_', $_lang['user'], []);
                                    $value->content = Multilanguage::getLocaleObjectValue($value, 'content_', $_lang['user'], ['parse' => 1]);
                                    $value->contentNoP = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', (string) $value->content);

                                    $arr[] = $value;

                                    /* prelevo i dati immagine associatio al blocco */
                                    Sql::initQuery(DB_TABLE_PREFIX.'pages_blocks_resources', ['*'], [$value->id], 'active = 1 AND id_owner = ? AND resource_type = 1');
                                    Sql::setOrder('ordering ASC');
                                    $objImg = Sql::getRecords();
                                    $arrImg = [];
                                    if (Core::$resultOp->error == 0) {
                                        if (is_array($objImg) && is_array($objImg) && count($objImg) > 0) {
                                            foreach ($objImg as $keyImg => $valueImg) {
                                                $valueImg->title =  Multilanguage::getLocaleObjectValue($valueImg, 'title_', $_lang['user'], []);
                                                $arrImg[] = $valueImg;
                                            }
                                        }
                                    }
                                    $App->pageData->blocks_images[$value->id] = $arrImg;
                                }

                            }
                        }
                        $App->pageData->blocks = $arr;

                        /* requpera i file associati pagina */
                        $App->pageData->files = [];
                        Sql::initQuery(DB_TABLE_PREFIX.'pages_resources', ['*'], [$App->pageData->id], 'active = 1 AND id_owner = ? AND resource_type = 2', '');
                        Sql::setOrder('ordering ASC');
                        $obj = Sql::getRecords();
                        if (Core::$resultOp->error == 1) {
                            break;
                        }
                        /* sistema i files */
                        $arr = [];
                        if (is_array($obj) && is_array($obj) && count($obj) > 0) {
                            foreach ($obj as $value) {
                                $value->title =  Multilanguage::getLocaleObjectValue($value, 'title_', $_lang['user'], []);
                                $value->image =  ToolsDownload::getFileIcon($value->filename, []);
                                $value->url = URL_SITE.'pages/df/'.$value->id.'/'.$value->org_filename;
                                $arr[] = $value;
                            }
                        }
                        $App->pageData_files = $arr;
                        //print_r($App->pageData_files);

                        /* requpera le immagini associati pagina */

                        $App->pageData->images = [];
                        Sql::initQuery(DB_TABLE_PREFIX.'pages_resources', ['*'], [$App->pageData->id], 'active = 1 AND id_owner = ? AND resource_type = 1', '');
                        Sql::setOrder('ordering ASC');
                        $obj = Sql::getRecords();
                        if (Core::$resultOp->error == 1) {
                            break;
                        }
                        /* sistema i files */
                        $arr = [];
                        if (is_array($obj) && is_array($obj) && count($obj) > 0) {
                            foreach ($obj as $value) {
                                $value->title =  Multilanguage::getLocaleObjectValue($value, 'title_', $_lang['user'], []);
                                $value->image =  ToolsDownload::getFileIcon($value->filename, []);
                                $arr[] = $value;
                            }
                        }
                        $App->pageData_images = $arr;
                        //print_r($App->pageData_images);

                        /* requpera la galleria associata pagina */

                        //ToolsStrings::dump(self::$request->urlparamrequest);

                        if (!isset($_SESSION[Core::$request->action]['galleriesimage_tag_id'])) {
                            $_SESSION[Core::$request->action]['galleriesimage_tag_id'] = '0';
                        }
                        if (Core::$request->param == 'gaimctag' && (isset(Core::$request->urlparamrequest[3]) && Core::$request->urlparamrequest[3] != '')) {
                            $_SESSION[Core::$request->action]['galleriesimage_tag_id'] = intval(Core::$request->urlparamrequest[3]);
                        }

                        //echo '<br>sessiion galleriesimage_tag_id: '.$_SESSION[Core::$request->action]['galleriesimage_tag_id'];
                        //die('fatto');

                        // preleva i tags dei prodotti nella categoria associata
                        $fv = [$App->pageData->galleriesimages_categories_id];
                        $where = 'active = 1 AND categories_id = ?';
                        $and = ' AND ';
                        Sql::initQuery(DB_TABLE_PREFIX.'galleriesimages', ['*'], $fv, $where, '');
                        $pdoObject = Sql::getPdoObjRecords();
                        if (Core::$resultOp->error > 0) {
                            //ToolsStrings::redirect(URL_SITE.'error/db'); die();
                        }
                        $App->pageData_galleriesimages_items_tags = [];

                        $App->pageData_galleriesimages_items_count = 0;

                        while ($row = $pdoObject->fetch()) {
                            $App->pageData_galleriesimages_items_count++;
                            $tags = '';
                            if ($row->id_tags != '') {
                                $tags = explode(',', (string) $row->id_tags);
                            }
                            if (is_array($tags) && count($tags) > 0) {
                                foreach ($tags as $value) {
                                    $foo = ltrim($value, 't');
                                    if (!isset($App->pageData_galleriesimages_items_tags[$foo])) {
                                        $App->pageData_galleriesimages_items_tags[$foo] = $foo;
                                    }

                                }
                            }
                        }

                        //echo '<br>items count: '.$App->pageData_galleriesimages_items_count;
                        //ToolsStrings::dump($App->pageData_galleriesimages_items_tags);//die();

                        // preleva i tags
                        Sql::initQuery(DB_TABLE_PREFIX.'galleriesimages_tags', ['*'], [], 'active = 1', '');
                        Sql::setOrder('title_'.Config::$langVars['user'].' ASC');
                        $pdoObject = Sql::getPdoObjRecords();
                        if (Core::$resultOp->error > 0) {
                            //ToolsStrings::redirect(URL_SITE.'error/db'); die();
                        }
                        $App->pageData_galleriesimages_tags = [];
                        while ($row = $pdoObject->fetch()) {
                            $row->title =  Multilanguage::getLocaleObjectValue($row, 'title_', $_lang['user'], []);
                            if (in_array($row->id, $App->pageData_galleriesimages_items_tags)) {
                                $App->pageData_galleriesimages_tags[$row->id] = $row;
                            }
                        }

                        //ToolsStrings::dump($App->pageData_galleriesimages_tags);//die();
                        //echo $_SESSION[Core::$request->action]['galleriesimage_tag_id'];

                        if (!array_key_exists($_SESSION[Core::$request->action]['galleriesimage_tag_id'], $App->pageData_galleriesimages_tags)) {
                            $_SESSION[Core::$request->action]['galleriesimage_tag_id'] = 0;
                        }

                        //echo $_SESSION[Core::$request->action]['galleriesimage_tag_id'];

                        $App->pageData->galleriesimages = [];
                        $fv = [$App->pageData->galleriesimages_categories_id];
                        $where = 'active = 1 AND categories_id = ?';
                        $and = ' AND ';
                        if (intval($_SESSION[Core::$request->action]['galleriesimage_tag_id']) > 0) {
                            $fv[] = '%t'.intval($_SESSION[Core::$request->action]['galleriesimage_tag_id']).'%';
                            $where .= $and.'id_tags LIKE ?';
                        }

                        //ToolsStrings::dump($fv);

                        //Config::$debugMode = 1;
                        Sql::initQuery(DB_TABLE_PREFIX.'galleriesimages', ['*'], $fv, $where, '');
                        Sql::setOrder('ordering DESC');
                        $pdoObject = Sql::getPdoObjRecords();

                        if (Core::$resultOp->error == 1) {
                            break;
                        }
                        $App->pageData_galleriesimages = [];
                        while ($row = $pdoObject->fetch()) {
                            $row->title =  Multilanguage::getLocaleObjectValue($row, 'title_', $_lang['user'], []);
                            $row->content = Multilanguage::getLocaleObjectValue($row, 'content_', $_lang['user'], []);
                            $row->contentNoP = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', (string) $row->content);
                            $App->pageData_galleriesimages[] = $row;
                        }

                        if (Core::$resultOp->error == 0) {
                            if (isset($dataMenuPages[$App->pageData->alias]->breadcrumbs) && is_array($dataMenuPages[$App->pageData->alias]->breadcrumbs) && count($dataMenuPages[$App->pageData->alias]->breadcrumbs) > 0) {
                                array_pop($dataMenuPages[$App->pageData->alias]->breadcrumbs);
                            }

                            /* aggiorna breadcrumbs */
                            if (isset($dataMenuPages[$App->pageData->alias]->breadcrumbs)) {
                                $breadcrumbs = $dataMenuPages[$App->pageData->alias]->breadcrumbs;
                            }
                            $x = 1;
                            if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0) {
                                foreach ($breadcrumbs as $key => $value) {
                                    if ($value['sons'] == 0) {
                                        $url = URL_SITE.Core::$request->action.'/'.$value['alias'];

                                        $title = Multilanguage::getLocaleArrayValue($value, 'title_', Config::$langVars['user'], []);
                                        $App->breadcrumbs->items[$x] = ['class' => 'breadcrumb-item','url' => $url,'title' => $title];
                                    } else {
                                        $title = Multilanguage::getLocaleArrayValue($value, 'title_', Config::$langVars['user'], []);
                                        $App->breadcrumbs->items[$x] = ['class' => 'breadcrumb-item','url' => '','title' => $title];
                                    }

                                    $x++;
                                }
                            }
                            $App->breadcrumbs->items[$x] = ['class' => 'breadcrumb-item active','url' => '','title' => strip_tags((string) $App->pageTitle)];
                            $App->breadcrumbs->title = $App->pageTitle;
                            $App->breadcrumbs->tree =  Utilities::generateBreadcrumbsTree($App->breadcrumbs->items, $_lang, ['template' => $templateBreadcrumbsBar]);

                            $App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->titles['titleMeta'].$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
                            $App->metaDescriptionPage = Multilanguage::getLocaleObjectValue($App->pageData, 'meta_description_', $_lang['user'], []);
                            ;
                            $App->metaKeywordsPage = Multilanguage::getLocaleObjectValue($App->pageData, 'meta_keyword_', $_lang['user'], []);
                            ;
                            $App->view = '';

                            /* imposta il template in uso dalla pagina */
                            $App->templateApp = $App->templateData->template;
                            if (isset($App->templateData->base_tpl_page) && $App->templateData->base_tpl_page != '') {
                                $App->templateApp = $App->templateData->base_tpl_page;
                            }

                        } else {
                            //ToolsStrings::redirect(URL_SITE.'error/db');
                            die();
                        }

                    }
                }

            }

            break;
    }
}

switch ($App->view) {
    default:
        break;
}
