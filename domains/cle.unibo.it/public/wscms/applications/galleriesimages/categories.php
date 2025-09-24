<?php
/* wscms/galleriesimages/categories.php v.4.0.0. 06/12/2021 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if (Core::$request->method == 'listCate' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$App->id);

switch(Core::$request->method) {

	case 'activeCate':
	case 'disactiveCate':
		if ($App->id == 0) {	ToolsStrings::redirect(URL_SITE.'error/404'); }	
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['cate'],$App->id,array('label'=>Config::$langVars['categoria'],'attivata'=>Config::$langVars['attivata'],'disattivata'=>Config::$langVars['disattivata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
	break;
	
	case 'deleteCate':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }	

		//Config::$debugMode = 1;
			
		// controlla se ha voci associate
		Sql::initQuery($App->params->tables['imag'],array('id'),array($App->id),'categories_id = ?');
		$count = Sql::countRecord();
		if ($count > 0) {
			$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['categorie'],Config::$langVars['Ci sono ancora %ITEM% associate!']));
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
		}
	
		// prendo i vecchi dati	
		$App->itemOld = new stdClass;
		Sql::initQuery($App->params->tables['cate'],array('*'),array($App->id),'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		Sql::initQuery($App->params->tables['cate'],array('id'),array($App->id),'id = ?');
		Sql::deleteRecord();

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['categoria'],Config::$langVars['%ITEM% cancellata'])).'!';	
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');

	break;
	
	case 'newCate':
		$App->item = new stdClass;
		$App->item->active = 1;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['categoria'],Config::$langVars['inserisci %ITEM%']);
		$App->methodForm = 'insertCate';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertCate':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['cate'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newCate');
		}

		Sql::insertRawlyPost($App->params->fields['cate'],$App->params->tables['cate']);
		if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['categoria'],Config::$langVars['%ITEM% inserita'])).'!';
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');

	break;

	case 'modifyCate':	
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }	
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['cate'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,Config::$DatabaseTablesFields['galleriesimages categories']);
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['categoria'],Config::$langVars['modifica %ITEM%']);
		$App->methodForm = 'updateCate';	
		$App->viewMethod = 'form';

	break;
	
	case 'updateCate':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }	
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }


		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['cate'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newCate');
		}
		
		Sql::updateRawlyPost($App->params->fields['cate'],$App->params->tables['cate'],'id',$App->id);
		if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['categoria'],Config::$langVars['%ITEM% modificata'])).'!';
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');		
	
	break;
	
	case 'pageCate':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
	break;


	case 'listCate':
	default;
	
		$App->items = new stdClass;
		$App->item = new stdClass;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array();
		$qryFields[] = "cat.*,(SELECT COUNT(ite.id) FROM ".Config::$DatabaseTables['galleriesimages']." AS ite WHERE ite.categories_id = cat.id) AS items";	
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],Config::$DatabaseTablesFields['galleriesimages categories'],'');
		}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
		}
		Sql::initQuery(Config::$DatabaseTables['galleriesimages categories']." AS cat",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('title_it '.$App->params->orderTypes['cate']);

		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$field = 'title_'.$_lang['user'];	
				$value->title = $value->$field;
				$arr[] = $value;
			}
		}
		$App->items = $arr;
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['categorie'],Config::$langVars['lista delle %ITEM%']);
		$App->viewMethod = 'list';	
	break;	
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formCategories.html';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'. Core::$request->action.'/templates/'.$App->templateUser.'/js/formCategories.js"></script>';
	break;
	
	case 'list':
	default:
		$App->templateApp = 'listCategories.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'applications/'. Core::$request->action.'/templates/'.$App->templateUser.'/js/listCategories.js"></script>';
	break;
	}	
?>