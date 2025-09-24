<?php
/* wscms/home/base.php v.3.5.4. 06/08/2019 */

/* users */
if (in_array(DB_TABLE_PREFIX.'users',$tablesDb) && file_exists(PATH.$App->pathApplications."users/index.php") && Permissions::checkIfModulesIsReadable('users',$App->userLoggedData,$App->user_module_active) == true) {
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
				
	$App->homeTables['users'] = array(
		'table'													=> DB_TABLE_PREFIX.'users',
		'query opt'												=> array(
			'clause'											=> 'is_root = 0',
			'clauseValRif'										=> array()
		),
		'icon panel'											=> 'fa-users',
		'label'													=> ucfirst($_lang['ultimi']).' '.$_lang['utenti'],
		'fields'												=> array(
			'name'												=> array(
				'multilanguage'									=> 0,
				'type'											=> 'varchar',
				'label'											=> ucfirst($_lang['nome']),
				'url'											=> true,
				'url item'										=> array(
					'string'									=> URL_SITE_ADMIN.'users',
					'opt'										=> array(
						)
					)
				),
			'avatar'											=> array(
				'multilanguage'									=> 0,
				'type'											=> 'avatar',
				'label'											=> ucfirst($_lang['avatar']),
				'url'											=> false,
				)
			
			)
		);

	}

/* pages */
if (in_array(DB_TABLE_PREFIX.'pages',$tablesDb) && file_exists(PATH.$App->pathApplications."pages/index.php") && Permissions::checkIfModulesIsReadable('pages',$App->userLoggedData,$App->user_module_active) == true) {
	$App->homeBlocks['pages'] = array(
		'table'=>DB_TABLE_PREFIX.'pages',
		'icon panel'=>'fa-pager',
		'label'=>ucfirst($_lang['pagine']),
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'pages',
			'opt'=>array()
			)
		);			
	$App->homeTables['site-pages'] = array(
		'table'=>DB_TABLE_PREFIX.'pages',
		'icon panel'=>'fa-pager',
		'label'=>ucfirst($_lang['ultime']).' '.$_lang['pagine'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'pages/modifyItem',
					'opt'=>array(
						'fieldItemRif'=>'id'
						)
					)
				)
			)
		);
	}
	

	/* pages blocks*/
if (in_array(DB_TABLE_PREFIX.'pages_blocks',$tablesDb) && file_exists(PATH.$App->pathApplications."pages/index.php") && Permissions::checkIfModulesIsReadable('pages',$App->userLoggedData,$App->user_module_active) == true) {
	$App->homeBlocks['pages-blocks'] = array(
		'table'=>DB_TABLE_PREFIX.'pages_blocks',
		'icon panel'=>'fa-tasks',
		'label'=>ucfirst($_lang['blocchi contenuto']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'pages',
			'opt'=>array(
				'fieldItemRif'=>'id_owner'
			)
		)
	);			
	$App->homeTables['pages-blocks'] = array(
		'table'=>DB_TABLE_PREFIX.'pages_blocks',
		'icon panel'=>'fa-tasks',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['blocchi contenuto pagine'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'pages/modifyIblo',
					'opt'=>array(
						'fieldItemRif'=>'id'
					)
				)
			)
		)
	);
}
?>