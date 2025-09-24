<?php
/*	framework siti html-PHP-Mysql	copyright 2011 Roberto Mantovani	http://www.robertomantovani.vr;it	email: me@robertomantovani.vr.it	news/files.php v.2.6.3. 11/04/2016
*/
if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
if (isset($_POST['id_owner'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$_POST['id_owner']);

if (Core::$request->method == 'listIfil' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$App->id);

/* gestione sessione -> id_owner */	
$App->id_owner = ($_MY_SESSION_VARS[$App->sessionName]['id_owner'] ?? 0);

if ($App->id_owner > 0) {
	Sql::initQuery($App->tableItem,['*'],[$App->id_owner],'active = 1 AND id = ?');
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->ownerData = Sql::getRecord();
	}
if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageItem/2/'.urlencode('Devi creare o attivare almeno un'.$App->labels['ifil']['ownerSex'].' '.$App->labels['ifil']['owner'].'!'));
	die();
	}

switch(Core::$request->method) {
	case 'activeIfil':
	case 'disactiveIfil':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->tableIfil,$App->id,ucfirst((string) $App->labels['ifil']['item']));
		$App->viewMethod = 'list';		
	break;
		
	case 'deleteIfil':
		if ($App->id > 0) { 
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			Sql::initQuery($App->tableIfil,['filename','org_filename'],[$App->id],'id = ?');
		   $App->itemOld = Sql::getRecord();
		   if (Core::$resultOp->error == 0) {
				Sql::initQuery($App->tableIfil,[],[$App->id],'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					/* cancella il file vero e proprio */
					if (file_exists($App->ifilUploadPathDir.$App->itemOld->filename)) {			
						@unlink($App->ifilUploadPathDir.$App->itemOld->filename);			
						} 			
					Core::$resultOp->message = ucfirst((string) $App->labels['ifil']['item']).' cancellat'.$App->labels['ifil']['itemSex'].'!';		
					}
				}
			}
		$App->viewMethod = 'list';	
	break;
	
	case 'newIfil':			
		$App->pageSubTitle = 'inserisci '.$App->labels['ifil']['item'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertIfil':
	   if ($_POST) {
	   	if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
	   	if (!isset($_POST['active'])) $_POST['active'] = 0;	   		   	
	   	/* preleva il filename dal form */	   	
	   	ToolsUpload::getFilenameFromForm();	   	
			$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
	   	$_POST['size'] = ToolsUpload::getFileSize();
	   	$_POST['extension'] = ToolsUpload::getFileExtension();
	   	$_POST['type'] = ToolsUpload::getFileType();		   	  	
	   	/* controlla i campi obbligatori */
	   	Sql::checkRequireFields($App->fieldsIfil);
	   	if (Core::$resultOp->error == 0) {	   	 		
	   		Sql::stripMagicFields($_POST);
	   		/* memorizza nel db */
	   		Sql::insertRawlyPost($App->fieldsIfil,$App->tableIfil);
	   		if (Core::$resultOp->error == 0) {	   	 		
		   	   /* sposto il Ifil */
		   		if ($_POST['filename'] != '') {
		   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->ifilUploadPathDir.$_POST['filename']) or die('Errore caricamento file');
		   			}
		   		}
				}	
			} else {	
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'inserisci '.$App->labels['ifil']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst((string) $App->labels['ifil']['item']).' inserit'.$App->labels['ifil']['itemSex'].'!';				
				}
	break;

	case 'modifyIfil':		
		$App->pageSubTitle = 'modifica '.$App->labels['ifil']['item'];
		$App->viewMethod = 'formMod';	
	break;

	case 'updateIfil':
		if ($_POST) {
			$App->itemOld = new stdClass;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;	
	   	/* preleva Ifilname vecchio */
	   	Sql::initQuery($App->tableIfil,['filename','org_filename'],[$App->id],'id = ?');	
	   	$App->itemOld = Sql::getRecord();	   	
	   	if (Core::$resultOp->error == 0) { 	  		   	
	   		/* preleva il filename dal form */	   	
	   		ToolsUpload::getFilenameFromForm($App->id);	   		
				$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		   	$_POST['size'] = ToolsUpload::getFileSize();
		   	$_POST['extension'] = ToolsUpload::getFileExtension();
		   	$_POST['type'] = ToolsUpload::getFileType();
	   		$uploadFilename = $_POST['filename'];	   	
				/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
				if($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
				if($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;     	
				/* controlla i campi obbligatori */
				Sql::checkRequireFields($App->fieldsIfil);
				if (Core::$resultOp->error == 0) {
					Sql::stripMagicFields($_POST);
					/* memorizza nel db */
					Sql::updateRawlyPost($App->fieldsIfil,$App->tableIfil,'id',$App->id);
					if (Core::$resultOp->error == 0) {   	
						if ($uploadFilename != '') {
				   		move_uploaded_file(ToolsUpload::getTempFilename(),$App->ifilUploadPathDir.$uploadFilename) or die('Errore caricamento file');   			
				   		/* cancella l'immagine vecchia */
							if (file_exists($App->ifilUploadPathDir.$App->itemOld->filename)) {			
								@unlink($App->ifilUploadPathDir.$App->itemOld->filename);			
								}	   			
					   	}	
		   			}	
					}
				}				
			} else {					
				Core::$resultOp->error = 1;
				}		
		if (Core::$resultOp->error == 1) {
			$App->pageSubTitle = 'modifica '.$App->labels['ifil']['item'];
			$App->viewMethod = 'formMod';	
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst((string) $App->labels['ifil']['item']).' modificat'.$App->labels['ifil']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle = 'modifica '.$App->labels['ifil']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['ifil']['item'];
								}
						}				
				}		
	break;
	
	case 'pageIfil':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'downloadIfil':
		if($App->id > 0) {	
			$renderTpl = false;		
			ToolsUpload::downloadFileFromDB($App->ifilUploadPathDir,$App->tableIfil,$App->id,'filename','org_filename','','');	
			die();
			}
		$App->viewMethod = 'list';
	break;
	
	case 'messageIfil':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listIfil':
		$App->viewMethod = 'list';
	break;

	default;		
		$App->viewMethod = 'list';
	break;		
	}

switch((string)$App->viewMethod){
	
	case 'formNew':
		$App->item = new stdClass;	
		$App->item->created = $App->nowDateTime;
		$App->item->active = 1;
		$App->item->filenameRequired = true;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsIfil);
		$App->templatePage = 'formIfil.tpl.php';
		$App->methodForm = 'insertIfil';
	break;
	
	case 'formMod':
		$App->item = new stdClass;	
		Sql::initQuery($App->tableIfil,['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsIfil);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->templatePage = 'formIfil.tpl.php';
		$App->methodForm = 'updateIfil';
	break;
	
	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['*'];
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsIfil,'');
			}	
		if ($App->id_owner > 0) {
			$clause .= "id_owner = ?";
			$qryFieldsValues[] = $App->id_owner;
			$and = ' AND ';
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableIfil,$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);	
		$App->pageSubTitle = 'lista dei '.$App->labels['ifil']['items'].' presenti nell'.$App->labels['ifil']['ownerSex'].' '.$App->labels['ifil']['owner'];				
		$App->templatePage = 'listIfil.tpl.php';			
	break;		
	
	default;	
	break;
	}
?>