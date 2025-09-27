<?php

/* wscms/home/extra.php v.3.5.4. 06/08/2019 */

/* SLIDE HOME REV */
if (in_array(DB_TABLE_PREFIX.'slides_home_rev', $tablesDb) && file_exists(PATH.$App->pathApplications.'slides-home-rev/index.php') && Permissions::checkAccessUserModule('slides-home-rev', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['slides-home-rev'] = [
        'table' => DB_TABLE_PREFIX.'slides_home_rev',
        'icon panel' => 'fa-picture-o',
        'label' => ucfirst((string) $_lang['slide']),
        'sex suffix' => ucfirst((string) $_lang['nuove']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'slides-home-rev',
            'opt' => [],
            ],
        ];
    $App->homeTables['slides-home-rev'] = [
        'table' => DB_TABLE_PREFIX.'slides_home_rev',
        'icon panel' => 'fa-picture-o',
        'label' => ucfirst((string) $_lang['ultime']).' '.$_lang['slide'],
        'fields' => [
            'title' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'slides-home-rev',
                    'opt' => [
                        'fieldItemRif' => 'id_owner',
                        ],
                    ],
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'slides-home-rev/',
                'path' => UPLOAD_DIR.'slides-home-rev/',
                ],
            ],
        ];

}

/* SLIDE HOME */
if (in_array(DB_TABLE_PREFIX.'slides_home', $tablesDb) && file_exists(PATH.$App->pathApplications.'slides-home/index.php') && Permissions::checkAccessUserModule('slides-home', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['slides-home'] = [
        'table' => DB_TABLE_PREFIX.'slides_home',
        'icon panel' => 'fa-picture-o',
        'label' => ucfirst((string) $_lang['slide']),
        'sex suffix' => ucfirst((string) $_lang['nuove']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'slides-home-rev',
            'opt' => [],
            ],
        ];
    $App->homeTables['slides-home'] = [
        'table' => DB_TABLE_PREFIX.'slides_home',
        'icon panel' => 'fa-picture-o',
        'label' => ucfirst((string) $_lang['ultime']).' '.$_lang['slide'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'slides-home',
                    'opt' => [
                        'fieldItemRif' => 'id_owner',
                        ],
                    ],
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'slides-home/',
                'path' => UPLOAD_DIR.'slides-home/',
                ],
            ],
        ];

}

/* NEWS */
if (in_array(DB_TABLE_PREFIX.'news', $tablesDb) && file_exists(PATH.$App->pathApplications.'news/index.php') && Permissions::checkAccessUserModule('news', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['news'] = [
        'table' => DB_TABLE_PREFIX.'news',
        'icon panel' => 'fa-newspaper-o',
        'label' => ucfirst((string) $_lang['notizie']),
        'sex suffix' => ucfirst((string) $_lang['nuove']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'news',
            'opt' => [],
            ],
        ];
    $App->homeTables['news'] = [
        'table' => DB_TABLE_PREFIX.'news',
        'icon panel' => 'fa-newspaper',
        'label' => ucfirst((string) $_lang['ultime']).' '.$_lang['notizie'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'news',
                    'opt' => [
                        'fieldItemRif' => '',
                        ],
                    ],
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'news/',
                'path' => UPLOAD_DIR.'news/',
                ],
            ],
        ];
}

/* BLOG */
if (in_array(DB_TABLE_PREFIX.'blog', $tablesDb) && file_exists(PATH.$App->pathApplications.'blog/index.php') && Permissions::checkAccessUserModule('news', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['blog'] = [
        'table' => DB_TABLE_PREFIX.'blog',
        'icon panel' => 'fa-comments',
        'label' => ucfirst((string) $_lang['post']),
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'blog',
            'opt' => [],
            ],
        ];
    $App->homeTables['blog'] = [
        'table' => DB_TABLE_PREFIX.'blog',
        'icon panel' => 'fa-comments',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['post'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'blog',
                    'opt' => [
                        'fieldItemRif' => '',
                        ],
                    ],
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'blog/',
                'path' => UPLOAD_DIR.'blog/',
                ],
            ],
        ];

}

/* FAQ */
if (in_array(DB_TABLE_PREFIX.'faq', $tablesDb) && file_exists(PATH.$App->pathApplications.'faq/index.php') && Permissions::checkIfModulesIsReadable('faq', $App->userLoggedData, $App->user_module_active) == true) {
    //Core::setDebugMode(1);
    $App->homeBlocks['faq'] = [
        'table' => DB_TABLE_PREFIX.'faq',
        'icon panel' => 'fa-question',
        'label' => ucfirst((string) $_lang['faq']),
        'sex suffix' => ucfirst((string) $_lang['nuove']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'news',
            'opt' => [],
            ],
        ];
    $App->homeTables['faq'] = [
        'table' => DB_TABLE_PREFIX.'faq',
        'icon panel' => 'fa-question',
        'label' => ucfirst((string) $_lang['ultime']).' '.$_lang['faq'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'faq',
                    'opt' => [
                        'fieldItemRif' => '',
                        ],
                    ],
                ],
            ],
        ];

}

if (in_array(DB_TABLE_PREFIX.'products', $tablesDb) && file_exists(PATH.$App->pathApplications.'products/index.php') && Permissions::checkAccessUserModule('products', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['products'] = [
        'table' => DB_TABLE_PREFIX.'products',
        'icon panel' => 'fa-tags',
        'label' => ucfirst((string) $_lang['prodotti']),
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'products',
            'opt' => [],
            ],
        ];
    $App->homeTables['products'] = [
        'table' => DB_TABLE_PREFIX.'products',
        'icon panel' => 'fa-tags',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['prodotti'],
        'fields' => [
            'code' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['codice']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'products',
                    'opt' => [
                        'fieldItemRif' => '',
                        ],
                    ],
                ],
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => false,
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'products/',
                'path' => UPLOAD_DIR.'products/',
                ],
            ],
        ];

}

if (in_array(DB_TABLE_PREFIX.'ec_products', $tablesDb) && file_exists(PATH.$App->pathApplications.'ecommerce/index.php') && Permissions::checkAccessUserModule('ecommerce', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['ec_products'] = [
        'table' => DB_TABLE_PREFIX.'ec_products',
        'icon panel' => 'fa-tags',
        'label' => ucfirst((string) $_lang['prodotti']),
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'ecommerce',
            'opt' => [],
            ],
        ];
    $App->homeTables['ec_products'] = [
        'table' => DB_TABLE_PREFIX.'ec_products',
        'icon panel' => 'fa-tags',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['prodotti'],
        'fields' => [
        /*
            'code'=>array(
                'multilanguage'=>0,
                'type'=>'varchar',
                'label'=>ucfirst($_lang['codice']),
                'url'=>true,
                'url item'=>array(
                    'string'=>URL_SITE_ADMIN.'products',
                    'opt'=>array(
                        'fieldItemRif'=>''
                        )
                    )
                ),
        */
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => false,
                ],
            'filename' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['immagine']),
                'type' => 'image',
                'pathdef' => UPLOAD_DIR.'ecommerce/products/',
                'path' => UPLOAD_DIR.'ecommerce/products/',
                ],
            ],
        ];

}

/* NEWSLETTER */
if (in_array(DB_TABLE_PREFIX.'newsletter_indirizzi', $tablesDb) && file_exists(PATH.$App->pathApplications.'/newsletter/index.php') && Permissions::checkAccessUserModule('newsletter', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['newsletter-indsos'] = [
        'table' => DB_TABLE_PREFIX.'newsletter_indirizzi',
        'query opt' => ['clause' => 'created > ? AND confirmed = 0'],
        'icon panel' => 'fa-user-secret',
        'label' => ucfirst((string) $_lang['indirizzi sospesi']).' '.$_lang['newsletter'],
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'newsletter/listIndSos',
            'opt' => [],
            ],
        ];

    $App->homeTables['newsletter-indsos'] = [
        'table' => DB_TABLE_PREFIX.'newsletter_indirizzi',
        'query opt' => ['clause' => 'confirmed = 0'],
        'icon panel' => 'fa-user-secret',
        'label' => ucfirst((string) $_lang['indirizzi sospesi']).' '.$_lang['newsletter'],
        'fields' => [
            'email' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['email']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'newsletter/listIndSos',
                    'opt' => [],
                    ],
                ],
            ],
        ];

    $App->homeBlocks['newsletter-ind'] = [
        'table' => DB_TABLE_PREFIX.'newsletter_indirizzi',
        'query opt' => ['clause' => 'created > ? AND confirmed = 1'],
        'icon panel' => 'fa-user',
        'label' => ucfirst((string) $_lang['indirizzi']).' '.$_lang['newsletter'],
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'newsletter/listInd',
            'opt' => [],
            ],
        ];

    $App->homeTables['newsletter-ind'] = [
        'table' => DB_TABLE_PREFIX.'newsletter_indirizzi',
        'query opt' => ['clause' => 'confirmed = 1'],
        'icon panel' => 'fa-user',
        'label' => ucfirst((string) $_lang['indirizzi']).' '.$_lang['newsletter'],
        'fields' => [
            'email' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['email']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'newsletter/listInd',
                    'opt' => [],
                    ],
                ],
            ],
        ];
}

/* PORTFOLIO */
if (in_array('portfolio', $tablesDb) && file_exists(PATH.'application/portfolio/index.php') && Permissions::checkAccessUserModule('portfolio', $App->userLoggedData, $App->user_modules_active) == true) {
    Sql::initQuery(DB_TABLE_PREFIX.'portfolio', ['*'], [], $clause = '', $order = 'created DESC', $limit = ' LIMIT 5 OFFSET 0', $options = '', false);
    $App->items = Sql::getRecords();
    Sql::initQuery(DB_TABLE_PREFIX.'portfolio', ['id'], [$App->lastLogin], 'created > ?', '', '', '', false);
    $numItems = Sql::countRecord();
    $App->moduleHome['portfolio'] = ['urlDetails' => URL_SITE_ADMIN.'portfolio/listItem','urlItem' => ['string' => '{{URLSITEADMIN}}portfolio/listItem','opz' => ['filedvalue' => 'id_cat']],'label' => 'Ultimi Lavori','fieldsData' => $App->items,'fields' => ['title_it' => ['label' => 'Titolo','url' => true],'filename' => ['label' => 'Immagine','type' => 'image','path' => UPLOAD_DIR.'portfolio/']],'countnew' => true,'sexSuffix' => 'i','label count' => 'Nuovi Lavori','icon count' => 'fa-tags','type count' => 'info','numItems' => $numItems];
}

if (in_array('portfolio_images', $tablesDb) && file_exists(PATH.'application/portfolio/index.php') && Permissions::checkAccessUserModule('portfolio', $App->userLoggedData, $App->user_modules_active) == true) {
    Sql::initQuery(DB_TABLE_PREFIX.'portfolio_images', ['*'], [], $clause = '', $order = 'created DESC', $limit = ' LIMIT 5 OFFSET 0', $options = '', false);
    $App->items = Sql::getRecords();
    Sql::initQuery(DB_TABLE_PREFIX.'portfolio_images', ['id'], [$App->lastLogin], 'created > ?', '', '', '', false);
    $numItems = Sql::countRecord();
    $App->moduleHome['portfolio-images'] = ['urlDetails' => URL_SITE_ADMIN.'portfolio/listIimg','urlItem' => ['string' => '{{URLSITEADMIN}}portfolio/listIimg','opz' => ['filedvalue' => 'id_owner']],'label' => 'Ultime Immagini Lavori','fieldsData' => $App->items,'fields' => ['title_it' => ['label' => 'Titolo','url' => true],'filename' => ['label' => 'Immagine','type' => 'image','pathdef' => UPLOAD_DIR.'portfolio/','path' => UPLOAD_DIR.'portfolio/images/']],'countnew' => true,'sexSuffix' => 'e','label count' => 'Nuove Immagini Lavori','icon count' => 'fa-picture-o','type count' => 'info','numItems' => $numItems];
}

if (in_array('portfolio_cat', $tablesDb) && file_exists(PATH.'application/portfolio/index.php') && Permissions::checkAccessUserModule('portfolio', $App->userLoggedData, $App->user_modules_active) == true) {
    //Sql::initQuery(DB_TABLE_PREFIX.'portfolio_cat',array('*'),array(),$clause='',$order='created DESC',$limit=' LIMIT 5 OFFSET 0',$options='',false);
    //$App->items = Sql::getRecords();
    Sql::initQuery(DB_TABLE_PREFIX.'portfolio_cat', ['id'], [$App->lastLogin], 'created > ?', '', '', '', false);
    $numItems = Sql::countRecord();
    $App->moduleHome['portfolio-cat'] = ['urlDetails' => URL_SITE_ADMIN.'portfolio/listCate','urlItem' => ['string' => '{{URLSITEADMIN}}portfolio/listCate','opz' => ['filedvalue' => 'id']],'label' => 'Ultime Categorie Lavori','fieldsData' => '','fields' => ['title_it' => ['label' => 'Titolo','url' => true],'filename' => ['label' => 'Immagine','type' => 'image','pathdef' => UPLOAD_DIR.'portfolio/','path' => UPLOAD_DIR.'/portfolio/categories/']],'countnew' => true,'sexSuffix' => 'e','label count' => 'Nuove Categorie Lavori','icon count' => 'fa-folder-o','class count' => 'panel-green','numItems' => $numItems];
}

/* VIDEO */
if (in_array(DB_TABLE_PREFIX.'video', $tablesDb) && file_exists(PATH.$App->pathApplications.'video/index.php') && Permissions::checkAccessUserModule('news', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['video'] = [
        'table' => DB_TABLE_PREFIX.'video',
        'icon panel' => 'fa-youtube',
        'label' => ucfirst((string) $_lang['video']),
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'video',
            'opt' => [],
            ],
        ];
    $App->homeTables['youtube'] = [
        'table' => DB_TABLE_PREFIX.'video',
        'icon panel' => 'fa-youtube',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['video'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'video',
                    'opt' => [
                        'fieldItemRif' => '',
                        ],
                    ],
                ],
            ],
        ];

}

/* ECOMMERCE */

if (in_array(DB_TABLE_PREFIX.'ec_customers', $tablesDb) && file_exists(PATH.$App->pathApplications.'/ecommerce/index.php') && Permissions::checkAccessUserModule('ecommerce', $App->userLoggedData, $App->user_modules_active) == true) {
    $App->homeBlocks['ec_customers'] = [
    'table' => DB_TABLE_PREFIX.'ec_customers',
    'icon panel' => 'fa-user',
    'label' => ucfirst((string) $_lang['clienti']),
    'sex suffix' => ucfirst((string) $_lang['nuovi']),
    'type' => 'info',
    'url' => true,
    'url item' => [
        'string' => URL_SITE_ADMIN.'ecommerce/listCust',
        'opt' => [],
        ],
    ];

    $App->homeTables['ec_customers'] = [
        'table' => DB_TABLE_PREFIX.'ec_customers',
        'icon panel' => 'fa-user',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['clienti'],
        'fields' => [
            'email' => [
                'multilanguage' => 0,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['clienti']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'ecommerce/listCust',
                    'opt' => [],
                    ],
                ],
            ],
        ];

    /*
        $App->homeBlocks['ec_donations'] = array(
            'table'=>DB_TABLE_PREFIX.'ec_donations',
            'query opt'=>array('clause' => 'modified_date > ?'),
            'icon panel'=>'fa-money',
            'label'=>'Donazioni',
            'sex suffix'=>ucfirst($_lang['nuove']),
            'type'=>'info',
            'url'=>true,
            'url item'=>array (
                'string'=>URL_SITE_ADMIN.'ecommerce/listDona',
                'opt'=>array()
                )
            );


        $App->homeTables['ec_donations'] = array(
            'table'=>DB_TABLE_PREFIX.'ec_donations',
            'query opt'=>array('order'=>'modified_date DESC','formatdataorder'=>'date','fieldcreated'=>'modified_date'),
            'icon panel'=>'fa-money',
            'label'=>ucfirst($_lang['ultime']).' Donazioni',
            'fields'=>array(
                'pagamento'=>array(
                    'multilanguage'=>0,
                    'type'=>'arraykey',
                    'array'=>$_lang['label pagamento'],
                    'label'=>'Tipo',
                    'url'=>true,
                    'url item'=>array(
                        'string'=>URL_SITE_ADMIN.'ecommerce/listDona',
                        'opt'=>array()
                        )
                    ),
                'frequency'=>array(
                    'multilanguage'=>0,
                    'type'=>'arraykey',
                    'array'=>$_lang['label frequenza'],
                    'label'=>'Pagamento',
                    'url'=>true,
                    'url item'=>array(
                        )
                    ),
                'frequency_value'=>array(
                    'multilanguage'=>0,
                    'type'=>'amount',
                    'currency'=>'€',
                    'label'=>'Euro',
                    'url'=>false,
                    'url item'=>array(
                        )
                    )
                )
            );
    */
}
