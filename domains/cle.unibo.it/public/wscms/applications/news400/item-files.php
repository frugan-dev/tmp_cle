<?php
/* wscms/news/item-files.php v.3.5.4. 03/04/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
if (isset($_POST['id_owner']) && isset($_MY_SESSION_VARS[$App->sessionName]['id_owner']) && $_MY_SESSION_VARS[$App->sessionName]['id_owner'] != $_POST['id_owner']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$_POST['id_owner']);

if (Core::$request->method == 'listIfil' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_owner',$App->id);

/* gestione sessione -> id_owner */	
$App->id_owner = (isset($_MY_SESSION_VARS[$App->sessionName]['id_owner']) ? $_MY_SESSION_VARS[$App->sessionName]['id_owner'] : 0);

if ($App->id_owner > 0) {
	Sql::initQuery($App->params->tables['item'],array('*'),array($App->id_owner),'active = 1 AND id = ?');
	Sql::setOptions(array('fieldTokeyObj'=>'id'));
	$App->ownerData = Sql::getRecord();
	if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
	$field = 'title_'.$_lang['user'];	
	$App->ownerData->title = $App->ownerData->$field;
	}

if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageItem/2/'.urlencode($_lang['Devi creare o attivare almeno una voce!']));
	die();
	}
	
$App->pageSubTitle = $_lang['voce'].': ';

switch(Core::$request->method) {
	case 'moreOrderingIfil':
		$Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['resources'],'orderingType'=>$App->params->ordersType['ifil'],'parent'=>1,'parentField'=>'id_owner','label'=>ucfirst($_lang['file']).' '.$_lang['spostato'],'addclauseparent'=>'resource_type = ?','addclauseparentvalues'=>array(2)));
		$App->viewMethod = 'list';	
	break;
	case 'lessOrderingIfil':
		$Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['resources'],'orderingType'=>$App->params->ordersType['ifil'],'parent'=>1,'parentField'=>'id_owner','label'=>ucfirst($_lang['file']).' '.$_lang['spostato'],'addclauseparent'=>'resource_type = ?','addclauseparentvalues'=>array(2)));
		$App->viewMethod = 'list';		
	break;

	case 'activeIfil':
	case 'disactiveIfil':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['resources'],$App->id,array('label'=>$_lang['file'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
		$App->viewMethod = 'list';		
	break;
		
	case 'deleteIfil':
		if ($App->id > 0) { 
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
			Sql::initQuery($App->params->tables['resources'],array('filename','org_filename'),array($App->id),'id = ?');
		   $App->itemOld = Sql::getRecord();
		   if (Core::$resultOp->error == 0) {
				Sql::initQuery($App->params->tables['resources'],array(),array($App->id),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					/* cancella il file vero e proprio */
					if (file_exists($App->params->uploadPaths['ifil'].$App->itemOld->filename)) {			
						@unlink($App->params->uploadPaths['ifil'].$App->itemOld->filename);			
						} 			
					Core::$resultOp->message = ucfirst($_lang['file cancellato']).'!';		
					}
				}
			}
		$App->viewMethod = 'list';	
	break;
	
	case 'newIfil':			
		$App->pageSubTitle .= $_lang['inserisci file'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertIfil':
	   if ($_POST) {
	   	if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
	   	if (!isset($_POST['active'])) $_POST['active'] = 0;
	   	if (!isset($_POST['resource_type'])) $_POST['resource_type'] = 2;	
	   	if (!isset($_POST['code'])) $_POST['code'] = '';	
	   	if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['resources'],'ordering','id_owner = '.intval($_POST['id_owner']).' AND resource_type = 2') + 1;
	   		   	
	   	/* preleva il filename dal form */	   
	   	ToolsUpload::setFilenameFormat($globalSettings['file type available']);   	
	   	ToolsUpload::getFilenameFromForm();	
	   	if (Core::$resultOp->error == 0) {   	
				$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
	   		$_POST['size_file'] = ToolsUpload::getFileSize();
	   		$_POST['size_image'] = ToolsUpload::getImageSize();
		   	$_POST['extension'] = ToolsUpload::getFileExtension();
		   	$_POST['type'] = ToolsUpload::getFileType();  
	   	
	   		/* controlla i campi obbligatori */
	   		Sql::checkRequireFields($App->params->fields['resources']);
		   	if (Core::$resultOp->error == 0) {	   	 		
		   		Sql::stripMagicFields($_POST);
		   		/* memorizza nel db */
		   		Sql::insertRawlyPost($App->params->fields['resources'],$App->params->tables['resources']);
		   		if (Core::$resultOp->error == 0) {	   	 		
			   	   /* sposto il Ifil */
			   		if ($_POST['filename'] != '') {
			   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['ifil'].$_POST['filename']) or die('Errore caricamento file');
			   			}
			   		}
					}
				}
			} else {	
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle .= $_lang['inserisci file'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst($_lang['file inserito']).'!';			
				}
	break;

	case 'modifyIfil':		
		$App->pageSubTitle .=  $_lang['modifica file'];
		$App->viewMethod = 'formMod';	
	break;

	case 'updateIfil':
		if ($_POST) {
			$App->itemOld = new stdClass;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;
	   	if (!isset($_POST['resource_type'])) $_POST['resource_type'] = 2;	
	   	if (!isset($_POST['code'])) $_POST['code'] = '';	
	   	if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['resources'],'ordering','id_owner = '.intval($_POST['id_owner']).' AND resource_type = 2') + 1;

	   	/* preleva Ifilname vecchio */
	   	Sql::initQuery($App->params->tables['resources'],array('filename','org_filename'),array($App->id),'id = ?');	
	   	$App->itemOld = Sql::getRecord();	   	
	   	if (Core::$resultOp->error == 0) { 
	   		/* preleva il filename dal form */	  
	   		ToolsUpload::setFilenameFormat($globalSettings['file type available']); 	
	   		ToolsUpload::getFilenameFromForm($App->id);
	   		if (Core::$resultOp->error == 0) {	   		  		   	
					$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   		$_POST['org_filename'] = ToolsUpload::getOrgFilename();
		   		$_POST['size_file'] = ToolsUpload::getFileSize();
		   		$_POST['size_image'] = ToolsUpload::getImageSize();
			   	$_POST['extension'] = ToolsUpload::getFileExtension();
			   	$_POST['type'] = ToolsUpload::getFileType();  
					$uploadFilename = $_POST['filename'];	   	
					/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
					if($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
					if($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename; 
					    	
					/* controlla i campi obbligatori */
					Sql::checkRequireFields($App->params->fields['resources']);
					if (Core::$resultOp->error == 0) {
						Sql::stripMagicFields($_POST);
						/* memorizza nel db */
						Sql::updateRawlyPost($App->params->fields['resources'],$App->params->tables['resources'],'id',$App->id);
						if (Core::$resultOp->error == 0) {   	
							if ($uploadFilename != '') {
					   		move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['ifil'].$uploadFilename) or die('Errore caricamento file');   			
					   		/* cancella l'immagine vecchia */
								if (file_exists($App->params->uploadPaths['ifil'].$App->itemOld->filename)) {			
									@unlink($App->params->uploadPaths['ifil'].$App->itemOld->filename);			
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
			$App->pageSubTitle .= $_lang['modifica file'];
			$App->viewMethod = 'formMod';	
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst($_lang['file modificato']).'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle .= $_lang['modifica file'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = $_lang['Modifiche applicate!'];
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle .= $_lang['inserisci file'];
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
			ToolsDownload::downloadFileFromDB($App->params->uploadPaths['ifil'],$App->id,array('table'=>$App->params->tables['resources']));
			die();
			}
		$App->viewMethod = 'list';
	break;
	
	case 'messageIfil':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listIfil':
		$App->viewMethod = 'list';
	break;

	default;		
		$App->viewMethod = 'list';
	break;		
	}

/*	
echo Core::$resultOp->error;
echo Core::$resultOp->type;
*/

switch((string)$App->viewMethod){
	
	case 'formNew':
		$App->item = new stdClass;	
		$App->item->created = $App->nowDateTime;
		$App->item->active = 1;
		$App->item->filenameRequired = true;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['resources']);
		$App->templateApp = 'formIfil.tpl.php';
		$App->methodForm = 'insertIfil';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formIfil.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;	
		Sql::initQuery($App->params->tables['resources'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['resources']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->templateApp = 'formIfil.tpl.php';
		$App->methodForm = 'updateIfil';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formIfil.js"></script>';		
	break;
	
	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = 'resource_type = 2';
		$and = ' AND ';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['resources'],'');
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
		Sql::initQuery($App->params->tables['resources'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->ordersType['ifil']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
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

		$App->pageSubTitle .= $_lang['lista dei files'];
		$App->templateApp = 'listIfil.tpl.php';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listIfil.js"></script>';		
	break;		
	
	default;	
	break;
	}
?>