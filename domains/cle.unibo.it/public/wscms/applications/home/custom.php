<?php
/* wscms/home/custom.php v.3.5.4. 06/08/2019 */
/* sector */
if (in_array(DB_TABLE_PREFIX.'sector',$tablesDb) && file_exists(PATH.$App->pathApplications."sector/index.php") && Permissions::checkAccessUserModule('sector',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['sector'] = array(
		'table'=>DB_TABLE_PREFIX.'sector',
		'icon panel'=>'fa-gear',
		'label'=>'Our Sector',
		'sex suffix'=>ucfirst($_lang['nuovi']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'sector',
			'opt'=>array()
		)
	);			
	$App->homeTables['sector'] = array(
		'table'=>DB_TABLE_PREFIX.'sector',
		'icon panel'=>'fa-gear',
		'label'=>ucfirst($_lang['ultimi']).' Our Sector',
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'sector',
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
				'pathdef'=>UPLOAD_DIR.'sector/',
				'path'=>UPLOAD_DIR.'sector/'
			)
		)
	);	
}
/* evidence */
if (in_array(DB_TABLE_PREFIX.'evidenza',$tablesDb) && file_exists(PATH.$App->pathApplications."evidenza/index.php") && Permissions::checkAccessUserModule('evidenza',$App->userLoggedData,$App->user_modules_active,$App->modulesCore) == true) {
	$App->homeBlocks['evidenze'] = array(
		'table'=>DB_TABLE_PREFIX.'evidenza',
		'icon panel'=>'fa-book',
		'label'=>'In evidenze',
		'sex suffix'=>ucfirst($_lang['nuove']),
		'type'=>'info',
		'url'=>true,
		'url item'=>array (
			'string'=>URL_SITE_ADMIN.'evidenza',
			'opt'=>array()
			)
		);			
	$App->homeTables['evidenze'] = array(
		'table'=>DB_TABLE_PREFIX.'evidenza',
		'icon panel'=>'fa-book',
		'label'=>ucfirst($_lang['ultime']).' In Evidence',
		'fields'=>array(
			'title'=>array(
				'multilanguage'=>1,
				'type'=>'varchar',
				'label'=>ucfirst($_lang['titolo']),
				'url'=>true,
				'url item'=>array(
					'string'=>URL_SITE_ADMIN.'evidenza',
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
				'pathdef'=>UPLOAD_DIR.'evidenza/',
				'path'=>UPLOAD_DIR.'evidenza/'
				)
			)
		);	
}


	

		/* 
		MODELLO 

		if(file_exists(PATH."application/modello/index.php") && Permissions::checkAccessUserModule('modello',$_MY_SESSION_VARS['usr'],$App->user_modules_active) == true) {
			--- crea la query ---
			Sql::initQuery(Sql::getTablePrefix().'modello',array('*'),array(),$clause='',$order='created DESC',$limit=' LIMIT 5 OFFSET 0',$options='',false);
			$App->items = Sql::getRecords();
			Sql::initQuery(Sql::getTablePrefix().'modello',array('id'),array($App->lastLogin),'created > ?','','','',false);		
			$numItems = Sql::countRecord();	

			--- configurazione ---

			$App->moduleHome['faq'] = array(
				'label'=>'Ultime F.A.Q.',
				'fieldsData'=>$App->items,
				'fields'=>array(
					'title_it'=>array(
						'label'=>'Titolo','url'=>true
						),					
					),		
				'countnew'=>true,
				'label count'=>'Nuove  F.A.Q.',
				'icon count'=>'fa-question',
				'class count'=>'panel-red',
				'numItems'=>$numItems

				);
			}
*/		
?>