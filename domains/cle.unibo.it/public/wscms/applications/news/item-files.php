<?php
/* resources/item-files.php v.3.5.4. 09/05/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
if (isset($_POST['id_owner']) && isset($_MY_SESSION_VARS[$App->sessionName]['id_owner']) && $_MY_SESSION_VARS[$App->sessionName]['id_owner'] != $_POST['id_owner']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$_POST['id_owner']);

if (Core::$request->method == 'listIfil' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$App->id);

/* gestione sessione -> id_owner */	
$App->id_owner = ($_MY_SESSION_VARS[$App->sessionName]['id_owner'] ?? 0);

if ($App->id_owner > 0) {
	Sql::initQuery($App->params->tables['item resources owner'],['*'],[$App->id_owner],'active = 1 AND id = ?');
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->ownerData = Sql::getRecord();
	if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
	$field = 'title_'.$_lang['user'];	
	$App->ownerData->title = $App->ownerData->$field;
	}

if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageItem/2/'.urlencode((string) $_lang['Devi creare o attivare almeno una voce!']));
	die();
	}
	
$App->pageSubTitle = Config::$langVars['voce'].': ';

switch(Core::$request->method) {
	case 'moreOrderingIfil':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item resources'],'orderingType'=>$App->params->orderTypes['item resources'],'parent'=>1,'parentField'=>'id_owner','label'=>ucfirst((string) $_lang['file']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');
	break;
	case 'lessOrderingIfil':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['item resources'],'orderingType'=>$App->params->orderTypes['item resources'],'parent'=>1,'parentField'=>'id_owner','label'=>ucfirst((string) $_lang['file']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');
	break;

	case 'activeIfil':
	case 'disactiveIfil':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['item resources'],$App->id,['label'=>Config::$langVars['file'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');
	break;
		
	case 'deleteIfil':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		$App->itemOld = new stdClass;
		Sql::initQuery($App->params->tables['item resources'],['filename','org_filename'],[$App->id],'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		Sql::initQuery($App->params->tables['item resources'],[],[$App->id],'id = ?');
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		if (file_exists($App->params->uploadPaths['item resources'].$App->itemOld->filename)) {			
			@unlink($App->params->uploadPaths['item resources'].$App->itemOld->filename);			
		} 			

		$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) Config::$langVars['file'],(string) Config::$langVars['%ITEM% cancellato'])).'!';	
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');		
	break;
	
	case 'newIfil':	
		$App->item = new stdClass;	
		$App->item->active = 1;
		$App->item->filenameRequired = true;
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Core::$langVars['file'],(string) Core::$langVars['inserisci %ITEM%']);
		$App->methodForm = 'insertIfil';		
		$App->viewMethod = 'form';	
	break;
	
	case 'insertIfil':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }

		//Config::$debugMode = 1;

		$_POST['resource_type'] = 2;
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && intval($_POST['ordering']) == 0)) {
			$_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item resources'],'ordering','id_owner = '.intval($_POST['id_owner']).' AND resource_type = 2') + 1;
		}

		ToolsUpload::setFilenameFormat($globalSettings['file type available']);   	
	   	ToolsUpload::getFilenameFromForm();
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newIfil');
		}	
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		$_POST['size_file'] = ToolsUpload::getFileSize();
		$_POST['size_image'] = ToolsUpload::$imageSize;
		$_POST['extension'] = ToolsUpload::getFileExtension();
		$_POST['type'] = ToolsUpload::getFileType();  

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['item resources'],Config::$langVars,[]);
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newIfil');
		}

		Sql::insertRawlyPost($App->params->fields['item resources'],$App->params->tables['item resources']);
		if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');}

		if ($_POST['filename'] != '') {
			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item resources'].$_POST['filename']) or die('Errore caricamento file');
		}
	
		$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) Config::$langVars['file'],(string) Config::$langVars['%ITEM% inserito'])).'!';
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');	
	break;

	case 'modifyIfil':	
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		$App->item = new stdClass;	
		Sql::initQuery($App->params->tables['item resources'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Core::$langVars['file'],(string) Core::$langVars['modifica %ITEM%']);
		$App->methodForm = 'updateIfil';
		$App->viewMethod = 'form';	
	break;

	case 'updateIfil':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }

		$_POST['resource_type'] = 2;
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item resources'],'ordering','id_owner = '.intval($_POST['id_owner']).' AND resource_type = 2') + 1;

		$App->itemOld = new stdClass;
	   	Sql::initQuery($App->params->tables['item resources'],['filename','org_filename'],[$App->id],'id = ?');	
	   	$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }
		   
		ToolsUpload::setFilenameFormat($globalSettings['file type available']); 	
		ToolsUpload::getFilenameFromForm($App->id);
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyIfil/'.$App->id);
		}
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		$_POST['size_file'] = ToolsUpload::getFileSize();
		$_POST['size_image'] = ToolsUpload::$imageSize;
		$_POST['extension'] = ToolsUpload::getFileExtension();
		$_POST['type'] = ToolsUpload::getFileType();  
		   
		$uploadFilename = $_POST['filename'];	   	
		// imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)
		if($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
		if($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename; 

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['item resources'],Config::$langVars,[]);
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyIfil/'.$App->id);
		}

		Sql::updateRawlyPost($App->params->fields['item resources'],$App->params->tables['item resources'],'id',$App->id);
		if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE_ADMIN.'error/db');}
		
		if ($uploadFilename != '') {
		   move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item resources'].$uploadFilename) or die('Errore caricamento file');   			
			if (file_exists($App->params->uploadPaths['item resources'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['item resources'].$App->itemOld->filename);			
			}	   			
		}

		$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) Config::$langVars['file'],(string) Config::$langVars['%ITEM% modificato'])).'!';
		if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyIfil/'.$App->id);
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listIfil');
		}
	break;
	
	case 'pageIfil':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'downloadIfil':
		if ($App->id > 0) {	
			$renderTpl = false;	
			$opt = [
				'fileFieldName'							=> 'filename',
				'fileOrgFieldName'						=> 'org_filename',
				'fieldFolderName'						=> '',
				'folderName'							=> '',
				'table'									=> $App->params->tables['item resources'],
				'valuesClause'							=> [$App->id],
				'whereClause'							=> 'id = ?'
			];	

			ToolsDownload::downloadFileFromDB2($App->params->uploadPaths['item resources'],$opt);
		}
		die();
	break;
	
	
	case 'listIfil':
	default:	
		//Config::$debugMode = 1;
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['*'];
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = 'resource_type = 2';
		$and = ' AND ';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item resources'],'');
		}	
		if ($App->id_owner > 0) {
			$clause .= $and."id_owner = ?";
			$qryFieldsValues[] = $App->id_owner;
			$and = ' AND ';
		}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
		}
		Sql::initQuery($App->params->tables['item resources'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->orderTypes['item resources']);

		$App->items = [];
		$pdoObject = Sql::getPdoObjRecords();
		while ($row = $pdoObject->fetch()) {
			$field = 'title_'.Config::$langVars['user'];	
			$row->title = $row->$field;
			$App->items[] = $row;		
		}
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,(string) $App->paginationTitle);

		$App->pageSubTitle .= preg_replace('/%ITEM%/',(string) Core::$langVars['file'],(string) Core::$langVars['lista dei %ITEM%']);
		$App->viewMethod = 'list';
	break;		
	}

switch((string)$App->viewMethod){
	
	case 'form':
		$App->templateApp = 'formItemsFiles.html';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItemsFiles.js"></script>';
	break;
	
	case 'list':
	default:
		$App->templateApp = 'listItemsFiles.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItemsFiles.js"></script>';		
	break;
}
?>