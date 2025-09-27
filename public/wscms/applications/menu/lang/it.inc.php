<?php

/* v.3.5.4. 05/08/2019 */

Config::$langVars = array_merge(Config::$langVars, [
    'voce' => 'menu',
    'voci' => 'menu',
    'menu-type-vars' =>  [
        'menupages' => ['varreplace' => '%MENUPAGES%','title' => 'Il menu pagine dinamiche','info' => 'Il menu generato dal modulo pages è in genere il menu principale del sito, dove vengono mostrate nel menu le pagine dinamiche gestite dal modulo stesso'],
        'menuvecchiepages' => ['varreplace' => '%MENUPAGESOLD%','title' => 'Il menu vecchie pagine dinamiche','info' => 'Il menu generato dal vecchio ed obsoleto modulo Pagine ecchio è in genere il menu mantenuto per compatibilità dei vecchi contenuti, dove vengono mostrate nel menu le pagine dinamiche gestite dal modulo stesso'],
        //'menupagelists'=>array('varreplace'=>'%MENUPAGESLIST%','title'=>'Il menu vecchie pagine con blocchi dinamiche','info'=>'Il menu generato dal vecchio ed obsoleto modulo Pagine con Blocchi è in genere il menu mantenuto per compatibilità dei vecchi contenuti, dove vengono mostrate nel menu le pagine dinamiche gestite dal modulo stesso'),
        //'menusubcategories'=>array('varreplace'=>'/%MENUSUBCATEGORIES%/','title'=>'Il menu sottocategorie','info'=>'Il menu generato dal modulo prodotti, ecommerce od equivalente dove vengono mostrate nel menu le categorie ad albero (sottocategorie) del catalogo prodotti del sito'),

        //'menucategories'=>array('varreplace'=>'%MENUCATEGORIES%','title'=>'Il menu categorie','info'=>'Il menu generato dal modulo prodotti, ecommerce od equivalenti e dove vengono mostrate nel menu le categorie del catalogo prodotti del sito'),
        //'menuproducts'=>array('varreplace'=>'/%MENUPRODUCTS%/','title'=>'Il menu prodotti','info'=>'Il menu generato dal modulo prodotti, ecommerce od equivalenti e dove vengono mostrati nel menu i prodotti del catalogo prodotti del sito'),
    ],

    'link a modulo' => 'link a modulo',

]);
$_lang['voce'] = 'menu';
$_lang['voci'] = 'menu';
