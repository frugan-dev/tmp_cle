<?php
/* wscms/home/extra.php v.4.0.0. 06/08/2019 */

// Sponsor
if (in_array(DB_TABLE_PREFIX.'sponsor',$tablesDb) && file_exists(PATH.$App->pathApplications."sponsor/index.php") && Permissions::checkIfModulesIsReadable('sponsor',$App->userLoggedData,$App->user_module_active) == true) {
	$App->homeBlocks['sponsor'] 				= [
		'table'									=> DB_TABLE_PREFIX.'sponsor',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst((string) $_lang['sponsor']),
		'sex suffix'							=> ucfirst((string) $_lang['nuovi']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=>  [
			'string'							=> URL_SITE_ADMIN.'sponsor',
			'opt'								=> []
		]
	];			
	$App->homeTables['sponsor'] 				= [
		'table'									=> DB_TABLE_PREFIX.'sponsor',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst((string) $_lang['ultimi']).' '.$_lang['sponsor'],
		'fields'								=> [

			'title'								=> [
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst((string) $_lang['titolo']),
				'url'							=> true,
				'url item'						=> [
					'string'					=> URL_SITE_ADMIN.'sponsor',
					'opt'						=> [
						'fieldItemRif'			=> ''
					]
				]
			],

			'filename'							=> [
				'multilanguage'					=> 0,
				'type'							=> 'varchar',
				'label'							=> ucfirst((string) $_lang['immagine']),
				'type'							=> 'image',
				'pathdef'						=> UPLOAD_DIR.'sponsor/',
				'path'							=> UPLOAD_DIR.'sponsor/'
			]
		]
	];	
}

// Partner
if (in_array(DB_TABLE_PREFIX.'partners',$tablesDb) && file_exists(PATH.$App->pathApplications."partners/index.php") && Permissions::checkIfModulesIsReadable('partners',$App->userLoggedData,$App->user_module_active) == true) {
	//Core::setDebugMode(1);	
	$App->homeBlocks['partners'] 				= [
		'table'									=> DB_TABLE_PREFIX.'partners',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst((string) $_lang['partners']),
		'sex suffix'							=> ucfirst((string) $_lang['nuovi']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=>  [
			'string'							=> URL_SITE_ADMIN.'partners',
			'opt'								=> []
		]
	];			
	$App->homeTables['partners'] 				= [
		'table'									=> DB_TABLE_PREFIX.'partners',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst((string) $_lang['ultimi']).' '.$_lang['partners'],
		'fields'								=> [

			'title'								=> [
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst((string) $_lang['titolo']),
				'url'							=> true,
				'url item'						=> [
					'string'					=> URL_SITE_ADMIN.'partners',
					'opt'						=> [
						'fieldItemRif'			=> ''
					]
				]
			],

			'filename'							=> [
				'multilanguage'					=> 0,
				'type'							=> 'varchar',
				'label'							=> ucfirst((string) $_lang['immagine']),
				'type'							=> 'image',
				'pathdef'						=> UPLOAD_DIR.'partners/',
				'path'							=> UPLOAD_DIR.'partners/'
			]
		]
	];	
}

// FAQ
if (in_array(DB_TABLE_PREFIX.'faq',$tablesDb) && file_exists(PATH.$App->pathApplications."faq/index.php") && Permissions::checkIfModulesIsReadable('faq',$App->userLoggedData,$App->user_module_active) == true) {
	//Core::setDebugMode(1);	
	$App->homeBlocks['faq'] 					= [
		'table'									=> DB_TABLE_PREFIX.'faq',
		'icon panel'							=> 'fa-question',
		'label'									=> ucfirst((string) $_lang['faq']),
		'sex suffix'							=> ucfirst((string) $_lang['nuove']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=>  [
			'string'							=> URL_SITE_ADMIN.'faq',
			'opt'								=> []
		]
	];			
	$App->homeTables['faq'] 					= [
		'table'									=> DB_TABLE_PREFIX.'faq',
		'icon panel'							=> 'fa-question',
		'label'									=> ucfirst((string) $_lang['ultime']).' '.$_lang['faq'],
		'fields'								=> [
			'title'								=> [
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst((string) $_lang['titolo']),
				'url'							=> true,
				'url item'						=> [
					'string'					=> URL_SITE_ADMIN.'faq',
					'opt'						=> [
						'fieldItemRif'			=> ''
					]
				]
			]
		]
	];	
}
?>