<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	site-files/items.php v.2.6.3. 18/04/2016
*/
if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
if (isset($_POST['id_folder'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_folder',$_POST['id_folder']);

if (Core::$request->method == 'listItem' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_folder',$App->id);

/* gestione sessione -> id_cat */	
$App->id_folder = (isset($_MY_SESSION_VARS[$App->sessionName]['id_folder']) ? $_MY_SESSION_VARS[$App->sessionName]['id_folder'] : 0);

//echo 'id folder: '.$App->id_folder;

Sql::initQuery($App->tableFold,array('id','title_it'),array());
Sql::setOptions(array('fieldTokeyObj'=>'id'));
$App->foldersData = Sql::getRecords();
if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
if (!is_array($App->foldersData) || (is_array($App->foldersData) && count($App->foldersData) == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageFold/2/'.urlencode('Devi creare o attivare almeno un'.$App->labels['item']['ownerSex'].' '.$App->labels['item']['owner'].'!'));
	die();
	}

switch(Core::$request->method) {
	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tableItem,$App->id,array('label'=>$_lang['file'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');	
	break;
		
	case 'deleteItem':
		if ($App->id > 0) { 
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			Sql::initQuery($App->tableItem,array('id_folder','filename','folder_name'),array($App->id),'id = ?');
		   $App->itemOld = Sql::getRecord();
		   if (Core::$resultOp->error == 0) {
			   $folder_name = ($App->itemOld->folder_name != '' ? $App->itemOld->folder_name.'/' : ''); 
				Sql::initQuery($App->tableItem,array(),array($App->id),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					/* cancella il file vero e proprio */
					if (file_exists($App->itemUploadPathDir.$folder_name.$App->itemOld->filename)) {			
						@unlink($App->itemUploadPathDir.$folder_name.$App->itemOld->filename);			
						} 			
					Core::$resultOp->message = ucfirst($App->labels['item']['item']).' cancellat'.$App->labels['item']['itemSex'].'!';		
					}
				}
			}
		$App->viewMethod = 'list';	
	break;
	
	case 'newItem':			
		$App->pageSubTitle = 'inserisci '.$App->labels['item']['item'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertItem':
	   if ($_POST) {
	   	$App->itemCat = new stdClass;
	   	if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
	   	if (!isset($_POST['active'])) $_POST['active'] = 0;	   		   	
	   	/* preleva il filename dal form */	   	
	   	ToolsUpload::getFilenameFromForm();	   	
			$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
	   	$_POST['size'] = ToolsUpload::getFileSize();
	   	$_POST['extension'] = ToolsUpload::getFileExtension();
	   	$_POST['type'] = ToolsUpload::getFileType();
	   	/* preleva il nome della cartella scelta */
	   	Sql::initQuery($App->tableFold,array('*'),array($_POST['id_folder']),'id = ?');
	   	$App->itemCat = Sql::getRecord();	
			if (Core::$resultOp->error == 0) {
		   	$_POST['folder_name'] = ($App->itemCat->folder_name != '' ? $App->itemCat->folder_name.'/' : '');	   	
		   	/* controlla i campi obbligatori */
		   	Sql::checkRequireFields($App->fieldsItem);
		   	if (Core::$resultOp->error == 0) {	   	 		
		   		Sql::stripMagicFields($_POST);
		   		/* memorizza nel db */
		   		Sql::insertRawlyPost($App->fieldsItem,$App->tableItem);
		   		if (Core::$resultOp->error == 0) {	   	 		
			   	   /* sposto il Item */
			   		if ($_POST['filename'] != '') {
			   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->itemUploadPathDir.$_POST['folder_name'].$_POST['filename']) or die('Errore caricamento file');
			   			}
			   		}
		   		}
				}	
			} else {	
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'inserisci '.$App->labels['item']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst($App->labels['item']['item']).' inserit'.$App->labels['item']['itemSex'].'!';				
				}
	break;

	case 'modifyItem':			
		$App->pageSubTitle = 'modifica '.$App->labels['item']['item'];
		$App->viewMethod = 'formMod';	
	break;

	case 'updateItem':
		if ($_POST) {
			$App->itemOld = new stdClass;
			$App->item = new stdClass;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;	
	   	/* preleva Itemname vecchio */
	   	Sql::initQuery($App->tableItem,array('filename','org_filename','folder_name'),array($App->id),'id = ?');	
	   	$App->itemOld = Sql::getRecord(); 	
	   	if (Core::$resultOp->error == 0) { 
	   		$oldFolder_name = ($App->itemOld->folder_name != '' ? (string)$App->itemOld->folder_name : '');	   	  		   	
	   		/* preleva il filename dal form */	   	
	   		ToolsUpload::getFilenameFromForm($App->id);	   		
				$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		   	$_POST['size'] = ToolsUpload::getFileSize();
		   	$_POST['extension'] = ToolsUpload::getFileExtension();
		   	$_POST['type'] = ToolsUpload::getFileType();
	   		$uploadFilename = $_POST['filename'];	   	
		   	/* preleva il nuovo foldername dalla categoria scelta */
		   	$_POST['folder_name'] = '';
		   	if (isset($_POST['id_folder'])) {
		   		Sql::initQuery($App->tableFold,array('folder_name'),array($_POST['id_folder']),'id = ?');	
		   		$App->item = Sql::getRecord();
		   		if(Core::$resultOp->error == 0) {  
		   			$_POST['folder_name'] = $App->item->folder_name.'/';
		   			}
		   		}
			   if (Core::$resultOp->error == 0) {	   	
			   	/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
			   	if($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
			   	if($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;     	
			   	/* controlla i campi obbligatori */
			   	Sql::checkRequireFields($App->fieldsItem);
			   	if (Core::$resultOp->error == 0) {
			   		Sql::stripMagicFields($_POST);
			   		/* memorizza nel db */
			   		Sql::updateRawlyPost($App->fieldsItem,$App->tableItem,'id',$App->id);
			   		if (Core::$resultOp->error == 0) {   	
					   	if ($uploadFilename != '') {
				   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->itemUploadPathDir.$_POST['folder_name'].$uploadFilename) or die('Errore caricamento file');   			
				   			/* cancella l'immagine vecchia */
								if (file_exists($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename)) {			
									@unlink($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename);			
									}	   			
					   		}	   	
					   	/* nel caso che si è cambiata SOLO la cartella di destinazione */		   	
					   	if ($uploadFilename == '' && $_POST['folder_name'] != $oldFolder_name && $App->itemOld->filename != '') {
					   		/* copia il file da una cartella all'altra */
					   		if (file_exists($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename)) copy($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename,$App->itemUploadPathDir.$_POST['folder_name'].$App->itemOld->filename) or die('Errore spostamento file');  			
				   			/* cancella l'immagine vecchia */
								if (file_exists($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename)) {			
									@unlink($App->itemUploadPathDir.$oldFolder_name.$App->itemOld->filename);			
									}	   			
					   		}
		   				}
		   			}	
					}
				}				
			} else {					
				Core::$resultOp->error = 1;
				}		
		if (Core::$resultOp->error == 1) {
			$App->pageSubTitle = 'modifica '.$App->labels['item']['item'];
			$App->viewMethod = 'formMod';	
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst($App->labels['item']['item']).' modificat'.$App->labels['item']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle = 'modifica '.$App->labels['item']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['item']['item'];
								}
						}				
				}		
	break;
	
	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'downloadItem':
		if($App->id > 0) {	
			$renderTpl = false;		
			ToolsUpload::downloadFileFromDB($App->itemUploadPathDir,$App->params->tableItem,$App->id,'filename','org_filename','folder_name','');	
			die();
			}
		$App->viewMethod = 'list';
	break;
	
	case 'messageItem':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listItem':
		$App->viewMethod = 'list';
	break;

	default;		
		$App->viewMethod = 'list';
	break;		
	}

switch((string)$App->viewMethod){
	
	case 'formNew':
		$App->id_folder = Categories::checkIfCatExistsInObjectOrGetOne($App->foldersData,$App->id_folder);
		$App->item = new stdClass;
		$App->item->created = Config::$nowDateTimeIso;
		$App->item->active = 1;
		$App->item->filenameRequired = true;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsItem);
		$App->templateApp = 'formItem.tpl.php';
		$App->methodForm = 'insertItem';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		$App->id_folder = Categories::checkIfCatExistsInObjectOrGetOne($App->foldersData,$App->id_folder);
		Sql::initQuery($App->tableItem,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsItem);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->templateApp = 'formItem.tpl.php';
		$App->methodForm = 'updateItem';
	break;
	
	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsItem,'');
			}	
		if ($App->id_folder > 0) {
			$clause .= "id_folder = ?";
			$qryFieldsValues[] = $App->id_folder;
			$and = ' AND ';
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableItem,$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);	
		$App->pageSubTitle = 'lista dei '.$App->labels['item']['items'].' presenti nell'.$App->labels['item']['ownerSex'].' '.$App->labels['item']['owner'];				
		$App->templateApp = 'listItem.tpl.php';			
	break;		
	
	default;	
	break;
	}
?>