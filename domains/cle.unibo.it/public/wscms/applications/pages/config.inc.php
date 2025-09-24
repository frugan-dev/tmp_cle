<?php
/* wscms/pages/config.inc.php v.3.5.4. 27/05/2019 */

/* prende i dati del modulo template dal file conf */
include_once(PATH.$App->pathApplications."pagetemplates/config.inc.php");
$paramsuploadPaths = $App->params->uploadPaths['item'];
$paramsuploadDirs = $App->params->uploadDirs['item'];

$App->params = new stdClass();
$App->params->label = "Pagine";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('pages'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->template['uploadpathdir'] = $paramsuploadPaths;
$App->params->template['defuploaddir'] = $paramsuploadDirs;

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->orderTypes = array();

$App->params->codeVersion = ' 3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'pages';

/* ITEMS */
$App->params->orderTypes['item'] = 'ASC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."pages/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."pages/";
$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_user'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>$App->userLoggedData->id),
	'parent'=>array('label'=>'Parent','searchTable'=>false,'required'=>false,'type'=>'varchar','defValue'=>0),
	'id_template'=>array('label'=>$_lang['template'],'searchTable'=>false,'required'=>false,'type'=>'int'),
	'ordering'=>array('label'=>$_lang['ordinamento'],'required'=>false,'type'=>'int|8','validate'=>'int','defValue'=>1),
	'menu'=>array('label'=>'In menu?','searchTable'=>false,'required'=>false,'type'=>'int','defValue'=>0),
	'alias'=>array('label'=>'Alias','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'url'=>array('URL'=>'Alias','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'target'=>array('label'=>'Target','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'jscript_init_code'=>array('label'=>'Codice Javascript inizio BODY','required'=>false,'type'=>'varchar','defValue'=>''),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar','defValue'=>''),
	'filename1'=>array('label'=>$_lang['immagine bottom'],'searchTable'=>false,'required'=>false,'type'=>'varchar|255','defValue'=>''),


		

	'galleriesimages_categories_id' =>array(
		'label'									=> $_lang['proprietario'],
		'searchTable'							=> false,
		'required'								=> true,
		'type'									=> 'int',
		'defValue'                              => 0,
		'forcedValue'                           => 0,
		'validate'								=> 0
	),


	'org_filename1'=>array('label'=>$_lang['immagine bottom'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'access_read'=>array('label'=>$_lang['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>$_lang['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),

	'created'                                   => array (
		'label'                                 => Config::$langVars['creazione'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso
	),

	'updated'                                   => array (
		'label'                                 => Config::$langVars['aggiornamento'],
		'searchTable'                           => false,
		'required'                              => false,
		'type'                                  => 'datatime',
		'defValue'                              => Config::$nowDateTimeIso,
		'forcedValue'                           => Config::$nowDateTimeIso,
		'validate'								=> 'datetimepicker'
	),

	'show_updated'                            						=> array (
		'label'                                 						=> 'Mostra updated',
		'required'                              						=> false,
		'type'                                  						=> 'int|1',
		'defValue'                              						=> 0,
		'forcedValue'                           						=> 0
	),

	'active'                                    => array (
		'label'                                 => Config::$langVars['attiva'],
		'required'                              => false,
		'type'                                  => 'int|1',
		'defValue'                              => 0,
		'forcedValue'                           => 0
	)
);	
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == $_lang['user'] ? true : false);
	
	$App->params->fields['item']['meta_title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|255');
	$App->params->fields['item']['meta_description_'.$lang] = array('label'=>'Descrizione META '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|300');
	$App->params->fields['item']['meta_keyword_'.$lang] = array('label'=>'Keyword META '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|255');
	$App->params->fields['item']['title_seo_'.$lang] = array('label'=>'Titolo SEO '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|255');

	$App->params->fields['item']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar',
	'forcedValue'                           => '');
	}
	
/* BLOCKS */
$App->params->uploadPaths['iblo'] = ADMIN_PATH_UPLOAD_DIR."pages/blocks/";
$App->params->uploadDirs['iblo'] = UPLOAD_DIR."pages/blocks/";
$App->params->orderTypes['iblo'] = 'DESC';
$App->params->tables['iblo'] = $App->params->tableRif.'_blocks';
$App->params->fields['iblo'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>false,'searchTable'=>false,'type'=>'int'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'url'=>array('URL'=>'Alias','searchTable'=>true,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'target'=>array('label'=>'Target','searchTable'=>true,'required'=>false,'type'=>'varchar|20','defValue'=>''),
	'ordering'=>array('label'=>$_lang['ordinamento'],'required'=>false,'type'=>'int|8','validate'=>'int','defValue'=>1),
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
	$required = ($lang == $_lang['user'] ? true : false);
	$App->params->fields['iblo']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar',
	'forcedValue'                           => ''
);
	$App->params->fields['iblo']['content_'.$lang] = array('label'=>$_lang['contenuto'].'  '.$lang,'searchTable'=>false,'required'=>false,'type'=>'longtext',
	'forcedValue'                           => '');
	}
	
/* RESOURCES */
$App->params->fields['resources'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>true,'searchTable'=>false,'type'=>'int'),
	'resource_type'=>array('label'=>'Type resource','required'=>true,'searchTable'=>false,'type'=>'int'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>true,'type'=>'varchar'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'extension'=>array('label'=>'Ext','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'code'=>array('label'=>'Code','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'size_file'=>array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'size_image'=>array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'type'=>array('label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'ordering'=>array('label'=>$_lang['ordinamento'],'required'=>false,'type'=>'int|8','validate'=>'int','defValue'=>1),
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
	$searchTable = true;
	$required = ($lang == $_lang['user'] ? true : false);
	$App->params->fields['resources']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>$searchTable,'required'=>$required,'type'=>'varchar');
	$App->params->fields['resources']['content_'.$lang] = array('label'=>$_lang['contenuto'].'  '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text','defValue'=>'');	
	}
	
/* ITEM IMAGES  type = 1 */
$App->params->uploadPaths['iimg'] = ADMIN_PATH_UPLOAD_DIR."pages/";
$App->params->uploadDirs['iimg'] = UPLOAD_DIR."pages/";
$App->params->orderTypes['iimg'] = 'ASC';

/* ITEM FILES type = 2 */
$App->params->uploadPaths['ifil'] = ADMIN_PATH_UPLOAD_DIR."pages/";
$App->params->uploadDirs['ifil'] = UPLOAD_DIR."pages/";
$App->params->orderTypes['ifil'] = 'ASC';

/* ITEM GALLERY type = 3 */
$App->params->uploadPaths['igal'] = ADMIN_PATH_UPLOAD_DIR."pages/";
$App->params->uploadDirs['igal'] = UPLOAD_DIR."pages/";
$App->params->orderTypes['igal'] = 'ASC';

/* ITEM VIDEO  type = 4*/
$App->params->orderTypes['ivid'] = 'ASC';
	
/* BLOCK IMAGES  type = 1 */
$App->params->uploadPaths['bimg'] = ADMIN_PATH_UPLOAD_DIR."pages/blocks/";
$App->params->uploadDirs['bimg'] = UPLOAD_DIR."pages/blocks/";
$App->params->orderTypes['bimg'] = 'ASC';

/* BLOCK FILES type = 2 */
$App->params->uploadPaths['bfil'] = ADMIN_PATH_UPLOAD_DIR."pages/blocks/";
$App->params->uploadDirs['bfil'] = UPLOAD_DIR."pages/blocks/";
$App->params->orderTypes['bfil'] = 'ASC';

/* BLOCK VIDEO  type = 4*/
$App->params->orderTypes['bvid'] = 'ASC';

// galleries categories
$App->params->tables['galleriesimages_categories'] = DB_TABLE_PREFIX.'galleriesimages_categories';


$App->params->orderTypes['page'] = $App->params->orderTypes['item'];
$App->params->fields['page'] = $App->params->fields['item'];
$App->params->tables['page'] = $App->params->tables['item'];

$App->params->tables['item resources owner'] = DB_TABLE_PREFIX.'pages';
$App->params->uploadDirs['item resources owner'] = UPLOAD_DIR."pages/";

$App->params->tables['item resources'] = DB_TABLE_PREFIX.'pages_resources';
$App->params->fields['item resources'] = Config::$DatabaseTablesFields['resources for item'];
$App->params->uploadPaths['item resources'] = ADMIN_PATH_UPLOAD_DIR."pages/";
$App->params->uploadDirs['item resources'] = UPLOAD_DIR."pages/";
$App->params->orderTypes['item resources'] = 'ASC';

?>