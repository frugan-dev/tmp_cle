#!/usr/bin/env php
<?php

/* cron/create_sitemap.php */
//header("Content-type: text/xml; charset=utf-8");
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

define('PATH', dirname(__DIR__).'/');

include_once(PATH.'wscms/include/configuration.inc.php');

// autoload by composer
//include_once(PATH."wscms/classes/class.Config.php");
//include_once(PATH."wscms/classes/class.Core.php");
//include_once(PATH."wscms/classes/class.Sessions.php");
//include_once(PATH."wscms/classes/class.Sql.php");
//include_once(PATH."wscms/classes/class.Subcategories.php");
//include_once(PATH."wscms/classes/class.Categories.php");
//include_once(PATH."wscms/classes/class.Pages.php");
//include_once(PATH."wscms/classes/class.SanitizeStrings.php");

$Config = new Config();
Config::setGlobalSettings($globalSettings);
$Core = new Core();
define('DB_TABLE_PREFIX', Sql::getTablePrefix());

//Sql::setDebugMode(1);

$tablesDb = Sql::getTablesDatabase($globalSettings['database'][DATABASE]['name']);
//print_r($tablesDb);

Sql::initQuery(DB_TABLE_PREFIX.'modules', ['*'], [], 'active = 1');
Sql::setOptions(['fieldTokeyObj' => 'name']);
$modules = Sql::getRecords();

$element = [];

if (array_key_exists('pages', $modules) && in_array(DB_TABLE_PREFIX.'pages', $tablesDb)) {
    $pages = Pages::setMainTreePagesData(['table' => 'pages','languages' => 'NULL','getbreadcrumbs' => 0]);
    //print_r($pages);
    if (isset($pages) && is_array($pages) && count($pages) > 0) {
        foreach ($pages as $page) {
            foreach ($globalSettings['languages'] as $lang) {
                $url = URL_SITE.$lang.'/'.$page->alias;
                $datetime = new DateTime($page->created);
                $lastmod = $datetime->format('Y-m-d\TH:i:sP');
                $changefreq = 'daily';
                $priority = '1.0';
                $element[] = [
                    'url'					=> $url,
                    'lastmod'				=> $lastmod,
                    'changefreq'			=> $changefreq,
                    'priority'              => $priority,
                ];
            }
        }
    }
}

if (array_key_exists('ecommerce', $modules) && in_array(DB_TABLE_PREFIX.'ec_categories', $tablesDb)) {
    $categories = Categories::getCategoriesListAll(['tablecat' => 'ec_categories','tablepro' => 'ec_products','sqloptions' => ['fieldTokeyObj' => 'id']]);
    if (isset($categories) && is_array($categories) && count($categories) > 0) {
        foreach ($categories as $category) {

            // crea per ogni categoria
            foreach ($globalSettings['languages'] as $lang) {
                $title = $category->alias;
                $t = 'title_seo_'.$lang;
                if (isset($category->$t) && $category->$t != '') {
                    $title = $category->$t;
                }
                $url = URL_SITE.$lang.'/products/'.$category->id.'/'.SanitizeStrings::urlslug($title, ['delimiter' => '-']);
                $datetime = new DateTime($category->created);
                $lastmod = $datetime->format('Y-m-d\TH:i:sP');
                $changefreq = 'daily';
                $priority = '1.0';
                $element[] = [
                    'url'					=> $url,
                    'lastmod'				=> $lastmod,
                    'changefreq'			=> $changefreq,
                    'priority'              => $priority,
                ];
            }

            /* crea i prodotti */
            if (in_array(DB_TABLE_PREFIX.'ec_products', $tablesDb)) {
                /* trova i prodotti */
                $fieldValues = [];
                $fieldValues[] = $category->id;
                $where = 'active = 1 AND id_cat = ?';
                Sql::initQuery(DB_TABLE_PREFIX.'ec_products', ['*'], $fieldValues, $where);
                $products = Sql::getRecords();
                if (isset($products) && is_array($products) && count($products) > 0) {
                    foreach ($products as $product) {
                        foreach ($globalSettings['languages'] as $lang) {
                            $t = 'title_'.$lang;
                            $title = $product->$t;
                            $t1 = 'title_seo_'.$lang;
                            if (isset($product->$t) && $product->$t1 != '') {
                                $title = $product->$t1;
                            }
                            $url = URL_SITE.$lang.'/products/dt/'.$product->id.'/'.SanitizeStrings::urlslug($title, ['delimiter' => '-']);
                            $datetime = new DateTime($product->created);
                            $lastmod = $datetime->format('Y-m-d\TH:i:sP');
                            $changefreq = 'daily';
                            $priority = '1.0';
                            $element[] = [
                                'url'					=> $url,
                                'lastmod'				=> $lastmod,
                                'changefreq'			=> $changefreq,
                                'priority'              => $priority,
                            ];
                        }
                    }
                }
            }

        }
    }

}

//print_r($element);

//create your XML document, using the namespaces
$urlset = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" />');

foreach ($element as $item) {

    //add the page URL to the XML urlset
    $url = $urlset->addChild('url');
    $url->addChild('loc', $item['url']);
    $url->addChild('lastmod', $item['lastmod']);
    $url->addChild('changefreq', $item['changefreq']);  //weekly etc.
    $url->addChild('priority', $item['priority']);

    //add an image
    if (isset($item['image_url'])):
        $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
        $image->addChild('image:loc', $item['image_url'], 'http://www.google.com/schemas/sitemap-image/1.1');
        $image->addChild('image:caption', $item->IMAGE->ALT_OR_TITLE, 'http://www.google.com/schemas/sitemap-image/1.1');
    endif;
}

//add whitespaces to xml output (optional, of course)
$dom = new DomDocument();
$dom->loadXML($urlset->asXML());
$dom->formatOutput = true;
$dom->save('../sitemap.xml');

echo 'fatto!';
