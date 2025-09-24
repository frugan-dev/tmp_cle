<?php
/* wscms/home/extra.php v.4.0.0. 06/08/2019 */

// Sponsor
if (in_array(DB_TABLE_PREFIX.'sponsor',$tablesDb) && file_exists(PATH.$App->pathApplications."sponsor/index.php") && Permissions::checkIfModulesIsReadable('sponsor',$App->userLoggedData,$App->user_module_active) == true) {
	$App->homeBlocks['sponsor'] 				= array(
		'table'									=> DB_TABLE_PREFIX.'sponsor',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst($_lang['sponsor']),
		'sex suffix'							=> ucfirst($_lang['nuovi']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=> array (
			'string'							=> URL_SITE_ADMIN.'sponsor',
			'opt'								=> array()
		)
	);			
	$App->homeTables['sponsor'] 				= array(
		'table'									=> DB_TABLE_PREFIX.'sponsor',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst($_lang['ultimi']).' '.$_lang['sponsor'],
		'fields'								=> array(

			'title'								=> array(
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst($_lang['titolo']),
				'url'							=> true,
				'url item'						=> array(
					'string'					=> URL_SITE_ADMIN.'sponsor',
					'opt'						=> array(
						'fieldItemRif'			=> ''
					)
				)
			),

			'filename'							=> array(
				'multilanguage'					=> 0,
				'type'							=> 'varchar',
				'label'							=> ucfirst($_lang['immagine']),
				'type'							=> 'image',
				'pathdef'						=> UPLOAD_DIR.'sponsor/',
				'path'							=> UPLOAD_DIR.'sponsor/'
			)
		)
	);	
}

// Partner
if (in_array(DB_TABLE_PREFIX.'partners',$tablesDb) && file_exists(PATH.$App->pathApplications."partners/index.php") && Permissions::checkIfModulesIsReadable('partners',$App->userLoggedData,$App->user_module_active) == true) {
	//Core::setDebugMode(1);	
	$App->homeBlocks['partners'] 				= array(
		'table'									=> DB_TABLE_PREFIX.'partners',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst($_lang['partners']),
		'sex suffix'							=> ucfirst($_lang['nuovi']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=> array (
			'string'							=> URL_SITE_ADMIN.'partners',
			'opt'								=> array()
		)
	);			
	$App->homeTables['partners'] 				= array(
		'table'									=> DB_TABLE_PREFIX.'partners',
		'icon panel'							=> 'fa-users',
		'label'									=> ucfirst($_lang['ultimi']).' '.$_lang['partners'],
		'fields'								=> array(

			'title'								=> array(
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst($_lang['titolo']),
				'url'							=> true,
				'url item'						=> array(
					'string'					=> URL_SITE_ADMIN.'partners',
					'opt'						=> array(
						'fieldItemRif'			=> ''
					)
				)
			),

			'filename'							=> array(
				'multilanguage'					=> 0,
				'type'							=> 'varchar',
				'label'							=> ucfirst($_lang['immagine']),
				'type'							=> 'image',
				'pathdef'						=> UPLOAD_DIR.'partners/',
				'path'							=> UPLOAD_DIR.'partners/'
			)
		)
	);	
}

// FAQ
if (in_array(DB_TABLE_PREFIX.'faq',$tablesDb) && file_exists(PATH.$App->pathApplications."faq/index.php") && Permissions::checkIfModulesIsReadable('faq',$App->userLoggedData,$App->user_module_active) == true) {
	//Core::setDebugMode(1);	
	$App->homeBlocks['faq'] 					= array(
		'table'									=> DB_TABLE_PREFIX.'faq',
		'icon panel'							=> 'fa-question',
		'label'									=> ucfirst($_lang['faq']),
		'sex suffix'							=> ucfirst($_lang['nuove']),
		'type'									=> 'info',
		'url'									=> true,
		'url item'								=> array (
			'string'							=> URL_SITE_ADMIN.'faq',
			'opt'								=> array()
		)
	);			
	$App->homeTables['faq'] 					= array(
		'table'									=> DB_TABLE_PREFIX.'faq',
		'icon panel'							=> 'fa-question',
		'label'									=> ucfirst($_lang['ultime']).' '.$_lang['faq'],
		'fields'								=> array(
			'title'								=> array(
				'multilanguage'					=> 1,
				'type'							=> 'varchar',
				'label'							=> ucfirst($_lang['titolo']),
				'url'							=> true,
				'url item'						=> array(
					'string'					=> URL_SITE_ADMIN.'faq',
					'opt'						=> array(
						'fieldItemRif'			=> ''
					)
				)
			)
		)
	);	
}
?>