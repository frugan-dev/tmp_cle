<?php
/* wscms/slides-home-rev/config.inc.php v.3.5.4. 25/06/2019 */

$App->params = new stdClass();
$App->params->label = "Slides Home Rev";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','label','help_small','help'),array('slideshomerev'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();
$App->params->item_contents = 2;

$App->params->codeVersion = ' v.3.5.4.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->slide_types = array();
if (isset($_lang['slide types'])) $App->slide_types = $_lang['slide types'];

$App->layer_types = array();
if (isset($_lang['layer types'])) $App->layer_types = $_lang['layer types'];

/* ITEM */
$App->params->orderTypes['item'] = 'ASC';
$App->params->uploadPaths['item'] = ADMIN_PATH_UPLOAD_DIR."slides-home-rev/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."slides-home-rev/";
$App->params->tables['item'] = DB_TABLE_PREFIX.'slides_home_rev';
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','autoinc'=>true,'primary'=>true),
	'user_id'=>array('label'=>Config::$langVars['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'title' => array('label'=>ucfirst($_lang['titolo']),'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar|255'),
	'org_filename'=>array('label'=>'','searchTable'=>true,'required'=>false,'type'=>'varchar255'),
	'li_data'=>array('label'=>'LI Data','required'=>false,'type'=>'text','defValue'=>''),
	'hide_image'=>array('label'=>Config::$langVars['nascondi immagine'],'required'=>false,'type'=>'int|1',
	'defValue'                              		=> 1,
	'forcedValue'                           		=> 1
),
	'ordering'=>array('label'=>'Ord','required'=>false,'type'=>'int8','defValue'=>1),
	'slide_type'=>array('label'=>'Tipo','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0'),
	'access_type'=>array('label'=>'Tipo accesso','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0'),
	'access_read'=>array('label'=>$_lang['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>$_lang['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'created'                                   		=> array(
		'label'                                 		=> Config::$langVars['creazione'],
		'searchTable'                           		=> false,
		'required'                              		=> false,
		'type'                                  		=> 'datatime',
		'defValue'                              		=> Config::$nowDateTimeIso,
		'forcedValue'                           		=> Config::$nowDateTimeIso
	),
	'active'                                   			=> array(
		'label'                                 		=> Config::$langVars['attiva'],
		'required'                              		=> false,
		'type'                                  		=> 'int|1',
		'defValue'                              		=> 1,
		'forcedValue'                           		=> 1
	)
);		

	
/* LAYER */
$App->params->orderTypes['laye'] = 'ASC';
$App->params->uploadPaths['laye'] = ADMIN_PATH_UPLOAD_DIR."slides-home-rev/";
$App->params->uploadDirs['laye'] = UPLOAD_DIR."slides-home-rev/";
$App->params->tables['laye'] = DB_TABLE_PREFIX.'slides_home_rev_layers';
$App->params->fields['laye'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','autoinc'=>true,'primary'=>true),
	'slide_id'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'filename'=>array('label'=>'Nome File','searchTable'=>false,'required'=>false,'type'=>'varchar|255'),
	'org_filename'=>array('label'=>'','searchTable'=>true,'required'=>false,'type'=>'varchar255'),
	'ordering'=>array('label'=>'Ord','required'=>false,'type'=>'int8','defValue'=>1),
	'type'=>array('label'=>$_lang['tipo'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0'),
	'created'                                   		=> array(
		'label'                                 		=> Config::$langVars['creazione'],
		'searchTable'                           		=> false,
		'required'                              		=> false,
		'type'                                  		=> 'datatime',
		'defValue'                              		=> Config::$nowDateTimeIso,
		'forcedValue'                           		=> Config::$nowDateTimeIso
	),
	'active'                                   			=> array(
		'label'                                 		=> Config::$langVars['attiva'],
		'required'                              		=> false,
		'type'                                  		=> 'int|1',
		'defValue'                              		=> 1,
		'forcedValue'                           		=> 1
	)
);		
foreach($globalSettings['languages'] AS $lang) {
	$required = ($lang == 'it' ? true : false);




	$App->params->fields['laye']['title_'.$lang] = array('label'=>$_lang['titolo'].' '.$lang,'searchTable'=>true,'required'=>$required,'type'=>'varchar|255');

	$App->params->fields['laye']['content_'.$lang] = array('label'=>$_lang['contenuto'].' '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text');
	$App->params->fields['laye']['template_'.$lang] = array('label'=>$_lang['template'].' '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text','defValue'=>'');
	
	$App->params->fields['laye']['url_'.$lang] = array('label'=>$_lang['url'].' '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|255');
	$App->params->fields['laye']['target_'.$lang] = array('label'=>$_lang['target'].' '.$lang,'searchTable'=>true,'required'=>false,'type'=>'varchar|255');
}
?>