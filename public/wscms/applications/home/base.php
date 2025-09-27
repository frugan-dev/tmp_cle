<?php

/* wscms/home/base.php v.3.5.4. 06/08/2019 */

/* users */
if (in_array(DB_TABLE_PREFIX.'users', $tablesDb) && file_exists(PATH.$App->pathApplications.'users/index.php') && Permissions::checkIfModulesIsReadable('users', $App->userLoggedData, $App->user_module_active) == true) {
    /*
    $App->homeBlocks['users'] 									= array(
        'table'													=> DB_TABLE_PREFIX.'users',
        'query opt'												=> array(
        'clause' 												=> "is_root = 0",
        'clauseValRif'											=> array()
        ),
        'icon panel'											=> 'fa-users',
        'label'													=> ucfirst($_lang['utenti']),
        'sex suffix'											=> ucfirst($_lang['nuovi']),
        'type'													=> 'info',
        'url'													=> true,
        'url item'												=> array (
            'string'											=> URL_SITE_ADMIN.'users',
            'opt'												=> array()
        )
    );
    */

    $App->homeTables['users'] = [
        'table'													=> DB_TABLE_PREFIX.'users',
        'query opt'												=> [
            'clause'											=> 'is_root = 0',
            'clauseValRif'										=> [],
        ],
        'icon panel'											=> 'fa-users',
        'label'													=> ucfirst((string) $_lang['ultimi']).' '.$_lang['utenti'],
        'fields'												=> [
            'name'												=> [
                'multilanguage'									=> 0,
                'type'											=> 'varchar',
                'label'											=> ucfirst((string) $_lang['nome']),
                'url'											=> true,
                'url item'										=> [
                    'string'									=> URL_SITE_ADMIN.'users',
                    'opt'										=> [
                        ],
                    ],
                ],
            'avatar'											=> [
                'multilanguage'									=> 0,
                'type'											=> 'avatar',
                'label'											=> ucfirst((string) $_lang['avatar']),
                'url'											=> false,
                ],

            ],
        ];

}

/* pages */
if (in_array(DB_TABLE_PREFIX.'pages', $tablesDb) && file_exists(PATH.$App->pathApplications.'pages/index.php') && Permissions::checkIfModulesIsReadable('pages', $App->userLoggedData, $App->user_module_active) == true) {
    $App->homeBlocks['pages'] = [
        'table' => DB_TABLE_PREFIX.'pages',
        'icon panel' => 'fa-pager',
        'label' => ucfirst((string) $_lang['pagine']),
        'sex suffix' => ucfirst((string) $_lang['nuove']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'pages',
            'opt' => [],
            ],
        ];
    $App->homeTables['site-pages'] = [
        'table' => DB_TABLE_PREFIX.'pages',
        'icon panel' => 'fa-pager',
        'label' => ucfirst((string) $_lang['ultime']).' '.$_lang['pagine'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'pages/modifyItem',
                    'opt' => [
                        'fieldItemRif' => 'id',
                        ],
                    ],
                ],
            ],
        ];
}

/* pages blocks*/
if (in_array(DB_TABLE_PREFIX.'pages_blocks', $tablesDb) && file_exists(PATH.$App->pathApplications.'pages/index.php') && Permissions::checkIfModulesIsReadable('pages', $App->userLoggedData, $App->user_module_active) == true) {
    $App->homeBlocks['pages-blocks'] = [
        'table' => DB_TABLE_PREFIX.'pages_blocks',
        'icon panel' => 'fa-tasks',
        'label' => ucfirst((string) $_lang['blocchi contenuto']),
        'sex suffix' => ucfirst((string) $_lang['nuovi']),
        'type' => 'info',
        'url' => true,
        'url item' => [
            'string' => URL_SITE_ADMIN.'pages',
            'opt' => [
                'fieldItemRif' => 'id_owner',
            ],
        ],
    ];
    $App->homeTables['pages-blocks'] = [
        'table' => DB_TABLE_PREFIX.'pages_blocks',
        'icon panel' => 'fa-tasks',
        'label' => ucfirst((string) $_lang['ultimi']).' '.$_lang['blocchi contenuto pagine'],
        'fields' => [
            'title' => [
                'multilanguage' => 1,
                'type' => 'varchar',
                'label' => ucfirst((string) $_lang['titolo']),
                'url' => true,
                'url item' => [
                    'string' => URL_SITE_ADMIN.'pages/modifyIblo',
                    'opt' => [
                        'fieldItemRif' => 'id',
                    ],
                ],
            ],
        ],
    ];
}
