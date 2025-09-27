<?php

$DatabaseTables = [];
$DatabaseTablesFields = [];

// galleries images
$DatabaseTables['galleriesimages categories'] = DB_TABLE_PREFIX.'galleriesimages_categories';
$DatabaseTablesFields['galleriesimages categories'] = [
        'id'									=> [
        'label'									=> 'ID',
        'required'								=> false,
        'type'									=> 'autoinc',
        'primary'								=> true,
    ],
    'created'                                   => [
        'label'                                 => Config::$langVars['creazione'],
        'searchTable'                           => false,
        'required'                              => false,
        'type'                                  => 'datatime',
        'defValue'                              => Config::$nowDateTimeIso,
        'forcedValue'                           => Config::$nowDateTimeIso,
    ],
    'active'                                    => [
        'label'                                 => Config::$langVars['attiva'],
        'required'                              => false,
        'type'                                  => 'int|1',
        'defValue'                              => 1,
        'forcedValue'                           => 1,
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['galleriesimages categories']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar'];
}

$DatabaseTables['galleriesimages'] = DB_TABLE_PREFIX.'galleriesimages';
$DatabaseTablesFields['galleriesimages']  = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'categories_id' => ['label' => 'ID '.Config::$langVars['categoria'],'searchTable' => false,'required' => true,'type' => 'int|8','defValue' => 0,
    'forcedValue'                           			=> 0,
    ],
    /*
    'id_user'=>array('label'=>Config::$langVars['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>0),*/
    'filename' => ['label' => 'Nome File','searchTable' => false,'required' => false,'type' => 'varchar|255','defValue' => ''],
    'org_filename' => ['label' => '','searchTable' => true,'required' => false,'type' => 'varchar255','defValue' => ''],
    'ordering' => ['label' => 'Ord','required' => false,'type' => 'int|8','defValue' => 1,
    'forcedValue'                           			=> 0,
    ],
    'id_tags'											=> [
        'label'											=> 'Id Tags',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
    ],
    /*
    'url'=>array('URL'=>'Alias','searchTable'=>true,'required'=>false,'type'=>'varchar|255','defValue'=>''),
    'target'=>array('label'=>'Target','searchTable'=>true,'required'=>false,'type'=>'varchar|20','defValue'=>''),
    'access_type'=>array('label'=>Config::$langVars['tipo accesso'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0'),
    'access_read'=>array('label'=>Config::$langVars['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
    'access_write'=>array('label'=>Config::$langVars['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
    */
    'created'                                   		=>  [
        'label'                                 		=> Config::$langVars['creazione'],
        'searchTable'                           		=> false,
        'required'                              		=> false,
        'type'                                  		=> 'datatime',
        'defValue'                              		=> Config::$nowDateTimeIso,
        'forcedValue'                           		=> Config::$nowDateTimeIso,
    ],
    'active'                                    		=>  [
        'label'                                 		=> Config::$langVars['attiva'],
        'required'                              		=> false,
        'type'                                  		=> 'int|1',
        'defValue'                              		=> 1,
        'forcedValue'                           		=> 1,
    ],
];

foreach (Config::$globalSettings['languages'] as $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['galleriesimages']['title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => $required,'type' => 'varchar|255','defValue' => ''];
}

$DatabaseTables['galleriesimages tags'] = DB_TABLE_PREFIX.'galleriesimages_tags';
$DatabaseTablesFields['galleriesimages tags'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'created'                                   		=> [
        'label'                                 		=> Config::$langVars['creazione'],
        'searchTable'                           		=> false,
        'required'                              		=> false,
        'type'                                  		=> 'datatime',
        'defValue'                              		=> Config::$nowDateTimeIso,
        'forcedValue'                           		=> Config::$nowDateTimeIso,
    ],
    'active'                                   			=> [
        'label'                                 		=> Config::$langVars['attiva'],
        'required'                              		=> false,
        'type'                                  		=> 'int|1',
        'defValue'                              		=> 1,
        'forcedValue'                           		=> 1,
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['galleriesimages tags']['title_'.$lang] = [
        'label'											=> 'Titolo '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
        'error message'									=> preg_replace('/%ITEM%/', (string) Config::$langVars['titolo'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
    ];
}

// HOMEINFOBOX
$DatabaseTables['home info box'] = DB_TABLE_PREFIX.'homeinfobox';
$DatabaseTablesFields['home info box'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int|8','autoinc' => true,'primary' => true],
    'created'                                   		=> [
        'label'                                 		=> Config::$langVars['creazione'],
        'searchTable'                           		=> false,
        'required'                              		=> false,
        'type'                                  		=> 'datatime',
        'defValue'                              		=> Config::$nowDateTimeIso,
        'forcedValue'                           		=> Config::$nowDateTimeIso,
    ],
    'active'                                   			=> [
        'label'                                 		=> Config::$langVars['attiva'],
        'required'                              		=> false,
        'type'                                  		=> 'int|1',
        'defValue'                              		=> 1,
        'forcedValue'                           		=> 1,
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);

    $DatabaseTablesFields['home info box']['title_'.$lang] = [
        'label'											=> 'Titolo '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
        'error message'									=> preg_replace('/%ITEM%/', (string) Config::$langVars['titolo'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
    ];
    $DatabaseTablesFields['home info box']['content_'.$lang] = [
        'label'											=> Config::$langVars['descrizione'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'mediumtext',
        'defValue'										=> '',
    ];

    $DatabaseTablesFields['home info box']['url_'.$lang] = [
        'label'											=> Config::$langVars['url'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ];
    $DatabaseTablesFields['home info box']['target_'.$lang] = [
        'label'											=> Config::$langVars['target'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|20',
        'defValue'										=> '',
    ];
}

// TEAM
$DatabaseTables['team'] = DB_TABLE_PREFIX.'team';
$DatabaseTablesFields['team'] = [
    'id'												=> [
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true,
    ],
    'name' 												=>  [
        'label'											=> Config::$langVars['nome'],
        'searchTable'									=> true,
        'required'										=> true,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
        'error message'									=> preg_replace('/%ITEM%/', (string) Config::$langVars['nome'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
    ],
    'email' 											=>  [
        'label'											=> Config::$langVars['indirizzo email'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
        'validate'										=> 'isemail',
        'error message'									=> preg_replace('/%ITEM%/', (string) Config::$langVars['indirizzo email'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
        'error validate message'						=> Config::$langVars['Devi inserire un indirizzo email valido!'],
    ],
    'url'											 	=> [
        'label'											=> Config::$langVars['url'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'target'											=> [
        'label'											=> Config::$langVars['target'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|20',
        'defValue'										=> '',
    ],
    'filename'											=> [
        'label'											=> 'Nome File',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_filename'										=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
    'ordering'											=> [
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 1,
        'forcedValue'                           		=> 0,
    ],
    'created'                                   		=> [
        'label'                                 		=> Config::$langVars['creazione'],
        'searchTable'                           		=> false,
        'required'                              		=> false,
        'type'                                  		=> 'datatime',
        'defValue'                              		=> Config::$nowDateTimeIso,
        'forcedValue'                           		=> Config::$nowDateTimeIso,
    ],
    'active'                                   			=> [
        'label'                                 		=> Config::$langVars['attiva'],
        'required'                              		=> false,
        'type'                                  		=> 'int|1',
        'defValue'                              		=> 1,
        'forcedValue'                           		=> 1,
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);

    $DatabaseTablesFields['team']['role_'.$lang] = [
        'label'											=> Config::$langVars['ruolo'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|100',
        'defValue'										=> '',
        'error message'									=> preg_replace('/%ITEM%/', (string) Config::$langVars['ruolo'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
    ];

    $DatabaseTablesFields['team']['universita_'.$lang] = [
        'label'											=> 'Università '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|100',
        'defValue'										=> '',
        'error message'									=> preg_replace('/%ITEM%/', 'università', (string) Config::$langVars['Devi inserire una %ITEM%!']),
    ];

    $DatabaseTablesFields['team']['summary_'.$lang] = [
        'label'											=> Config::$langVars['descrizione'].' lista '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> '',
    ];

    $DatabaseTablesFields['team']['content_'.$lang] = [
        'label'											=> Config::$langVars['descrizione'].' dettaglio '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'mediumtext',
        'defValue'										=> '',
    ];
}

$DatabaseTables['team config'] = DB_TABLE_PREFIX.'team_config';
$DatabaseTablesFields['team config'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int','primary' => true],
    'image_header'											=> [
        'label'											=> Config::$langVars['immagine'].' header',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_image_header'									=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $DatabaseTablesFields['team config']['title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];

    $DatabaseTablesFields['team config']['text_intro_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];
    $DatabaseTablesFields['team config']['page_content_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['team config']['meta_title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['team config']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|300'];
    $DatabaseTablesFields['team config']['meta_keywords_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
}

// NEWS
$DatabaseTables['news config'] = DB_TABLE_PREFIX.'news_config';
$DatabaseTablesFields['news config'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int','primary' => true],
    'image_header'											=> [
        'label'											=> Config::$langVars['immagine'].' header',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_image_header'									=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $DatabaseTablesFields['news config']['title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];

    $DatabaseTablesFields['news config']['text_intro_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];
    $DatabaseTablesFields['news config']['page_content_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['news config']['meta_title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['news config']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|300'];
    $DatabaseTablesFields['news config']['meta_keywords_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
}

// FAQ
$DatabaseTables['faq config'] = DB_TABLE_PREFIX.'faq_config';
$DatabaseTablesFields['faq config'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int','primary' => true],
    'image_header'											=> [
        'label'											=> Config::$langVars['immagine'].' header',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_image_header'									=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $DatabaseTablesFields['faq config']['title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];

    $DatabaseTablesFields['faq config']['text_intro_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];
    $DatabaseTablesFields['faq config']['page_content_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['faq config']['meta_title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['faq config']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|300'];
    $DatabaseTablesFields['faq config']['meta_keywords_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
}

// CONTACTS
$DatabaseTables['contacts config'] = DB_TABLE_PREFIX.'contacts_config';
$DatabaseTablesFields['contacts config'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int','primary' => true],
    'email_address' => ['label' => 'Indirizzo email','searchTable' => false,'required' => true,'type' => 'varchar|255','defValue' => ''],
    'label_email_address' => ['Etichetta email' => 'Totale','required' => true,'type' => 'float(10,2)','defValue' => ''],
    'send_email_debug'					=> [
        'label'							=> 'Invia email per debug',
        'searchTable'					=> false,
        'required'						=> false,
        'type'							=> 'int|1',
        'validate'						=> 'int',
        'defValue'						=> '0',
        'forcedValue'                   => 0,
    ],
    'email_debug' => ['label' => 'Email per debug','searchTable' => false,'required' => true,'type' => 'varchar|255'],
    'admin_email_subject' => ['label' => 'soggetto email admin','searchTable' => false,'required' => true,'type' => 'varchar|255'],
    'admin_email_content' => ['label' => 'contenuto email admin','searchTable' => false,'required' => true,'type' => 'mediumtext'],
    'map_latitude' => ['label' => 'latitudine','searchTable' => false,'required' => false,'type' => 'varchar|20'],
    'map_longitude' => ['label' => 'longitudine','searchTable' => false,'required' => false,'type' => 'varchar|20'],
    'url_privacy_page' => ['label' => 'url privacy page','searchTable' => false,'required' => false,'type' => 'varchar|255'],
    'image_header'											=> [
        'label'											=> Config::$langVars['immagine'].' header',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_image_header'									=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
];

foreach (Config::$globalSettings['languages'] as $lang) {
    $DatabaseTablesFields['contacts config']['user_email_subject_'.$lang] = ['label' => 'soggetto email utente'.' '.$lang,'searchTable' => false,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['contacts config']['user_email_content_'.$lang] = ['label' => 'contenuto email utente'.' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['contacts config']['title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];

    $DatabaseTablesFields['contacts config']['text_intro_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];
    $DatabaseTablesFields['contacts config']['page_content_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['contacts config']['meta_title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['contacts config']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|300'];
    $DatabaseTablesFields['contacts config']['meta_keywords_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
}

// NEWSLETTER
$DatabaseTables['newsletter config'] = DB_TABLE_PREFIX.'newsletter_config';
$DatabaseTablesFields['newsletter config'] = [
    'id' => ['label' => 'ID','required' => false,'type' => 'int','primary' => true],
    'image_header'											=> [
        'label'											=> Config::$langVars['immagine'].' header',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> '',
    ],
    'org_image_header'									=> [
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255',
        'defValue'										=> '',
    ],
];
foreach (Config::$globalSettings['languages'] as $lang) {
    $DatabaseTablesFields['newsletter config']['title_'.$lang] = ['label' => ucfirst((string) Config::$langVars['titolo']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];

    $DatabaseTablesFields['newsletter config']['text_intro_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'text'];
    $DatabaseTablesFields['newsletter config']['page_content_'.$lang] = ['label' => ucfirst((string) Config::$langVars['contenuto']).' '.$lang,'searchTable' => false,'required' => false,'type' => 'mediumtext'];

    $DatabaseTablesFields['newsletter config']['meta_title_'.$lang] = ['label' => 'Titolo '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
    $DatabaseTablesFields['newsletter config']['meta_description_'.$lang] = ['label' => 'Descrizione META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|300'];
    $DatabaseTablesFields['newsletter config']['meta_keywords_'.$lang] = ['label' => 'Keyword META '.$lang,'searchTable' => true,'required' => false,'type' => 'varchar|255'];
}

/*
// whises
$DatabaseTables['wishes']  = self::$dbTablePrefix . 'wishes';
$DatabaseTables['whises products']  = self::$dbTablePrefix . 'wishes_products';

// wareouse products
$DatabaseTables['warehouse products']  = self::$dbTablePrefix . 'warehouse_products';
$DatabaseTablesFields['warehouse products'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'users_id'											=> array(
        'label'											=> Config::$langVars['proprietario'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 0
    ),
    'categories_id'										=> array(
        'label'											=> 'ID Cat',
        'required'										=> false,
        'type'											=> 'int|8'
    ),
    'alias'												=> array(
        'label'											=> Config::$langVars['alias'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'price_unity'										=> array(
        'label'											=> Config::$langVars['prezzo unitario'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'float|10,2',
        'defValue'										=> '0.00',
        'validate'										=> 'float',
        'errorValidateMessage'							=> 'error validate message custom'
    ),
    'price_sconto'										=> array(
        'label'											=> Config::$langVars['sconto'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'float|10,2',
        'defValue'										=> '0.00',
        'validate'										=> 'float',
        'errorValidateMessage'							=> 'error validate message custom'
    ),
    'tax'												=> array(
        'label'											=> Config::$langVars['iva'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'float|10,2',
        'defValue'										=> '0.00',
        'validate'										=>'float'
    ),
    'ordering'											=> array(
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 1
    ),
    'filename'											=> array(
        'label'											=> Config::$langVars['immagine'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'org_filename'										=> array(
        'label'											=> Config::$langVars['nome file originale'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'id_tags'											=> array(
        'label'											=> 'Id '.Config::$langVars['tags'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    ),
    'is_new'											=> array(
        'label'											=> Config::$langVars['nuovo'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0'
    ),
    'is_promo'											=> array(
        'label'											=> Config::$langVars['promozione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=>	'int',
        'defValue'										=> '0'
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['warehouse products']['meta_title_'.$lang] = array(
        'label'											=> 'titolo'.' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'										=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse products']['meta_description_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,'required'=>false,
        'type'											=> 'varchar|300',
        'validate'										=> 'maxchar',
        'valuerif'										=> 300,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse products']['meta_keyword_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse products']['title_seo_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=>'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse products']['title_'.$lang] = array(
        'label'											=> Config::$langVars['titolo'].' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse products']['content_'.$lang] = array(
        'label'											=> Config::$langVars['descrizione'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'mediumtext',
        'defValue'										=> ''
    );
    $DatabaseTablesFields['warehouse products']['summary_'.$lang] = array(
        'label'											=> Config::$langVars['sommario'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    );
}

// warehouse categories
$DatabaseTables['warehouse categories']  = self::$dbTablePrefix . 'warehouse_categories';
$DatabaseTablesFields['warehouse categories'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'parent'											=> array(
        'label'											=> 'Parent',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 0
    ),
    'users_id'											=> array(
        'label'											=> Config::$langVars['proprietario'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 0
    ),
    'alias'												=> array(
        'label'											=> Config::$langVars['alias'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']),'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    ),
    'ordering'											=> array(
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 1
    ),
    'filename'											=> array(
        'label'											=> Config::$langVars['immagine'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'org_filename'										=> array(
        'label'											=> Config::$langVars['nome file originale'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=>'varchar|255'
    ),
    'id_tags'											=> array(
        'label'											=> 'Id '.Config::$langVars['tags'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['warehouse categories']['meta_title_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'										=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse categories']['meta_description_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,'required'=>false,
        'type'											=> 'varchar|300',
        'validate'										=> 'maxchar',
        'valuerif'										=> 300,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse categories']['meta_keyword_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse categories']['title_seo_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['warehouse categories']['title_'.$lang] = array(
        'label'											=> Config::$langVars['titolo'].' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
}

// warehouse tags
$DatabaseTables['warehouse tags']  = self::$dbTablePrefix . 'warehouse_tags';
$DatabaseTablesFields['warehouse tags'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'ordering'											=> array(
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 1
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['warehouse tags']['title_'.$lang] 	= array(
        'label'											=> Config::$langVars['titolo'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
}

// contacts
$DatabaseTables['contacts'] = self::$dbTablePrefix . 'contacts';
$DatabaseTablesFields['contacts'] = array(
    'id'																=> array(
        'label'															=> 'ID',
        'required'														=> false,
        'type'															=> 'autoinc',
        'primary'														=>	true
    ),
    'name'																=> array(
        'label'															=> Config::$langVars['nome'],
        'searchTable'													=> true,
        'required'														=> true,
        'type'															=> 'varchar|255',
        'defValue'														=> Config::$nowDateTimeIso,
    ),
    'email'																=> array(
        'label'															=> Config::$langVars['email'],
        'searchTable'													=> true,
        'required'														=> true,
        'type'															=> 'varchar|255',
        'defValue'														=> '',
        'validate'														=> 'isemail',
        'error message'             									=> preg_replace('/%ITEM%/',Config::$langVars['indirizzo email valido'],Config::$langVars['Devi inserire un %ITEM%!'])
    ),
    'telephone'															=> array(
        'label'															=> Config::$langVars['telefono'],
        'searchTable'													=> true,
        'required'														=> true,
        'type'															=> 'varchar|2',
        'defValue'														=> '',
        'validate'														=> 'telephonenumber',
        'error message'             									=> preg_replace('/%ITEM%/',Config::$langVars['numero di telefono'],Config::$langVars['Devi inserire un %ITEM%!']),
        'validation error message'     									=> preg_replace('/%ITEM%/',Config::$langVars['numero di telefono valido'],Config::$langVars['Devi inserire un %ITEM%!'])
    ),
    'object'															=> array(
        'label'															=> Config::$langVars['oggetto'],
        'searchTable'													=> true,
        'required'														=> true,
        'type'															=> 'varchar|255',
        'defValue'														=> '',
    ),
    'message'															=> array(
        'label'															=> Config::$langVars['messaggio'],
        'searchTable'													=> true,
        'required'														=> true,
        'type'															=> 'text',
        'defValue'														=> '',
    ),
    'ip_address'														=> array(
        'label'															=> Config::$langVars['indirizzo ip'],
        'searchTable'													=> true,
        'required'														=> false,
        'type'															=> 'varchar|50',
        'defValue'														=> '',
    ),
    'is_span'															=> array(
        'label'															=> Config::$langVars['è span'],
        'searchTable'													=> true,
        'required'														=> false,
        'type'															=> 'int|1',
        'defValue'														=> '0',
    ),
    'created'															=> array(
        'label'															=> Config::$langVars['creazione'],
        'searchTable'													=> false,
        'required'														=> false,
        'type'															=> 'datatime',
        'defValue'														=> Config::$nowDateTimeIso,
        'validate'														=> 'datetimeiso'
    )
);

// news o blog
$DatabaseTables['news'] = self::$dbTablePrefix . 'news';
$DatabaseTablesFields['news'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'id_user'											=> array(
        'label'											=> Config::$langVars['proprietario'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> ''
    ),
    'id_cat'											=> array(
        'label'											=> 'ID Cat',
        'required'										=> false,
        'type' 											=>'int|8',
        'defValue'										=> '0'
    ),
    'datatimeins'										=> array (
        'label'											=> Config::$langVars['data'],
        'searchTable'									=> false,
        'required'										=> true,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimepicker',
        'error message'									=> Config::$langVars['Devi inserire una data valida!']
    ),
    'datatimescaini'									=> array(
        'label'											=> Config::$langVars['inizio scadenza'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimepicker',
        'error message'									=> Config::$langVars['Devi inserire una data valida!']
    ),
    'datatimescaend'									=> array(
        'label'											=> Config::$langVars['fine scadenza'],
        'searchTable'									=> false,
        'required'										=> false,
        'type' 											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimepicker',
        'errorMessage'									=> Config::$langVars['Devi inserire una data valida!'],
        'errorValidateMessage'  						=> Config::$langVars['Devi inserire una data valida!']
    ),
    'filename'											=> array(
        'label'											=> 'Nome File',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=>'varchar|255'
    ),
    'org_filename'										=> array(
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'embedded'											=> array(
        'label'											=> 'Embedded',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    ),
    'scadenza'											=> array(
        'label'											=> 'Scadenza',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'defValue'										=> '0'
    ),
    'access_type'										=> array(
        'label'											=> Config::$langVars['tipo accesso'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'defValue'										=> '0'
    ),
    'access_read'										=> array(
        'label'											=> Config::$langVars['accesso lettura'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> 'none'
    ),
    'access_write'										=> array(
        'label'											=> Config::$langVars['accesso scrittura'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> 'none'
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['news']['meta_title_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'										=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['news']['meta_description_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','300',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,'required'=>false,
        'type'											=> 'varchar|300',
        'validate'										=> 'maxchar',
        'valuerif'										=> 300,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['descrizione']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'300'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['news']['meta_keyword_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['keyword']).' '.strtoupper(Config::$langVars['meta']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['news']['title_seo_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.strtoupper(Config::$langVars['seo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['news']['title_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['titolo']).' '.$lang,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['news']['summary_'.$lang] = array(
        'label'											=> ucfirst(Config::$langVars['sommario']).' '.$lang,
        'searchTable'									=> true,
        'labelsubtext'									=> preg_replace('/%NUMBER%/','255',Config::$langVars['massimo %NUMBER% caratteri']),
        'required'										=> false,
        'type'											=> 'text',
        'validate'										=> 'maxchar',
        'valuerif'  									=> 255,
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['sommario']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])

    );
    $DatabaseTablesFields['news']['content_'.$lang] = array(
        'label' 										=> 'Contenuto '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'mediumtext'
    );
}

// footer categorie
$DatabaseTables['footer categories'] = self::$dbTablePrefix . 'footer_categories';
$DatabaseTablesFields['footer categories'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'url'												=> array(
        'label'											=> Config::$langVars['url'],
        'searchTable'									=> true,
        'required'										=> true,
        'type'											=> 'varchar|255'
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['footer categories']['title_'.$lang]  = array(
        'label'											=> Config::$langVars['titolo'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
}

// brands
$DatabaseTables['brands'] = self::$dbTablePrefix . 'brands';
$DatabaseTablesFields['brands'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'int|8',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'url'												=> array(
        'label'											=> Config::$langVars['url'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'in_footer'											=> array(
        'label'											=> 'in footer',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int|1',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['brands']['title_'.$lang] = array(
        'label'											=> Config::$langVars['titolo'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
}

// slideshome rev
$DatabaseTables['slides home rev'] = self::$dbTablePrefix . 'slides_home_rev';
$DatabaseTablesFields['slides home rev'] = array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'autoinc',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'user_id'											=> array(
        'label'											=> Config::$langVars['proprietario'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|8',
        'defValue'										=> 0
    ),
    'title' 											=> array(
        'label'											=> ucfirst(Config::$langVars['titolo']),
        'searchTable'									=> true,
        'required'										=> true,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    ),
    'filename'											=> array(
        'label'											=> 'Nome File',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'org_filename'										=> array(
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'li_data'											=> array (
        'label'											=> 'LI Data',
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    ),
    'ordering'											=> array(
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int8',
        'defValue'										=> 1
    ),
    'slide_type'										=> array(
        'label'											=> 'Tipo',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'defValue'										=>'0'
    ),
    'access_type'										=> array(
        'label'											=> 'Tipo accesso',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'defValue'										=> '0'
    ),
    'access_read'										=> array(
        'label'											=> Config::$langVars['accesso lettura'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> 'none'
    ),
    'access_write'										=> array(
        'label'											=> Config::$langVars['accesso scrittura'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> 'none'
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
        )
    );

// slideshome rev layers
$DatabaseTables['slides home rev layers'] = self::$dbTablePrefix . 'slides_home_rev_layers';
$DatabaseTablesFields['slides home rev layers'] 		= array(
    'id'												=> array(
        'label'											=> 'ID',
        'required'										=> false,
        'type'											=> 'autoinc',
        'autoinc'										=> true,
        'primary'										=> true
    ),
    'slide_id'											=> array(
        'label'											=> '',
        'searchTable'									=> false,
        'required'										=> true,
        'type'											=> 'int|8',
        'defValue'										=> 0
    ),
    'title' 											=> array(
        'label'											=> Config::$langVars['titolo'],
        'searchTable'									=> true,
        'required'										=> true,
        'type'											=> 'varchar|255',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    ),
    'filename'											=> array(
        'label'											=> 'Nome File',
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'varchar|255'
    ),
    'org_filename'										=> array(
        'label'											=> '',
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar255'
    ),
    'ordering'											=> array(
        'label'											=> Config::$langVars['ordinamento'],
        'required'										=> false,
        'type'											=> 'int8',
        'defValue'										=>1
    ),
    'url'												=> array(
        'label'											=> Config::$langVars['url'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|255',
        'defValue'										=> ''
    ),
    'target'											=> array(
        'label'											=> Config::$langVars['target'],
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'varchar|20',
        'defValue'										=> ''
    ),
    'type'												=> array(
        'label'											=> Config::$langVars['tipo'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'int|1',
        'defValue'										=> '0'
    ),
    'created'											=> array(
        'label'											=> Config::$langVars['creazione'],
        'searchTable'									=> false,
        'required'										=> false,
        'type'											=> 'datatime',
        'defValue'										=> Config::$nowDateTimeIso,
        'validate'										=> 'datetimeiso',
        'forcedValue'                   				=> self::$nowDateTime
    ),
    'active'											=> array(
        'label'											=> Config::$langVars['attiva'],
        'required'										=> false,
        'type'											=> 'int|1',
        'validate'										=> 'int',
        'defValue'										=> '0',
        'forcedValue'      								=> 1
    )
);
foreach(Config::$globalSettings['languages'] AS $lang) {
    $required = ($lang == Config::$langVars['user'] ? true : false);
    $DatabaseTablesFields['slides home rev layers']['content_'.$lang] = array(
        'label'											=> Config::$langVars['contenuto'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> $required,
        'type'											=> 'text',
        'error message'									=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$langVars['titolo']).' '.$lang,'255'),Config::$langVars['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
    );
    $DatabaseTablesFields['slides home rev layers']['template_'.$lang] = array(
        'label'											=> Config::$langVars['template'].' '.$lang,
        'searchTable'									=> true,
        'required'										=> false,
        'type'											=> 'text',
        'defValue'										=> ''
    );
}

*/
