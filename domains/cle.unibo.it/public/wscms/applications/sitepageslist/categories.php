<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	site-galleries/categories.php v.2.6.3. 12/04/2016
*/
if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
	
switch(Core::$request->method) {
	
	case 'activeCate':
	case 'disactiveCate':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tableCate,$App->id,array('label'=>$_lang['pagina'],'attivata'=>$_lang['attivata'],'disattivata'=>$_lang['disattivata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
		die();
	break;

	case 'moreOrderingCate':
		Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tableCate,'orderingType'=>$App->params->orderingType,'parent'=>0,'parentField'=>'','label'=>ucfirst($_lang['pagina']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
	break;
	case 'lessOrderingCate':
		Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tableCate,'orderingType'=>$App->params->orderingType,'parent'=>0,'parentField'=>'','label'=>ucfirst($_lang['pagina']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listCate');
	break;
		
	case 'deleteCate':
		if ($App->id > 0) {
			$delete = true;
			/* controlla se ha voci associate */
			Sql::initQuery($App->params->tableItem,array('id'),array($App->id),'id_cat = ?');
			$count = Sql::countRecord();
			if($count > 0) {
				Core::$resultOp->error = 2;
				Core::$resultOp->message = 'Errore! La '.$App->labels['cate']['item'].' ha ancora '.$App->labels['cate']['sons'].' associat'.$App->labels['cate']['sonsSex'].'!';
				$delete = false;	
				}
			if ($delete == true && Core::$resultOp->error == 0) {
				/* preleva il titolo_it per cancellare la cartella */
				Sql::initQuery($App->params->tableCate,array('*'),array($App->id),'id = ?');
				$App->itemOld = Sql::getRecord();	
				if (Core::$resultOp->error == 0) {	   		
					Sql::initQuery($App->tableCate,array('*'),array($App->id),'id = ?');
					Sql::deleteRecord();
					if (Core::$resultOp->error == 0) {
						/* cancella la cartella galleria */
		   			if (isset($App->itemOld->folder_name) && $App->itemOld->folder_name != '' && file_exists($App->itemUploadPathDir.$App->itemOld->folder_name)) rmdir($App->itemUploadPathDir.$App->itemOld->folder_name) or die('impossibile cancellare la cartella'.$App->itemUploadPathDir.$App->itemOld->folder_name);
						Core::$resultOp->message = ucfirst($App->labels['cate']['item']).' cancellat'.$App->labels['cate']['itemSex'].'!';
						}
					}					
				}	
			}		
		$App->viewMethod = 'list';	
	break;
	
	case 'newCate':			
		$App->pageSubTitle = 'inserisci '.$App->labels['cate']['item'];
		$App->viewMethod = 'formNew';	
	break;

	case 'insertCate':
		if ($_POST) {
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->tableCate,'ordering','') + 1;		

	   	/* controlla i campi obbligatori */
	   	Sql::checkRequireFields($App->fieldsCate);
	   	if (Core::$resultOp->error == 0) { 	
	   		Sql::stripMagicFields($_POST);
	   		/* memorizza nel db */
	   		Sql::insertRawlyPost($App->fieldsCate,$App->tableCate);
	   		if (Core::$resultOp->error == 0) {
	   			}
				}
			} else {
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'inserisci '.$App->labels['cate']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst($App->labels['cate']['item']).' inserit'.$App->labels['cate']['itemSex'].'!';				
				}		
	break;
	
	case 'modifyCate':			
		$App->pageSubTitle = 'modifica '.$App->labels['cate']['item'];
		$App->viewMethod = 'formMod';	
	break;

	case 'updateCate':
		if ($_POST) {
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->tableCate,'ordering','') + 1;		
					
   		/* controlla i campi obbligatori */
   		Sql::checkRequireFields($App->fieldsCate);
   		if (Core::$resultOp->error == 0) {   	
   			Sql::stripMagicFields($_POST);
   			/* memorizza nel db */
   			Sql::updateRawlyPost($App->fieldsCate,$App->tableCate,'id',$App->id);
   			if (Core::$resultOp->error == 0) {   				
					}
   			}
			} else {					
				Core::$resultOp->error = 1;
				}
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'modifica '.$App->labels['cate']['item'];
			$App->viewMethod = 'formMod';					
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst($App->labels['cate']['item']).' modificat'.$App->labels['cate']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->pageSubTitle = 'modifica '.$App->labels['cate']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['cate']['item'];
								}
						}				
				}
	break;

	case 'pageCate':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'messageCate':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listCate':
		$App->viewMethod = 'list';
	break;

	default;	
		$App->viewMethod = 'list';	
	break;		
	}


switch((string)$App->viewMethod) {
	case 'formNew':
		$App->item = new stdClass;
		$App->item->created = Config::$nowDateTimeIso;		
		$App->item->active = 1;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsCate);
		$App->templateApp = 'formCate.tpl.php';
		$App->methodForm = 'insertCate';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->tableCate,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsCate);
		$App->templateApp = 'formCate.tpl.php';
		$App->methodForm = 'updateCate';	
	break;
		
	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);	
		$qryFields = array('c.*','(SELECT COUNT(i.id) FROM '.$App->tableItem.' AS i WHERE i.id_cat = c.id) AS numitems');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsCate,'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $sessClause;
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableCate.' AS c',$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->orderingType = 'DESC');
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = 'la lista delle '.$App->labels['cate']['items'];				
		$App->templateApp = 'listCate.tpl.php';
	break;	
	
	default;	
	break;
	}
?>