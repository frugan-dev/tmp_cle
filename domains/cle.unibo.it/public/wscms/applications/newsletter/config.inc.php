<?php
/* wscms/newsletter/config.inc.php v.3.1.0. 27/12/2016 */

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','help_small','help'),array('newsletter'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;


/* configurazione */
$App->params->applicationName = Core::$request->action;
$App->params->tableBaseName = 'newsletter';

$App->params->categories = 1;

$App->params->templatesFolder =  ADMIN_PATH_UPLOAD_DIR.'newsletter-templates/';

$App->params->codeVersion = ' 3.1.0.';
$App->params->pageTitle = 'Newsletter';
$App->params->breadcrumb[] = '<li class="active"><i class="icon-user"></i> La newsletter</li>';

$App->params->module_has_config = 1;

$App->params->appTableIndCat = DB_TABLE_PREFIX.$App->params->tableBaseName.'_indirizzi_cat';
$App->params->appTableInd = DB_TABLE_PREFIX.$App->params->tableBaseName.'_indirizzi';
$App->params->appTableRifCatInd = DB_TABLE_PREFIX.$App->params->tableBaseName.'_cat_ind';
$App->params->appTableNew = DB_TABLE_PREFIX.$App->params->tableBaseName;
$App->params->appTableConf = DB_TABLE_PREFIX.$App->params->tableBaseName.'_sendconfig';
$App->params->appTableIndInvio = DB_TABLE_PREFIX.$App->params->tableBaseName.'_indirizzi_invio';
$App->params->appTableNewCode = DB_TABLE_PREFIX.$App->params->tableBaseName.'_code';


$App->params->fieldsInd = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'name'=>array('label'=>'Nome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'surname'=>array('label'=>'Cognome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>'Email','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'confirmed'=>array('label'=>'Confermato','searchTable'=>false,'required'=>false,'type'=>'int'),
	'dateconfirmed'=>array('label'=>'Created','searchTable'=>false,'required'=>false,'type'=>'datetime'),
	'hash'=>array('label'=>'Hash','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	)
);
	
$App->params->fieldsIndCat = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'public'=>array('label'=>'Pubblica','required'=>false,'type'=>'int','defValue'=>0),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	) 
);
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);
	$App->params->fieldsIndCat['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar');
	}


$App->params->fieldsNew = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'datatimeins'=>array('label'=>'Data','searchTable'=>false,'required'=>true,'type'=>'datatime'),
	'datatimesent'=>array('label'=>'Data','searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'title_it'=>array('label'=>'Titolo Ita','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'content_it'=>array('label'=>'Contenuto','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'template'=>array('label'=>'Template','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'sent'=>array('label'=>'Spedita','searchTable'=>false,'required'=>false,'type'=>'int'),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	) 
);
	
$App->params->fieldsNewCode = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'content_it'=>array('label'=>'Contenuto','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	) 
);

$App->params->labels['code'] = array('item'=>'codice','itemSex'=>'o','items'=>'codici','itemsSex'=>'i','owner'=>'newsletter','ownerSex'=>'a','owners'=>'newsletter','ownersSex'=>'e');
$App->params->labels['indsos'] = array('item'=>'indirizzo sospeso','itemSex'=>'o','items'=>'indirizzi sospesi','itemsSex'=>'i','owner'=>'categoria','ownerSex'=>'a','owners'=>'categorie','ownersSex'=>'e');
$App->params->labels['indcat'] = array('item'=>'categoria','itemSex'=>'a','items'=>'categorie','itemsSex'=>'e','son'=>'indirizzo','sonSex'=>'i','sons'=>'indirizzi','sonsSex'=>'i','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->labels['ind'] = array('item'=>'indirizzo','itemSex'=>'o','items'=>'indirizzi','itemsSex'=>'i','owner'=>'categoria','ownerSex'=>'a','owners'=>'categorie','ownersSex'=>'e');
$App->params->labels['new'] = array('item'=>'newsletter','itemSex'=>'a','items'=>'newsletter','itemsSex'=>'e','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');

/* legge la configurazione */
$App->settings = new stdClass;
Sql::initQuery($App->params->appTableConf,array('*'));
Sql::setClause('active = 1');
Sql::setOptions(array('fieldTokeyObj'=>'name'));
$App->settings = Sql::getRecords();

/* INDIRIZZI */
$App->params->labels['ind'] = array('item'=>'indirizzo','itemSex'=>'o','items'=>'indirizzi','itemsSex'=>'i','owner'=>'categoria','ownerSex'=>'a','owners'=>'categorie','ownersSex'=>'e');
$App->params->tables['ind'] = DB_TABLE_PREFIX.$App->params->tableBaseName.'_indirizzi';
$App->params->fields['ind'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'name'=>array('label'=>'Nome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'surname'=>array('label'=>'Cognome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>'Email','searchTable'=>true,'required'=>true,'type'=>'varchar'),

	'confirmed'=>array('label'=>'Confermato','searchTable'=>false,'required'=>false,'type'=>'int'),

	'dateconfirmed'=>array('label'=>'Created','searchTable'=>false,'required'=>false,'type'=>'datetime'),
	'hash'=>array('label'=>'Hash','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),
	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 1,
		'forcedValue'                           => 1
	) 
);
	
	
/* CATEGORIE INDIRIZZI  */
$App->params->labels['indcat'] = array('item'=>'categoria','itemSex'=>'a','items'=>'categorie','itemsSex'=>'e','son'=>'indirizzo','sonSex'=>'i','sons'=>'indirizzi','sonsSex'=>'i','owner'=>'','ownerSex'=>'','owners'=>'','ownersSex'=>'');
$App->params->tables['indcat'] = DB_TABLE_PREFIX.$App->params->tableBaseName.'_indirizzi_cat';


$App->params->tables['rifcatind'] = DB_TABLE_PREFIX.$App->params->tableBaseName.'_cat_ind';
$App->params->uploadPathDirs['backup'] = ADMIN_PATH_UPLOAD_DIR."newsletter-backup/";

/* NEWSLETTER */
$App->params->tables['new'] = DB_TABLE_PREFIX.$App->params->tableBaseName;


// CONFIGURAZIONE
$App->params->uploadPaths['conf'] = ADMIN_PATH_UPLOAD_DIR."newsletter/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."newsletter/";
$App->params->tables['conf'] = Config::$DatabaseTables['newsletter config'];
$App->params->fields['conf'] = Config::$DatabaseTablesFields['newsletter config'];

?>