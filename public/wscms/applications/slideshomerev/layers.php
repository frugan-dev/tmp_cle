<?php
/* wscms/slides-home-rev/layers.php v.3.5.4. 25/06/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if (Core::$request->method == 'listLaye' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'slide_id',$App->id);

/* gestione sessione -> slide_id */	
$App->slide_id = ($_MY_SESSION_VARS[$App->sessionName]['slide_id'] ?? 0);


if ($App->slide_id > 0) {
	Sql::initQuery($App->params->tables['item'],['*'],[$App->slide_id],'id = ?');
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->ownerData = Sql::getRecord();
	if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
}

if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageItem/2/'.urlencode((string) Config::$langVars['Devi creare o attivare almeno una voce!']));
	die();
}
	
$App->pageSubTitle = Config::$langVars['voce'].': ';

/* gestione sessione -> id_cat */	
if (isset($_POST['id_cat']) && isset($_MY_SESSION_VARS[$App->sessionName]['id_cat']) && $_MY_SESSION_VARS[$App->sessionName]['id_cat'] != $_POST['id_cat']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$_POST['id_cat']);

switch(Core::$request->method) {
	case 'moreOrderingLaye':
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['laye'],'orderingType'=>$App->params->orderTypes['laye'],'parent'=>1,'parentField'=>'slide_id','labelItem'=>ucfirst((string) Config::$langVars['layer']).' '.Config::$langVars['spostato']]);
		$App->viewMethod = 'list';	
	break;
	case 'lessOrderingLaye':
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['laye'],'orderingType'=>$App->params->orderTypes['laye'],'parent'=>1,'parentField'=>'slide_id','labelItem'=>ucfirst((string) $Config::$langVars['layer']).' '.Config::$langVars['spostato']]);
		$App->viewMethod = 'list';		
	break;

	case 'activeLaye':
	case 'disactiveLaye':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['laye'],$App->id,['label'=>Config::$langVars['layer'],'attivata'=>Config::$langVars['attivato'],'disattivata'=>$_lang['disattivato']]);
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteLaye':
		if ($App->id > 0) {
			$delete = true;				
			if ($delete == true && Core::$resultOp->error == 0) {					
				$App->itemOld = new stdClass;
				Sql::initQuery($App->params->tables['laye'],['filename'],[$App->id],'id = ?');
			   $App->itemOld = Sql::getRecord();
				if (Core::$resultOp->error == 0) {
					Sql::initQuery($App->params->tables['laye'],['id'],[$App->id],'id = ?');
					Sql::deleteRecord();
					if (Core::$resultOp->error == 0) {
						if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['laye'].$App->itemOld->filename)) {
							@unlink($App->params->uploadPaths['laye'].$App->itemOld->filename);			
							}
						Core::$resultOp->message = ucfirst((string) preg_replace('/%ITEM%/',(string) Config::$langVars['layer'],(string) Config::$langVars['%ITEM% cancellato'])).'!';
						}
					}
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'newLaye':
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['layer'],(string) Config::$langVars['nuovo %ITEM%']);
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertLaye':
		if ($_POST) {		
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['ordering'])) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['laye'],'ordering','slide_id = '.intval($App->slide_id)) + 1;	   	
		   		
	   	if (Core::$resultOp->error == 0) {
		
				/* preleva il filename dal form */	  
				ToolsUpload::setFilenameFormat($globalSettings['image type available']);
		   	ToolsUpload::getFilenameFromForm();
		   	$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();

		   	if (Core::$resultOp->error == 0) {			
					/* parsa i post in base ai campi */

					//ToolsStrings::dump($_POST);
					Form::parsePostByFields($App->params->fields['laye'],$_lang,[]);

					if (Core::$resultOp->error == 0) {					
						Sql::insertRawlyPost($App->params->fields['laye'],$App->params->tables['laye']);
						if (Core::$resultOp->error == 0) {
							/* sposto il file */
				   		if ($_POST['filename'] != '') {
				   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['laye'].$_POST['filename']) or die('Errore caricamento file');
				   			}	
				   		}						
						}
					}
				}
			} else {	
				Core::$resultOp->error = 1;
				}			
		[$id, $App->viewMethod, $App->pageSubTitle, Core::$resultOp->message] = Form::getInsertRecordFromPostResults(0,Core::$resultOp,$_lang
		
		);
	break;

	case 'modifyLaye':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['layer'],(string) $_lang['modifica %ITEM%']);;
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateLaye':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); 	}
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }

		if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
		if (!isset($_POST['active'])) $_POST['active'] = 0;			
		if (!isset($_POST['ordering'])) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['laye'],'ordering','slide_id = '.intval($App->slide_id)) + 1;	
			
		// preleva filename vecchio
		$App->itemOld = new stdClass;
		Sql::initQuery($App->params->tables['laye'],['filename','org_filename'],[$App->id],'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }


		if ($App->userLoggedData->is_root == 1) {
			// preleva il filename dal form 
			ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
			ToolsUpload::getFilenameFromForm();	   			   	
			if (Core::$resultOp->error > 0) { 
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyLaye/'.$App->id);
			}
			$_POST['filename'] = ToolsUpload::getFilenameMd5();
			$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 		   		   	
			$uploadFilename = $_POST['filename'];
			/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
			if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
			if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;
		} else {
			$_POST['filename'] = '';
			$_POST['org_filename'] = ''; 
 			$uploadFilename = '';
		}	
		/* opzione cancella immagine */
		if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
			if (file_exists($App->params->uploadPaths['laye'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['laye'].$App->itemOld->filename);	
			}	
			$_POST['filename'] = '';
			$_POST['org_filename'] = ''; 	
		}	   	    	
			
			
		Form::parsePostByFields($App->params->fields['laye'],$_lang,[]);
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyLaye/'.$App->id);
		}
			
			Sql::updateRawlyPost($App->params->fields['laye'],$App->params->tables['laye'],'id',$App->id);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		if ($uploadFilename != '') {
			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['laye'].$uploadFilename) or die('Errore caricamento file');
			if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['laye'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['laye'].$App->itemOld->filename);			
			}	   			
		}

		$_SESSION['message'] = '0|'.ucfirst((string) preg_replace('/%ITEM%/',(string) Core::$langVars['blocco'],(string) Core::$langVars['%ITEM% modificato'])).'!';
		if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyLaye/'.$App->id);
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listLaye');
		}
	break;
	
	case 'pageLaye':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';	
	break;

	case 'messageLaye':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;

	case 'listLaye':
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
		$App->item->ordering = Sql::getMaxValueOfField($App->params->tables['laye'],'ordering','slide_id = '.intval($App->slide_id)) + 1;		
		$App->item->filenameRequired = true;
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['laye']);
		$App->templateApp = 'formLayer.html';
		$App->methodForm = 'insertLaye';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formLayer.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['laye'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['laye']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		$App->templateApp = 'formLayer.html';
		$App->methodForm = 'updateLaye';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formLayer.js"></script>';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->item = new stdClass;		
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);				
		$qryFields = [];
		$qryFields[] = 'ite.*';		
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['laye'],'');
			}	
			
		if ($App->slide_id > 0) {
			$clause .= "slide_id = ?";
			$qryFieldsValues[] = $App->slide_id;
			$and = ' AND ';
			}		

		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['laye']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ite.ordering '.$App->params->orderTypes['laye']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
							$field = 'content_'.$_lang['user'];
				if (isset($value->$field)) { $value->content = $value->$field; } else { $value->content = ''; }

				$arr[] = $value;
				}
			}
		$App->items = $arr;
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,(string) $App->paginationTitle);

		$App->pageSubTitle =  preg_replace('/%ITEM%/',(string) Config::$langVars['layers'],(string) Config::$langVars['lista degli %ITEM%']);
		$App->templateApp = 'listLayers.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listLayers.js"></script>';
	break;	
	
	default:
	break;
	}	
?>