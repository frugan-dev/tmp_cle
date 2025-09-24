<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/faq/items.php v.4.5.1. 26/11/2018
*/

if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {	
	case 'moreOrderingItem':
		Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst($_lang['voce']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	case 'lessOrderingItem':
		Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst($_lang['voce']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['item'],$App->id,array('label'=>$_lang['voce'],'attivata'=>$_lang['attivata'],'disattivata'=>$_lang['disattivata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');	
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% cancellata'])).'!';	
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');		
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	
	case 'newItem':				
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->ordering = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;		
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%']);
		$App->viewMethod = 'form';
		$App->methodForm = 'insertItem';
	break;
	
	case 'modifyItem':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['modifica %ITEM%'],$_lang['modulo']);
		$App->viewMethod = 'formMod';
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->viewMethod = 'form';
		$App->methodForm = 'updateItem';	
	break;

	
	case 'insertItem':
		//Sql::setDebugMode(1);
		if ($_POST) {	
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($table,'ordering','') + 1;
			// parsa i post in base ai campi
			//ToolsStrings::dump($_POST);

			Form::parsePostByFields($App->params->fields['item'],$_lang,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem');
			}

			//ToolsStrings::dump($_POST);
			//die();

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if (Core::$resultOp->error > 0) {
				echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem');
			}
			
			//die();

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% inserita']));
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');				
			
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
		}
	break;
	
	case 'updateItem':
		if ($_POST) {
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($table,'ordering','') + 1;	
			// requpero i vecchi dati
			$App->oldItem = new stdClass;
			Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
			$App->oldItem = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }
			Form::parsePostByFields($App->params->fields['item'],$_lang,array());
			if (Core::$resultOp->error == 0) {
				Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% modificata']));
				if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
					ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
				} else {
					ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
				}								
			} else {
				//echo 'errore 1'.$App->id; die();
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
			}
								
		} else {
			echo 'errore 2'; die();
			ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
		}
	break;

	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,Core::$request->action,'page',$App->id);
				
	case 'listItem':
	default:
		$App->item = new stdClass;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('ite.*');
			
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['item']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->ordersType['item']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$field = 'title_'.$_lang['user'];	
				$value->title = $value->$field;	
				$field = 'content_'.$_lang['user'];	
				$value->content = ToolsStrings::getStringFromTotNumberChar($value->$field,array('numchars'=>100,'suffix'=>'...'));
				$arr[] = $value;
			}
		}
		$App->items = $arr;
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);
		
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voci'],$_lang['lista %ITEM%']);
		$App->templateApp = 'listItem.tpl.php';
		
		$App->viewMethod = 'list';	
	break;
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':	
		$App->templateApp = 'formItem.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;
	
	default:
	case 'list':
		$App->templateApp = 'listItems.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js"></script>';
	break;
}	
?>