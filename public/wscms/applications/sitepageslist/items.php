<?php
/* wscms/site-pageslist/items.php 08/06/2016 */

if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if(isset($_POST['id_cat'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$_POST['id_cat']);

if (Core::$request->method == 'listItem' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$App->id);

/* gestione sessione -> id_cat */	
$App->id_cat = ($_MY_SESSION_VARS[$App->sessionName]['id_cat'] ?? 0);

if($App->params->categories == 1){
	Sql::initQuery($App->tableCate,['id','title_it'],[]);
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->categoriesData = Sql::getRecords();
	if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
	if (!is_array($App->categoriesData) || (is_array($App->categoriesData) && count($App->categoriesData) == 0)) {
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageCate/2/'.urlencode('Devi creare o attivare almeno un'.$App->labels['item']['ownerSex'].' '.$App->labels['item']['owner'].'!'));
		die();
		}
	}


switch(Core::$request->method) {
	

	case 'moreOrderingItem':
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tableItem,'orderingType'=>$App->params->orderingItemType,'parent'=>1,'parentField'=>'id_cat','label'=>ucfirst((string) $_lang['blocco']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	case 'lessOrderingItem':
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tableItem,'orderingType'=>$App->params->orderingItemType,'parent'=>1,'parentField'=>'id_cat','label'=>ucfirst((string) $_lang['blocco']).' '.$_lang['spostato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tableItem,$App->id,['label'=>$_lang['blocco'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
		die();
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			$delete = true;	
			/* controlla se ha immagini associate */
			if($App->params->item_images == 1) {
				Sql::initQuery($App->tableIimg,['id'],[$App->id],'id_owner = ?');
				$count = Sql::countRecord();
				if($count > 0) {
					Core::$resultOp->error = 2;
					Core::$resultOp->message = 'Errore! Ci sono ancora immagini associate!';
					$delete = false;	
					}
				}				
			/* controlla se ha files associati */
			if($App->params->item_files == 1) {
				Sql::initQuery($App->tableIfil,['id'],[$App->id],'id_owner = ?');
				$count = Sql::countRecord();
				if($count > 0) {
					Core::$resultOp->error = 2;
					Core::$resultOp->message = 'Errore! Ci sono ancora files associati!';
					$delete = false;	
					}
				}			
				
			if ($delete == true && Core::$resultOp->error == 0) {					
				$App->itemOld = new stdClass;
				Sql::initQuery($App->tableItem,['filename'],[$App->id],'id = ?');
			   $App->itemOld = Sql::getRecord();
				if (Core::$resultOp->error == 0) {
					Sql::initQuery($App->tableItem,['id'],[$App->id],'id = ?');
					Sql::deleteRecord();
					if (Core::$resultOp->error == 0) {
						if (isset($App->itemOld->filename) && file_exists($App->itemUploadPathDir.$App->itemOld->filename)) {
							@unlink($App->itemUploadPathDir.$App->itemOld->filename);			
							}
						Core::$resultOp->message = ucfirst((string) $App->labels['item']['item']).' cancellat'.$App->labels['item']['itemSex'].'!';		
						}
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
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->tableItem,'ordering','id_cat = '.intval($_POST['id_cat'])) + 1;			   	   		   	
			/* preleva il filename dal form */	  
			ToolsUpload::setFilenameFormat(['jpg','png']);
	   		ToolsUpload::getFilenameFromForm();
	   		$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
			if (Core::$resultOp->error == 0) {
				if (Core::$resultOp->error == 0) {	   	
						/* controlla i campi obbligatori */
						Sql::checkRequireFields($App->fieldsItem);
						if (Core::$resultOp->error == 0) {
							Sql::stripMagicFields($_POST);
							Sql::insertRawlyPost($App->fieldsItem,$App->tableItem);
							if (Core::$resultOp->error == 0) {
								/* sposto il file */
							if ($_POST['filename'] != '') {
								move_uploaded_file(ToolsUpload::getTempFilename(),$App->itemUploadPathDir.$_POST['filename']) or die('Errore caricamento file');
								}	
							}
							}	
						}
					}
				}		
		if (Core::$resultOp->error == 1) {
			$App->pageSubTitle = 'inserisci '.$App->labels['item']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst((string) $App->labels['item']['item']).' inserit'.$App->labels['item']['itemSex'].'!';				
				}		
	break;

	case 'modifyItem':				
		$App->pageSubTitle = 'modifica '.$App->labels['item']['item'];
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateItem':
		if ($_POST) {
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->tableItem,'ordering','id_cat = '.intval($_POST['id_cat'])) + 1;			   	   		   	
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;			
			/* preleva filename vecchio */
			Sql::initQuery($App->tableItem,['filename','org_filename'],[$App->id],'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error == 0) {							
				/* preleva il filename dal form */	  
				ToolsUpload::setFilenameFormat(['jpg','png']);	
		   		ToolsUpload::getFilenameFromForm();	   			   	
		   	if (Core::$resultOp->error == 0) {	
		   		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   		$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 		   		   	
			   	$uploadFilename = $_POST['filename'];
			   	/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
			   	if($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
			   	if($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;	   	
			   	/* opzione cancella immagine */
			   	if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
			   		if (file_exists($App->itemUploadPathDir.$App->itemOld->filename)) {			
							@unlink($App->itemUploadPathDir.$App->itemOld->filename);	
							}	
						$_POST['filename'] = '';
			   		$_POST['org_filename'] = ''; 	
			   		}	   				
					/* controlla i campi obbligatori */
					Sql::checkRequireFields($App->fieldsItem);
					if (Core::$resultOp->error == 0) {
						Sql::stripMagicFields($_POST);
						Sql::updateRawlyPost($App->fieldsItem,$App->tableItem,'id',$App->id);
						if(Core::$resultOp->error == 0) {
							if ($uploadFilename != '') {
			   				move_uploaded_file(ToolsUpload::getTempFilename(),$App->itemUploadPathDir.$uploadFilename) or die('Errore caricamento file');   			
			   				/* cancella l'immagine vecchia */
								if (isset($App->itemOld->filename) && file_exists($App->itemUploadPathDir.$App->itemOld->filename)) {			
									@unlink($App->itemUploadPathDir.$App->itemOld->filename);			
									}	   			
				   			}
					   	}					
						}						
					}
				}
			} 
		if (Core::$resultOp->error == 1) {
			$App->pageSubTitle = 'modifica '.$App->labels['item']['item'];
			$App->viewMethod = 'formMod';				
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst((string) $App->labels['item']['item']).' modificat'.$App->labels['item']['itemSex'].'!';								
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
		$App->item->ordering = 0;
		$App->item->active = 1;
		$App->item->created = Config::$nowDateTimeIso;
		$App->item->filenameRequired = false;	
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsItem);
		$App->templateApp = 'formItem.tpl.php';
		$App->methodForm = 'insertItem';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->tableItem,['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->fieldsItem);

		if (isset($App->item->id_cat)) $App->id_cat = $App->item->id_cat;
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : false);
		$App->templateApp = 'formItem.tpl.php';
		$App->methodForm = 'updateItem';	
	break;

	case 'list':
		$App->items = new stdClass;
		$App->item = new stdClass;		
				
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);				
		$qryFields = [];
		$qryFields[] = 'ite.*';		
		if ($App->params->item_files == 1) $qryFields[] = "(SELECT COUNT(fil.id) FROM ".$App->tableIfil." AS fil WHERE fil.id_owner = ite.id) AS files";		

		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsItem,'');
			}	
		if ($App->id_cat > 0) {
			$clause .= "id_cat = ?";
			$qryFieldsValues[] = $App->id_cat;
			$and = ' AND ';
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableItem." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->orderingItemType);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = 'lista delle '.$App->labels['item']['items'];
		$App->templateApp = 'listItem.tpl.php';	
	break;	
	
	default:
	break;
	}	
?>