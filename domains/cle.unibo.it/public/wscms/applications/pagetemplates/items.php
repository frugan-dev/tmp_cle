<?php
/* wscms/pagetemplates/items.php v.3.5.4. 22/01/2020 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	
	case 'moreOrderingItem':
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) $_lang['voce']).' '.$_lang['spostato']]);
		$App->viewMethod = 'list';	
	break;
	case 'lessOrderingItem':
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item'],'orderingType'=>$App->params->ordersType['item'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) $_lang['voce']).' '.$_lang['spostato']]);
		$App->viewMethod = 'list';	
	break;

	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['item'],$App->id,['label'=>$_lang['voce'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']]);
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			Sql::initQuery($App->params->tables['item'],['filename'],[$App->id],'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error == 0) {
				Sql::initQuery($App->params->tables['item'],['id'],[$App->id],'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					/* cancella il file vero e proprio */
					if (file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
						@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
						}
					Core::$resultOp->message = ucfirst((string) $_lang['voce cancellata']).'!';			
					}
				}
			}			
		$App->viewMethod = 'list';
	break;
	
	case 'predefinitoItem':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],['predefinito'],['0']);
			Sql::updateRecord();
			if (Core::$resultOp->error == 0) {
				Sql::initQuery($App->params->tables['item'],['predefinito'],['1',$App->id],'id = ?');
				Sql::updateRecord();
				if (Core::$resultOp->error == 0) {
					Core::$resultOp->message = ucfirst((string) $_lang['voce predefinita']).'!';		
					}
				}
			}
		$App->viewMethod = 'list';	
	break;
			
	case 'newItem':
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['inserisci %ITEM%'],(string) $_lang['voce']);
		$App->viewMethod = 'formNew';
	break;
	
	case 'insertItem':
		if ($_POST) {
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;
			/* preleva il filename dal form */	   	
	   	ToolsUpload::getFilenameFromForm();	   	
	   	$_POST['filename'] = ToolsUpload::getFilename();	
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['item'],$_lang,[]);
			if (Core::$resultOp->error == 0) {					
	   		/* sposto il file */
	   		if ($_POST['filename'] != '') {
	   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$_POST['filename']) or die('Errore caricamento file');
	   			}		  	   			
	   		/* memorizza nel db */
	   		Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
	   		if (Core::$resultOp->error == 0) {
			   	}
				}	
			} else {	
				Core::$resultOp->error = 1;
				}			
		[$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getInsertRecordFromPostResults(0,Core::$resultOp,$_lang);
	break;
	
	case 'modifyItem':	
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['modifica %ITEM%'],(string) $_lang['voce']);
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateItem':
		if ($_POST) {
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
	   	Sql::initQuery($App->params->tables['item'],['*'],[$App->id],'id = ?');
			$App->itemOld = Sql::getRecord();
	   	/* preleva il filename dal form */	   	
	   	ToolsUpload::getFilenameFromForm();	   	
	   	$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$uploadFilename = $_POST['filename'];	   	
	   	/* imposta il nomefile precedente se non si è caricata un immagine (serve per far passare il controolo campo immagine presente)*/	   	
	   	if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;	   	
			/* opzione cancella immagine */
	   	if (isset($_POST['deleteFile']) && $_POST['deleteFile'] == 1) {
	   		if (file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
					@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);
					$_POST['filename'] = '';
	   			$_POST['org_filename'] = ''; 		
					}	
	   		}	   	
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['item'],$_lang,[]);
			if (Core::$resultOp->error == 0) {					
	   		if ($uploadFilename != '') {
	   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$uploadFilename) or die('Errore caricamento file');   			
	   			/* cancella l'immagine vecchia */
					if (file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
						@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
						}	   			
		   		}
	   		/* memorizza nel db */
	   		Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
	   		if (Core::$resultOp->error == 0) {		
					}		
				}		
			} else {					
				Core::$resultOp->error = 1;
				}
		[$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getUpdateRecordFromPostResults($App->id,Core::$resultOp,$_lang);	
	break;

	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';	
	break;
	
	case 'messageItem':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listItem':
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
		$App->item->created = $App->nowDateTime;
		$App->item->active = 1;
		$App->item->predefinito = 0;
		$App->item->filenameRequired = false;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'insertItem';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';	
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : false);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'updateItem';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';	
	break;

	default;			
		$App->items = new stdClass;			
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);		
		$qryFields = ['*'];
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
		Sql::initQuery($App->params->tables['item'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);
		Sql::setResultPaged(true);	
		Sql::setOrder('ordering '.$App->params->ordersType['item']);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
	
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,(string) $App->paginationTitle);
		
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js"></script>';
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) $_lang['lista %ITEM%'],(string) $_lang['voci']);
		$App->templateApp = 'listItems.html';			
	break;	
	}
?>
