<?php
/*	wscms/levels/index.php v.3.5.4. 22/01/2020 */
include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

//Core::setDebugMode(1);

$App->params = new stdClass();
$App->params->label = "Livelli utente";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('levels'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj; 

/* variabili ambiente */
$App->sessionName = Core::$request->action;
$App->codeVersion = ' 3.5.4.';

$App->tables = DB_TABLE_PREFIX.'levels';
$Module = new Module(Core::$request->action,$App->tables);
$App->pageTitle = $App->params->label;
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

// prende id dell home
$App->module_home_id = 3;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$App->fields = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'title'=>array('label'=>$_lang['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'modules'									=> array (
		'label'									=> Config::$langVars['moduli'],
		'searchTable'							=> false,
		'type'									=> 'mediumtext',
		'defValue'                              => '',
        'forcedValue'                           => ''
	),
	'active'                                    => array (
        'label'                                 => Config::$langVars['attiva'],
        'required'                              => false,
        'type'                                  => 'int|1',
        'defValue'                              => 1,
        'forcedValue'                           => 1
    )
);

$App->params->tables['ass-item'] = DB_TABLE_PREFIX.'modules_levels_access';

if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

$App->userModules = Config::$modules;

switch(Core::$request->method) {

	case 'active':
	case 'disactive':
		Sql::manageFieldActive(Core::$request->method,$App->tables,$App->id,array('label'=>$_lang['voce'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
		$App->viewMethod = 'list';		
	break;
	
	case 'delete':
		if ($App->id > 0) {
			Sql::initQuery($App->tables,array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if(Core::$resultOp->error == 0) {
				Core::$resultOp->message = ucfirst($_lang['voce cancellata']).'!';
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'new':		
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->modules = array();	
		$App->pageSubTitle = $_lang['inserisci voce'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insert':
		if ($_POST) {
			
			// forzo il modulo home se non è settato
			$_POST['modules_read'][$App->module_home_id] = 1;
			
			// parsa i post in base ai campi
			Form::parsePostByFields($App->fields,Core::$langVars,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem');
			}


			Sql::insertRawlyPost($App->fields,$App->tables);				
			if (Core::$resultOp->error > 0) { die();ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
			
			// prende ultimo id
			$App->id = Sql::getLastInsertedIdVar();
			// asserra i record con lo stesso livello
			Sql::initQuery($App->params->tables['ass-item'],array(),array($App->id),'levels_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
			
			// memorizzo associazioni
			foreach($App->userModules AS $sectionKey=>$sectionModules) {
				foreach($sectionModules AS $module) {					
					$accessread = (isset($_POST['modules_read'][$module->id]) ? $_POST['modules_read'][$module->id] : 0);
					$accesswrite = (isset($_POST['modules_write'][$module->id]) ? $_POST['modules_write'][$module->id] : 0);
					
					Sql::initQuery($App->params->tables['ass-item'],array('modules_id','users_id','levels_id','read_access','write_access'),array($module->id,'0',$App->id,$accessread,$accesswrite),'');
					Sql::insertRecord();
					if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }
				}
			}

						
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Core::$langVars['livello'],Core::$langVars['%ITEM% inserito']));
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');				
			
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
		}
	break;

	case 'modify':		
		$App->level_modules = Permissions::getLevelModulesRights($App->id);
		//print_r($App->level_modules);
		$App->pageSubTitle = $_lang['modifica voce'];
		$App->viewMethod = 'formMod';
	break;
	
	case 'update':
		//Sql::setDebugMode(1);
		$App->itemOld = new stdClass;
		if ($_POST) {
			
			// forzo il modulo home se non è settato
			$_POST['modules_read'][$App->module_home_id] = 1;
						
			// asserra i record con lo stesso livello
			Sql::initQuery($App->params->tables['ass-item'],array(),array($App->id),'levels_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { 
				//ToolsStrings::redirect(URL_SITE.'error/db');
				die('Errore azzeramneto vecchie associazioni'); 
			}	
			
			// memorizzo associazioni
			foreach($App->userModules AS $sectionKey=>$sectionModules) {
				foreach($sectionModules AS $module) {					
					$accessread = (isset($_POST['modules_read'][$module->id]) ? $_POST['modules_read'][$module->id] : 0);
					$accesswrite = (isset($_POST['modules_write'][$module->id]) ? $_POST['modules_write'][$module->id] : 0);
					
					Sql::initQuery($App->params->tables['ass-item'],array('modules_id','users_id','levels_id','read_access','write_access'),array($module->id,'0',$App->id,$accessread,$accesswrite),'');
					Sql::insertRecord();
					if (Core::$resultOp->error > 0) { 
						//ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');
						die('Errore aggiunta nuova associazione'); 
					}
				}
			}
						
			Form::parsePostByFields($App->fields,Core::$langVars,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				//ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
				die('Errore parsing post');
			}
			
			Sql::updateRawlyPost($App->fields,$App->tables,'id',$App->id);
			if (Core::$resultOp->error > 0) { 
				//ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');
				die('errore aggiornamento record livello'); 
			}	
			
			//die('fatto');

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Core::$langVars['livello'],Core::$langVars['%ITEM% modificato']));
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
			}								
								
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
		}
	break;

	case 'page':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';	
	break;

	case 'message':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;

	case 'list':
		$App->viewMethod = 'list';		
	break;

	default;	
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'formNew':
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->modules = array();
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->fields);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'insert';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->tables,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->fields);
		$App->item->modules = explode(',', $App->item->modules);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'update';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;

	case 'list':
		$App->item = new stdClass;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fields,'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tables,$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();

		
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				
				$App->level_modules = Permissions::getLevelModulesRights($value->id);
				$modules = array();
				foreach ($App->level_modules AS $k1=>$v1) {	
					if ($v1->read_access == 1 || $v1->write_access == 1) {
						$modules[] = $k1;   	
    				}
					
				}
				$value->modules = implode(', ',$modules);
				$arr[] = $value;
			}
		}
		$App->items = $arr;
		
		//ToolsStrings::dump($App->items);

		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = $_lang['lista delle voci'];
		$App->templateApp = 'listItems.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js"></script>';
	break;
	
	default:
	break;
	}	

