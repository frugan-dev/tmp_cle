<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/home/index.php v.4.5.1. 25/11/2018
*/

//Core::setDebugMode(1);	

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->params = new stdClass();
$App->params->label = 'Home';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('home'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj; 

$tablesDb = Sql::getTablesDatabase($globalSettings['database'][DATABASE]['name']);
//print_r($tablesDb);

/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = $App->params->label;
$App->pageSubTitle = $_lang['la pagina home'];

$Module = new Module('','home');
$App->Module = $Module;

$App->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->countPanel = array();
$today = Config::$nowDateTimeIso;
$App->lastLogin = (isset($_MY_SESSION_VARS['lastLogin']) ? $_MY_SESSION_VARS['lastLogin'] : $today);
//$App->lastLogin = '2015-01-01 00:00:00';
$App->lastLoginLocale = DateFormat::dateFormating( $App->lastLogin, $format = 'd/m/Y H:i', $in_format = false, $f = '' );

$App->templateApp = 'list.html';
$numCountPanel = 0;
switch(Core::$request->method) {

	default;	
		$App->moduleHome = array();
		$App->homeBlocks = array();
		$App->homeTables = array(); 
		$App->panels = array('info'=>array('primary','default','info'),'alert'=>array('warning'),'danger'=>array('danger'),'success'=>array('success'));	
		
		$App->panelsInfo = count($App->panels['info']);
		$App->panelsAlert = count($App->panels['alert']);
		$App->panelsDanger = count($App->panels['danger']);
		$App->panelsSuccess = count($App->panels['success']);
	
		if (file_exists(PATH.$App->pathApplications."home/base.php")) include_once(PATH.$App->pathApplications."home/base.php");
		if (file_exists(PATH.$App->pathApplications."home/extra.php")) include_once(PATH.$App->pathApplications."home/extra.php");
		//if (file_exists(PATH.$App->pathApplications."home/custom.php")) include_once(PATH.$App->pathApplications."home/custom.php");
		
		$App->countModuleHome = ToolsStrings::multiSearch($App->moduleHome, array('countnew' => true));	
	break;
	}
	

$arr = array();
if (is_array($App->homeBlocks) && count($App->homeBlocks) > 0) {
	$panelsinfo = 0;
	$panelsalert = 0;
	$panelsdanger = 0;
	$panelssuccess = 0;
	
	foreach ($App->homeBlocks AS $key => $value) {
		$module = (isset($value['module']) && $value['module'] != '' ? $value['module'] : $key);
		
		$whereclause = 'created > ?';
		$whereclauseValRif = array($App->lastLogin);
		if (isset($value['query opt']['clause']) && $value['query opt']['clause'] != '') {
			$whereclause = $value['query opt']['clause'];
			if (isset($value['query opt']['clauseValRif'])) $whereclauseValRif = $value['query opt']['clauseValRif'];
		}

		$value['class'] = (isset($value['class']) ? $value['class'] :'');
		$value['type'] = (isset($value['type']) ? $value['type'] :'');
		Sql::initQuery($value['table'],array('id'),$whereclauseValRif,$whereclause,'','',false);		
		$items = Sql::countRecord();
		$value['items'] =  $items;
		
		if ($value['class'] == '') {
			switch($value['type']) {
				case 'alert':
					$value['class'] = $App->panels['alert'][$panelsalert];
					$panelsalert = $panelsalert + 1;
					if ($panelsalert > ($App->panelsAlert - 1)) $panelsalert = 0;
				break;					
				case 'danger':
					$value['class'] = $App->panels['danger'][$panelsdanger];
					$panelsalert = $panelsdanger + 1;
					if ($panelsdanger > ($App->panelsDanger - 1)) $panelsdanger = 0;
				break;
				case 'success':
					$value['class'] = $App->panels['success'][$panelssuccess];
					$panelssuccess = $panelssuccess + 1;
					if ($panelssuccess > ($App->panelsSuccess - 1)) $panelssuccess = 0;
				break;
										
				default:
				case 'info':
					$value['class'] = $App->panels['info'][$panelsinfo];
					$panelsinfo = $panelsinfo + 1;
					if ($panelsinfo > ($App->panelsInfo - 1)) $panelsinfo = 0;
				break;				
				}
			
			/* aggiungi url */
			if (isset($value['url']) && $value['url'] == true) {
				$value['url'] = $Module->getItemBlockUrl($value,$App->lastLogin);
				} else {
					$value['url'] = URL_SITE_ADMIN.$module;
					}							
			}
		$arr[] = $value;
		}
	}
$App->homeBlocks = $arr;		

//ToolsStrings::dump($App->homeBlocks);die();

/* sistemo i dati */
$arr = array();
if (is_array($App->homeTables) && count($App->homeTables) > 0) {
	foreach ($App->homeTables AS $key => $value) {	
		/* aggiunge i campi */	
		$fields = array('*');
		$whereclause = '';
		$whereclauseValRif = array();
		$order = 'created DESC';
		if (isset($value['query opt']['order']) && $value['query opt']['order'] != '') $order = $value['query opt']['order'];	
		if (isset($value['query opt']['clause']) && $value['query opt']['clause'] != '') {
			$whereclause = $value['query opt']['clause'];
			if (isset($value['query opt']['clauseValRif'])) $whereclauseValRif = $value['query opt']['clauseValRif'];
		}

		Sql::initQuery($value['table'],$fields,$whereclauseValRif,$whereclause,$order,' LIMIT 5 OFFSET 0','',false);
		$value['itemdata'] = Sql::getRecords();
		
		//print_r($value['itemdata']);
		
		$formatdataorder = 'datetime';
		if (isset($value['query opt']['formatdataorder']) && $value['query opt']['formatdataorder'] != '') $formatdataorder = $value['query opt']['formatdataorder'];
		/* sistemo i dati */
		$arr1 = array();
		if (is_array($value['itemdata']) && count($value['itemdata']) > 0) {
			foreach ($value['itemdata'] AS $key1 => $value1) {
				
				/* data */
				$fieldCreated = 'created';
				if (isset($value['query opt']['fieldcreated']) && $value['query opt']['fieldcreated'] != '') $fieldCreated = $value['query opt']['fieldcreated'];		
				if ($formatdataorder == 'date') {
					$data = DateTime::createFromFormat('Y-m-d',$value1->$fieldCreated);
					$value1->datacreated = '<a href="'.URL_SITE_ADMIN.$key.'" title="'.ucfirst($_lang['creata il']).' '.$data->format('d/m/Y').'"><i class="fas fa-clock"></i></a>';
				} else {
					$data = DateTime::createFromFormat('Y-m-d H:i:s',$value1->$fieldCreated);
					$value1->datacreated = '<a href="'.URL_SITE_ADMIN.$key.'" title="'.ucfirst($_lang['creata il']).' '.$data->format('d/m/Y').'"><i class="fas fa-clock"></i></a>';
				}
				
				
						
				/* genera url */
				$value1->url = URL_SITE_ADMIN.$key;				
				if (is_array($value['fields']) && count($value['fields']) > 0) {
					foreach ($value['fields'] AS $keyF => $valueF) {
						/* creo output del del campo */	
						$str = '';						
						if ($keyF != '') {
							//echo $keyF;
							$type = (isset($value['fields'][$keyF]['type']) && $value['fields'][$keyF]['type'] != '' ? $value['fields'][$keyF]['type'] : '');
							switch($type) {									
								case 'text':
									if (isset($value1->$keyF)) $output = strip_tags($value1->$keyF);
								break;
								case 'image':																
									$path = (isset($value['fields'][$keyF]['path']) ? $value['fields'][$keyF]['path'] :  UPLOAD_DIR.'/');	
									$pathdef = (isset($value['fields'][$keyF]['path def']) ? $value['fields'][$keyF]['path def'] :  '');	
									if ($pathdef == '')	$pathdef = $path;																																
									if ($value1->$keyF != ''){
										$output = '<a class="" href="'.$path.$value1->$keyF.'" data-lightbox="images" data-title="'.$value1->$keyF.'" title="'.$value1->$keyF.'"><img class="img-thumbnail  img-miniature" src="'.$path.$value1->$keyF.'" alt="'.$value1->$keyF.'"></a>';
										} else {
											$output = '<img class="img-thumbnail img-miniature"  src="'.$pathdef.$value1->$keyF.'default/image.png" alt="'.ucfirst($_lang['immagine di default']).'">';
											}
								break;							
								case 'imagefolder':		
									$folderField = (isset($value['fields'][$keyF]['folderField']) ? $value['fields'][$keyF]['folderField'] : 'folder_name');														
									$path = (isset($value['fields'][$keyF]['path']) ? $value['fields'][$keyF]['path'] :  UPLOAD_DIR.'/');
									$path =	$path.$value1->$folderField;																		
									if ($value1->$keyF != ''){
										$output = '<a class="" href="'.$path.$value1->$keyF.'" data-lightbox="images" data-title="'.$value1->$keyF.'" title="'.ucfirst($_lang['immagine zoom']).'"><img class="img-fluid img-thumbnail w-50" src="'.$path.$value1->$keyF.'" alt=""></a>';
									}
								break;																
								case 'file':															
									if ($value1->$keyF != ''){
										$u = $Module->getItemUrl($value1,$value['fields'][$keyF]['url item']);
										$output = '<a class="" href="'.$u.'" title="'.ucfirst($_lang['scarica il file']).'">'.$value1->$keyF.'</a>';
										}
								break;
								
								case 'avatar':															
									if ($value1->$keyF != ''){
										$output = '<img class="img-miniature-home" src="'.URL_SITE.'ajax/renderuseravatarfromdb.php?id='.$value1->id.'" alt="Avatar">';
									}

								break;
								
								case 'arraykey':
								$array = array();
								$key = '';
								if (isset($value1->$keyF) && $value1->$keyF |= '') $key = $value1->$keyF;
								if (isset($valueF['array']) && is_array($valueF['array'])) $array = $valueF['array'];
								$output = (isset($array[$key]) ? $array[$key] : '');
								break;
								
								case 'amount':
									$currency = (isset($valueF['currency']) ? $valueF['currency'] : '€');
									$amount = (isset($value1->$keyF) ? $value1->$keyF : '0');
									$output = $currency.' '.number_format($amount,2,',','.');
								break;

								
								default:
									$f = $keyF;
									if (isset($value['fields'][$keyF]['multilanguage']) && $value['fields'][$keyF]['multilanguage'] == 1) {
										$f = $keyF.$_lang['field_suffix'];
										}
									$output = $value1->$f;
								break;
								
								}
								
							/* aggiungi url */
							if (isset($value['fields'][$keyF]['url']) && $value['fields'][$keyF]['url'] == true) {
								if (isset($value['fields'][$keyF]['url item']) && is_array($value['fields'][$keyF]['url item']) && count($value['fields'][$keyF]['url item']) > 0) {
									$u = $Module->getItemUrl($value1,$value['fields'][$keyF]['url item']);
									$output = '<a href="'.$u.'" title="'.ucfirst($_lang['vai alla lista']).'">'.$output.'</a>';								
									} else {
										$output = '<a href="'.URL_SITE_ADMIN.$key.'" title="'.ucfirst($_lang['vai alla lista']).'">'.$output.'</a>';										
										}							
								}							
							$value1->$keyF = $output;							
							}							
						}
					}				
				$arr1[] = $value1;
				}
			$value['itemdata'] =  $arr1;		
			$value['icon panel'] = (isset($value['icon panel']) ? $value['icon panel'] : 'fa-newspaper-o');
			$arr[] = $value;			
			}		
		
		}
	}
$App->homeTables = $arr;	




			
$App->AddPluginJscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/vendor/raphael/raphael.min.js"></script>';
$App->AddPluginJscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/vendor/morrisjs/morris.min.js"></script>';
$App->AddPluginJscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/data/morris-data.js"></script>';

$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/module.js"></script>';	
?>