<?php
/* wscms/ecommerce/tags.php v.3.5.4. 05/08/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	case 'moreOrderingTags':
		Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['tags'],'orderingType'=>$App->params->ordersType['tags'],'parent'=>0,'parentField'=>'id_cat','label'=>ucfirst($_lang['tag']).' '.$_lang['spostato']));
		$App->viewMethod = 'list';	
	break;
	case 'lessOrderingTags':
		Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['tags'],'orderingType'=>$App->params->ordersType['tags'],'parent'=>0,'parentField'=>'id_cat','label'=>ucfirst($_lang['tag']).' '.$_lang['spostato']));
		$App->viewMethod = 'list';		
	break;

	case 'activeTags':
	case 'disactiveTags':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['tags'],$App->id,array('label'=>$_lang['tag'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteTags':
		if ($App->id > 0) {
			$delete = true;			
			if ($delete == true && Core::$resultOp->error == 0) {				
				Sql::initQuery($App->params->tables['tags'],array('id'),array($App->id),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {					
					Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['tag'],$_lang['%ITEM% cancellato'])).'!';
					}
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'newTags':
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['tag'],$_lang['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertTags':
		if ($_POST) {	
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
	   	/* gestione automatica dell'ordering de in input = 0 */
	   	if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['tags'],'ordering','') + 1;
				
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['tags'],$_lang,array());
			if (Core::$resultOp->error == 0) {							
				Sql::insertRawlyPost($App->params->fields['tags'],$App->params->tables['tags']);
				if (Core::$resultOp->error == 0) {
					
	   		}								
			}

		} else {
			Core::$resultOp->error = 1;
		}					
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getInsertRecordFromPostResults(0,Core::$resultOp,'',
			array(		
				'label inserted'=>preg_replace('/%ITEM%/',$_lang['tag'],$_lang['%ITEM% inserito']),
				'label insert'=>preg_replace('/%ITEM%/',$_lang['tag'],$_lang['inserisci %ITEM%'])	
			)
		);
	break;

	case 'modifyTags':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['tag'],$_lang['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateTags':
		if ($_POST) {
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;	
			/* gestione automatica dell'ordering de in input = 0 */
	   	if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['tags'],'ordering','') + 1;
	   				
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['tags'],$_lang,array());
			if (Core::$resultOp->error == 0) {							
				Sql::updateRawlyPost($App->params->fields['tags'],$App->params->tables['tags'],'id',$App->id);
				if (Core::$resultOp->error == 0) {
				}					
			}						

		} else {					
			Core::$resultOp->error = 1;
		}
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getUpdateRecordFromPostResults($App->id,Core::$resultOp,'',
			array(
				'label modified'=>preg_replace('/%ITEM%/',$_lang['tag'],$_lang['%ITEM% modificato']),
				'label modify'=>preg_replace('/%ITEM%/',$_lang['tag'],$_lang['modifica %ITEM%']),
				'label insert'=>preg_replace('/%ITEM%/',$_lang['tag'],$_lang['inserisci %ITEM%']),
				'label modify applied'=>$_lang['modifiche applicate']
			)
		);	
	break;
	
	case 'pageTags':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';	
	break;

	case 'messageTags':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;

	case 'listTags':
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
		$App->item->ordering = 0;
		$App->item->created = $App->nowDateTime;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['tags']);
		$App->templateApp = 'formTag.html';
		$App->methodForm = 'insertTags';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formTag.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['tags'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['tags']);
		$App->breadcrumb[] = array('name'=>'formTags');
		$App->templateApp = 'formTag.html';
		$App->methodForm = 'updateTags';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formTag.js"></script>';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->item = new stdClass;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['tags'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['tags'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->ordersType['tags']);
		//Sql::setOrder('datatimeins '.$App->params->ordersType['item']);
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

		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['tags'],$_lang['lista dei %ITEM%']);
		$App->templateApp = 'listTags.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listTags.js"></script>';
	break;	
	
	default:
	break;
	}	
?>