<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/modules/items.php v.4.5.1. 26/11/2018
*/

if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {	
	case 'moreOrderingItem':
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) $_lang['voce']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	case 'lessOrderingItem':
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) $_lang['voce']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['item'],$App->id,['label'=>$_lang['voce'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');	
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],['id'],[$App->id],'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) $_lang['voce'],(string) $_lang['%ITEM% cancellato'])).'!';	
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');		
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	
	case 'newItem':				
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->ordering = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;	
		$App->item->code_menu = '{"name":"%NAME%","icon":"<i class=\"fa fa-cog\" ><\/i>","label":"%LABEL%"}';		
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['voce'],(string) $_lang['inserisci %ITEM%']);
		$App->viewMethod = 'form';
		$App->methodForm = 'insertItem';
	break;
	
	case 'modifyItem':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['modifica %ITEM%'],(string) $_lang['modulo']);
		$App->viewMethod = 'formMod';
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->viewMethod = 'form';
		$App->methodForm = 'updateItem';	
	break;

	
	case 'insertItem':
		Sql::setDebugMode(1);
		if ($_POST) {	
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($table,'ordering','') + 1;
			
			//ToolsStrings::dump($_POST);
			
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],$_lang,[]);

			//ToolsStrings::dump(Core::$resultOp);
			
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
			}

			//ToolsStrings::dump($_POST);


			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
				
				


				$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) $_lang['voce'],(string) $_lang['%ITEM% inserito']));
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');				
			} else {
				
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem');
			}
	
		

		die();
	break;
	
	case 'updateItem':
		if ($_POST) {
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($table,'ordering','') + 1;	
			// requpero i vecchi dati
			$App->oldItem = new stdClass;
			Sql::initQuery($App->params->tables['item'],['*'],[$App->id],'id = ?');
			$App->oldItem = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }
			Form::parsePostByFields($App->params->fields['item'],$_lang,[]);

			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
			}








			if (Core::$resultOp->error == 0) {
				Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
				$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) $_lang['voce'],(string) $_lang['%ITEM% modificato']));
				if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
					ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
				} else {
					ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
				}								
			} else {
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
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['ite.*'];
			
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['item']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('section ASC, ordering '.$App->params->ordersType['item']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				if (!file_exists(PATH.$App->pathApplications.$value->name."/index.php")) {
					$value->installed = '<span style="color:red;font-size:80%;">'.$_lang['Modulo non installato!'].'</span>';
				}
				$arr[] = $value;
			}
		}
		$App->items = $arr;
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,(string) $App->paginationTitle);
		
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['voci'],(string) $_lang['lista %ITEM%']);
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