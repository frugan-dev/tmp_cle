<?php
/*	wscms/help/items.php v.3.5.4. 22/01/2020 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch (Core::$request->method) {
	case 'listItem':
		$App->viewMethod = 'list';		
	break;

	default;	
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
		case 'list':
		$App->items = new stdClass;
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		Sql::initQuery($App->params->tables['item'],$qryFields,$qryFieldsValues,$clause);
		Sql::setOrder('ordering '.$App->params->ordersType['item']);
				if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$field = 'title_'.$_lang['user'];	
				$value->title = $value->$field;
				$field = 'content_'.$_lang['user'];	
				$value->content = $value->$field;
				$arr[] = $value;
				}
			}
		$App->items = $arr;		
		$App->pageSubTitle = '';
		$App->templateApp = 'listItems.html';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js"></script>';
	break;	
	
	default:
	break;
	}	
?>