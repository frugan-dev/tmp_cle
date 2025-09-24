<?php
/* wscms/slides-home-rev/items.php v.3.5.4. 05/06/2020 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);	
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if (Core::$request->method == 'listItem' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$App->id);

/* gestione sessione -> id_cat */	
if (isset($_POST['id_cat']) && isset($_MY_SESSION_VARS[$App->sessionName]['id_cat']) && $_MY_SESSION_VARS[$App->sessionName]['id_cat'] != $_POST['id_cat']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$_POST['id_cat']);

switch(Core::$request->method) {
	
	case 'moreOrderingItem':
		if ($App->id > 0) {
			Utilities::increaseFieldOrdering($App->id,$_lang,
			[
				'table'=>$App->params->tables['item'],
				'orderingType'=>$App->params->orderTypes['item'],
				'parent'=>0,
				'parentField'=>'',
				'label'=>ucfirst((string) Config::$langVars['voce']).' '.$_lang['spostato']]);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }			
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	case 'lessOrderingItem':
		if ($App->id > 0) {
			Utilities::decreaseFieldOrdering(
				$App->id,
				$_lang,
				[
					'table'=>$App->params->tables['item'],
					'orderingType'=>$App->params->orderTypes['item'],
					'parent'=>0,
					'parentField'=>'',
					'label'=>ucfirst((string) Config::$langVars['voce']).' '.$_lang['spostato']
				]
			);

			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }			
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'activeItem':
	case 'disactiveItem':
		if ($App->id > 0) {
			Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['item'],$App->id,['label'=>Config::$langVars['voce'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']]);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }		
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');	
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	
	case 'deleteItem':		
		if ($App->id > 0) {
			
			Sql::initQuery($App->params->tables['laye'],['id'],[$App->id],'slide_id = ?');
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
			if (Sql::countRecord() > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',(string) $_lang['layers'],(string) $_lang['Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');				
			}	
			
			$App->itemOld = new stdClass;
			Sql::initQuery($App->params->tables['item'],['filename'],[$App->id],'id = ?');
			$App->itemOld = Sql::getRecord();	
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
			
			Sql::initQuery($App->params->tables['item'],['id'],[$App->id],'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
			
			if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {
				@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
			}
							
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['%ITEM% cancellato'])).'!';	
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');		
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	
	case 'newItem':
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertItem':

		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }	

		if (!isset($_POST['active'])) $_POST['active'] = 0;
		if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;
		if (!isset($_POST['ordering'])) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;   
		ToolsUpload::setFilenameFormat($globalSettings['image type available']);
		ToolsUpload::getFilenameFromForm();
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem/'.$App->id);
		}
		
		Form::parsePostByFields($App->params->fields['item'],Config::$langVars,[]);
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem/'.$App->id);
		}

		Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		$id_item = Sql::getLastInsertedIdVar();

		// crea unlayer vuoto
		$fields = ['slide_id','ordering','created','active'];
		$fieldsVal = [$id_item,1,Config::$nowDateTimeIso,1];
		Sql::initQuery($App->params->tables['laye'],$fields,$fieldsVal);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) {	die('errore creazione primo layer'); }

		if ($_POST['filename'] != '') {
			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$_POST['filename']) or die('Errore caricamento file');
		}	

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['%ITEM% inserita']));
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'modifyItem':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateItem':
		if ($_POST) {			
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;
			if (!isset($_POST['active'])) $_POST['active'] = 0;			
			if (!isset($_POST['ordering'])) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;

			/* preleva filename vecchio */
			Sql::initQuery($App->params->tables['item'],['filename','org_filename'],[$App->id],'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

			/* preleva il filename dal form */	  
			ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
			ToolsUpload::getFilenameFromForm();	  
			if (Core::$resultOp->error > 0) { 
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			 	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem');
			}

			$_POST['filename'] = ToolsUpload::getFilenameMd5();
			$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 
			$uploadFilename = $_POST['filename'];
			/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
			if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
			if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;
			// opzione cancella immagine
			if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
				if (file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
				 	@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);	
			 	}	
				$_POST['filename'] = '';
				$_POST['org_filename'] = ''; 	
			}	
			
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['item'],$_lang,[]);
			if (Core::$resultOp->error > 0) { 
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
			}

			Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

			if ($uploadFilename != '') {
				move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$uploadFilename) or die('Errore caricamento file');   			
				/* cancella l'immagine vecchia */
				if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
					@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
					}	   			
			}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['%ITEM% modificata']));
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyItem/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
			}								

		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.'error/404');
		}
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
		$App->item->created = Config::$nowDateTimeIso;	
		$App->item->active = 1;
		$App->item->hide_image = 1;
		$App->item->ordering = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','') + 1;		
		$App->item->filenameRequired = true;
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'insertItem';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'updateItem';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->item = new stdClass;	
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);				
		$qryFields = [];
		$qryFields[] = 'ite.*';	
		
		$qryFields[] = "(SELECT COUNT(lay.id) FROM ".$App->params->tables['laye']." AS lay WHERE lay.slide_id = ite.id) AS layers";

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
		Sql::setOrder('ordering '.$App->params->orderTypes['item']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$arr[] = $value;
				}
			}
		//ToolsStrings::dump($App->items);

		$App->items = $arr;
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['voci'],(string) Config::$langVars['lista %ITEM%']);
		$App->templateApp = 'listItem.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItem.js"></script>';
	break;	
	
	default:
	break;
	}	
?>