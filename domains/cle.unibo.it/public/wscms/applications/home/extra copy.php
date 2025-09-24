<?php
/* wscms/home/extra.php v.3.5.4. 06/08/2019 */

/* SLIDE HOME REV */
if (in_array(DB_TABLE_PREFIX.'slides_home_rev',$tablesDb) && file_exists(PATH.$App->pathApplications."slides-home-rev/index.php") && Permissions::checkAccessUserModule('slides-home-rev',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['slides-home-rev'] = array(
		'table'=>DB_TABLE_PREFIX.'slides_home_rev',
		'icon panel'=>'fa-picture-o',
		'label'=>ucfirst($_lang['slide']),
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'slides-home-rev',
			'opt'=>array()
			)
		);			
	$App->homeTables['slides-home-rev'] = array(
		'table'=>DB_TABLE_PREFIX.'slides_home_rev',
		'icon panel'=>'fa-picture-o',
		'label'=>ucfirst($_lang['ultime']).' '.$_lang['slide'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'slides-home-rev',
					'opt'=>array(
						'fieldItemRif'=>'id_owner'
						)
					)
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'slides-home-rev/',
				'path'=>UPLOAD_DIR.'slides-home-rev/'
				)
			)
		);	
	
	}
	
/* SLIDE HOME */
if (in_array(DB_TABLE_PREFIX.'slides_home',$tablesDb) && file_exists(PATH.$App->pathApplications."slides-home/index.php") && Permissions::checkAccessUserModule('slides-home',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['slides-home'] = array(
		'table'=>DB_TABLE_PREFIX.'slides_home',
		'icon panel'=>'fa-picture-o',
		'label'=>ucfirst($_lang['slide']),
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'slides-home-rev',
			'opt'=>array()
			)
		);			
	$App->homeTables['slides-home'] = array(
		'table'=>DB_TABLE_PREFIX.'slides_home',
		'icon panel'=>'fa-picture-o',
		'label'=>ucfirst($_lang['ultime']).' '.$_lang['slide'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'slides-home',
					'opt'=>array(
						'fieldItemRif'=>'id_owner'
						)
					)
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'slides-home/',
				'path'=>UPLOAD_DIR.'slides-home/'
				)
			)
		);	
	
	}
	
/* NEWS */
if (in_array(DB_TABLE_PREFIX.'news',$tablesDb) && file_exists(PATH.$App->pathApplications."news/index.php") && Permissions::checkAccessUserModule('news',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['news'] = array(
		'table'=>DB_TABLE_PREFIX.'news',
		'icon panel'=>'fa-newspaper-o',
		'label'=>ucfirst($_lang['notizie']),
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'news',
			'opt'=>array()
			)
		);			
	$App->homeTables['news'] = array(
		'table'=>DB_TABLE_PREFIX.'news',
		'icon panel'=>'fa-newspaper',
		'label'=>ucfirst($_lang['ultime']).' '.$_lang['notizie'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'news',
					'opt'=>array(
						'fieldItemRif'=>''
						)
					)
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'news/',
				'path'=>UPLOAD_DIR.'news/'
				)
			)
		);	
	}
	
/* BLOG */
if (in_array(DB_TABLE_PREFIX.'blog',$tablesDb) && file_exists(PATH.$App->pathApplications."blog/index.php") && Permissions::checkAccessUserModule('news',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['blog'] = array(
		'table'=>DB_TABLE_PREFIX.'blog',
		'icon panel'=>'fa-comments',
		'label'=>ucfirst($_lang['post']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'blog',
			'opt'=>array()
			)
		);			
	$App->homeTables['blog'] = array(
		'table'=>DB_TABLE_PREFIX.'blog',
		'icon panel'=>'fa-comments',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['post'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'blog',
					'opt'=>array(
						'fieldItemRif'=>''
						)
					)
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'blog/',
				'path'=>UPLOAD_DIR.'blog/'
				)
			)
		);	
	
	}
	


	/* FAQ */
if (in_array(DB_TABLE_PREFIX.'faq',$tablesDb) && file_exists(PATH.$App->pathApplications."faq/index.php") && Permissions::checkIfModulesIsReadable('faq',$App->userLoggedData,$App->user_module_active) == true) {
	//Core::setDebugMode(1);	
	$App->homeBlocks['faq'] = array(
		'table'=>DB_TABLE_PREFIX.'faq',
		'icon panel'=>'fa-question',
		'label'=>ucfirst($_lang['faq']),
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'news',
			'opt'=>array()
			)
		);			
	$App->homeTables['faq'] = array(
		'table'=>DB_TABLE_PREFIX.'faq',
		'icon panel'=>'fa-question',
		'label'=>ucfirst($_lang['ultime']).' '.$_lang['faq'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'faq',
					'opt'=>array(
						'fieldItemRif'=>''
						)
					)
				)
			)
		);	
	
	}

if (in_array(DB_TABLE_PREFIX.'products',$tablesDb) && file_exists(PATH.$App->pathApplications."products/index.php") && Permissions::checkAccessUserModule('products',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['products'] = array(
		'table'=>DB_TABLE_PREFIX.'products',
		'icon panel'=>'fa-tags',
		'label'=>ucfirst($_lang['prodotti']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'products',
			'opt'=>array()
			)
		);			
	$App->homeTables['products'] = array(
		'table'=>DB_TABLE_PREFIX.'products',
		'icon panel'=>'fa-tags',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['prodotti'],
		'fields'=>array(
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
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>false,
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'products/',
				'path'=>UPLOAD_DIR.'products/'
				)
			)
		);	
	
	}
	
if (in_array(DB_TABLE_PREFIX.'ec_products',$tablesDb) && file_exists(PATH.$App->pathApplications."ecommerce/index.php") && Permissions::checkAccessUserModule('ecommerce',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['ec_products'] = array(
		'table'=>DB_TABLE_PREFIX.'ec_products',
		'icon panel'=>'fa-tags',
		'label'=>ucfirst($_lang['prodotti']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'ecommerce',
			'opt'=>array()
			)
		);			
	$App->homeTables['ec_products'] = array(
		'table'=>DB_TABLE_PREFIX.'ec_products',
		'icon panel'=>'fa-tags',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['prodotti'],
		'fields'=>array(
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
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>false,
				),
			'filename'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['immagine']),
				'type'=>'image',
				'pathdef'=>UPLOAD_DIR.'ecommerce/products/',
				'path'=>UPLOAD_DIR.'ecommerce/products/'
				)
			)
		);	
	
}


/* NEWSLETTER */
if (in_array(DB_TABLE_PREFIX.'newsletter_indirizzi',$tablesDb) && file_exists(PATH.$App->pathApplications."/newsletter/index.php") && Permissions::checkAccessUserModule('newsletter',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['newsletter-indsos'] = array(
		'table'=>DB_TABLE_PREFIX.'newsletter_indirizzi',
		'query opt'=>array('clause' => 'created > ? AND confirmed = 0'),
		'icon panel'=>'fa-user-secret',
		'label'=>ucfirst($_lang['indirizzi sospesi']).' '.$_lang['newsletter'],
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'newsletter/listIndSos',
			'opt'=>array()
			)
		);	
	
	$App->homeTables['newsletter-indsos'] = array(
		'table'=>DB_TABLE_PREFIX.'newsletter_indirizzi',
		'query opt'=>array('clause' => 'confirmed = 0'),
		'icon panel'=>'fa-user-secret',
		'label'=>ucfirst($_lang['indirizzi sospesi']).' '.$_lang['newsletter'],
		'fields'=>array(
			'email'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['email']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'newsletter/listIndSos',
					'opt'=>array()
					)
				)
			)
		);	

	$App->homeBlocks['newsletter-ind'] = array(
		'table'=>DB_TABLE_PREFIX.'newsletter_indirizzi',
		'query opt'=>array('clause' => 'created > ? AND confirmed = 1'),
		'icon panel'=>'fa-user',
		'label'=>ucfirst($_lang['indirizzi']).' '.$_lang['newsletter'],
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'newsletter/listInd',
			'opt'=>array()
			)
		);	
		
	$App->homeTables['newsletter-ind'] = array(
		'table'=>DB_TABLE_PREFIX.'newsletter_indirizzi',
		'query opt'=>array('clause' => 'confirmed = 1'),
		'icon panel'=>'fa-user',
		'label'=>ucfirst($_lang['indirizzi']).' '.$_lang['newsletter'],
		'fields'=>array(
			'email'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['email']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'newsletter/listInd',
					'opt'=>array()
					)
				)
			)
		);	
	}

	

	/* PORTFOLIO */
if (in_array('portfolio',$tablesDb) && file_exists(PATH."application/portfolio/index.php") && Permissions::checkAccessUserModule('portfolio',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	Sql::initQuery(DB_TABLE_PREFIX.'portfolio',array('*'),array(),$clause='',$order='created DESC',$limit=' LIMIT 5 OFFSET 0',$options='',false);
	$App->items = Sql::getRecords();
	Sql::initQuery(DB_TABLE_PREFIX.'portfolio',array('id'),array($App->lastLogin),'created > ?','','','',false);		
	$numItems = Sql::countRecord();	
	$App->moduleHome['portfolio'] = array('urlDetails'=>URL_SITE_ADMIN.'portfolio/listItem','urlItem'=>array('string'=>'{{URLSITEADMIN}}portfolio/listItem','opz'=>array('filedvalue'=>'id_cat')),'label'=>'Ultimi Lavori','fieldsData'=>$App->items,'fields'=>array('title_it'=>array('label'=>'Titolo','url'=>true),'filename'=>array('label'=>'Immagine','type'=>'image','path'=>UPLOAD_DIR.'portfolio/')),'countnew'=>true,'sexSuffix'=>'i','label count'=>'Nuovi Lavori','icon count'=>'fa-tags','type count'=>'info','numItems'=>$numItems);
	}
	
if (in_array('portfolio_images',$tablesDb) && file_exists(PATH."application/portfolio/index.php") && Permissions::checkAccessUserModule('portfolio',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	Sql::initQuery(DB_TABLE_PREFIX.'portfolio_images',array('*'),array(),$clause='',$order='created DESC',$limit=' LIMIT 5 OFFSET 0',$options='',false);
	$App->items = Sql::getRecords();
	Sql::initQuery(DB_TABLE_PREFIX.'portfolio_images',array('id'),array($App->lastLogin),'created > ?','','','',false);		
	$numItems = Sql::countRecord();	
	$App->moduleHome['portfolio-images'] = array('urlDetails'=>URL_SITE_ADMIN.'portfolio/listIimg','urlItem'=>array('string'=>'{{URLSITEADMIN}}portfolio/listIimg','opz'=>array('filedvalue'=>'id_owner')),'label'=>'Ultime Immagini Lavori','fieldsData'=>$App->items,'fields'=>array('title_it'=>array('label'=>'Titolo','url'=>true),'filename'=>array('label'=>'Immagine','type'=>'image','pathdef'=>UPLOAD_DIR.'portfolio/','path'=>UPLOAD_DIR.'portfolio/images/')),'countnew'=>true,'sexSuffix'=>'e','label count'=>'Nuove Immagini Lavori','icon count'=>'fa-picture-o','type count'=>'info','numItems'=>$numItems);
	}

if (in_array('portfolio_cat',$tablesDb) && file_exists(PATH."application/portfolio/index.php") && Permissions::checkAccessUserModule('portfolio',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	//Sql::initQuery(DB_TABLE_PREFIX.'portfolio_cat',array('*'),array(),$clause='',$order='created DESC',$limit=' LIMIT 5 OFFSET 0',$options='',false);
	//$App->items = Sql::getRecords();
	Sql::initQuery(DB_TABLE_PREFIX.'portfolio_cat',array('id'),array($App->lastLogin),'created > ?','','','',false);		
	$numItems = Sql::countRecord();	
	$App->moduleHome['portfolio-cat'] = array('urlDetails'=>URL_SITE_ADMIN.'portfolio/listCate','urlItem'=>array('string'=>'{{URLSITEADMIN}}portfolio/listCate','opz'=>array('filedvalue'=>'id')),'label'=>'Ultime Categorie Lavori','fieldsData'=>'','fields'=>array('title_it'=>array('label'=>'Titolo','url'=>true),'filename'=>array('label'=>'Immagine','type'=>'image','pathdef'=>UPLOAD_DIR.'portfolio/','path'=>UPLOAD_DIR.'/portfolio/categories/')),'countnew'=>true,'sexSuffix'=>'e','label count'=>'Nuove Categorie Lavori','icon count'=>'fa-folder-o','class count'=>'panel-green','numItems'=>$numItems);
	}


	/* VIDEO */
if (in_array(DB_TABLE_PREFIX.'video',$tablesDb) && file_exists(PATH.$App->pathApplications."video/index.php") && Permissions::checkAccessUserModule('news',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['video'] = array(
		'table'=>DB_TABLE_PREFIX.'video',
		'icon panel'=>'fa-youtube',
		'label'=>ucfirst($_lang['video']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'video',
			'opt'=>array()
			)
		);			
	$App->homeTables['youtube'] = array(
		'table'=>DB_TABLE_PREFIX.'video',
		'icon panel'=>'fa-youtube',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['video'],
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'video',
					'opt'=>array(
						'fieldItemRif'=>''
						)
					)
				)
			)
		);	
	
	}
	
	
/* ECOMMERCE */

if (in_array(DB_TABLE_PREFIX.'ec_customers',$tablesDb) && file_exists(PATH.$App->pathApplications."/ecommerce/index.php") && Permissions::checkAccessUserModule('ecommerce',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
		$App->homeBlocks['ec_customers'] = array(
		'table'=>DB_TABLE_PREFIX.'ec_customers',
		'icon panel'=>'fa-user',
		'label'=>ucfirst($_lang['clienti']),
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'ecommerce/listCust',
			'opt'=>array()
			)
		);	
	
	$App->homeTables['ec_customers'] = array(
		'table'=>DB_TABLE_PREFIX.'ec_customers',
		'icon panel'=>'fa-user',
		'label'=>ucfirst($_lang['ultimi']).' '.$_lang['clienti'],
		'fields'=>array(
			'email'=>array(
				'multilanguage'=>0,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['clienti']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'ecommerce/listCust',
					'opt'=>array()
					)
				)
			)
		);

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
?>