<?php
/* wscms/news/items.phpv.3.5.4. 10/09/2019 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if(isset($_POST['id_cat'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$_POST['id_cat']);

if (Core::$request->method == 'listItem' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$App->id);

/* gestione sessione -> id_cat */	
$App->id_cat = (isset($_MY_SESSION_VARS[$App->sessionName]['id_cat']) ? $_MY_SESSION_VARS[$App->sessionName]['id_cat'] : 0);


Sql::initQuery($App->params->tables['cate'],array('*'),array(),'active = 1');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
$obj = Sql::getRecords();
if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
/* sistemo i dati */

	$arr = array();
	if (is_array($obj) && is_array($obj) && count($obj) > 0) {
		foreach ($obj AS $value) {	
			$field = 'title_'.$_lang['user'];	
			$value->title = $value->$field;
			$arr[] = $value;
			}
		}
$App->categories = $arr;
if (!is_array($App->categories) || (is_array($App->categories) && count($App->categories) == 0)) {
	ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageCate/2/'.urlencode($_lang['Devi creare o attivare almeno una categoria!']));
	die();
	}

switch(Core::$request->method) {
	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['item'],$App->id,array('label'=>$_lang['voce'],'attivata'=>$_lang['attivata'],'disattivata'=>$_lang['disattivata']));
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			$delete = true;	
			
			/* controlla se ha immagini associate */
			Sql::initQuery($App->params->tables['resources'],array('id'),array($App->id),'id_owner = ? AND resource_type = 1');
			if (Sql::countRecord() > 0) {
				Core::$resultOp->type = 2;
				Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['immagini'],$_lang['Ci sono ancora %ITEM% associate!']));
				$delete = false;	
				}						
			/* controlla se ha files associati */			
			Sql::initQuery($App->params->tables['resources'],array('id'),array($App->id),'id_owner = ? AND resource_type = 2');
			if (Sql::countRecord() > 0) {
				Core::$resultOp->type = 2;
				Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['files'],$_lang['Ci sono ancora %ITEM% associati!']));
				$delete = false;	
				}		
				
			/* controlla se ha immagini gallerie associate */
			Sql::initQuery($App->params->tables['resources'],array('id'),array($App->id),'id_owner = ? AND resource_type = 3');
			if (Sql::countRecord() > 0) {
				Core::$resultOp->type = 2;
				Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['immagini'],$_lang['Ci sono ancora %ITEM% associate!']));	
				$delete = false;	
				}						
			/* controlla se ha video associati */			
			Sql::initQuery($App->params->tables['resources'],array('id'),array($App->id),'id_owner = ? AND resource_type = 4');
			if (Sql::countRecord() > 0) {
				Core::$resultOp->type = 2;
				Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['video'],$_lang['Ci sono ancora %ITEM% associati!']));	
				$delete = false;
				}	
						
			if ($delete == true && Core::$resultOp->error == 0) {					
				$App->itemOld = new stdClass;
				Sql::initQuery($App->params->tables['item'],array('filename'),array($App->id),'id = ?');
			   $App->itemOld = Sql::getRecord();
				if (Core::$resultOp->error == 0) {
					Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
					Sql::deleteRecord();
					if (Core::$resultOp->error == 0) {
						if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {
							@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
							}
						Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% cancellata'])).'!';
						}
					}
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'newItem':
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertItem':
		if ($_POST) {	
			
	   	/* tagsId */					
			if (isset($_POST['id_tags']) && is_array($_POST['id_tags'])) {
				$_POST['id_tags'] = implode(',',$_POST['id_tags']).',';
				} else {
					$_POST['id_tags'] = '';
					}							
			/* end tagsId */

			/* preleva il filename dal form */	  
			ToolsUpload::setFilenameFormat($globalSettings['image type available']);
			ToolsUpload::getFilenameFromForm();
			$_POST['filename'] = ToolsUpload::getFilenameMd5();
			$_POST['org_filename'] = ToolsUpload::getOrgFilename();
			if (Core::$resultOp->error == 0) {   		
				/* parsa i post in base ai campi */
				Form::parsePostByFields($App->params->fields['item'],$_lang,array());
				if (Core::$resultOp->error == 0) {					
					/* se scadenza controllla le date */
					if ($_POST['scadenza'] == 1) {
						DateFormat::checkDataTimeIsoIniEndInterval($_POST['datatimescaini'],$_POST['datatimescaend'],$App->nowDateTime);
						}	
					if (Core::$resultOp->error == 0) {	
						
						Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
						if (Core::$resultOp->error == 0) {
							/* sposto il file */
							if ($_POST['filename'] != '') {
						   			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$_POST['filename']) or die('Errore caricamento file');
								}	
							}
						}
					}
				}
			} else {
				Core::$resultOp->error = 1;
				} 		
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getInsertRecordFromPostResults(0,Core::$resultOp,'',
			array(		
				'label inserted'=>preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% inserita']),
				'label insert'=>preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%'])	
			)
		);
	break;

	case 'modifyItem':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateItem':
		if ($_POST) {
		
			if (!isset($App->itemOld)) $App->itemOld = new stdClass;
					   		
	   	if (Core::$resultOp->error == 0) {
	   		
				/* preleva filename vecchio */
				Sql::initQuery($App->params->tables['item'],array('filename','org_filename'),array($App->id),'id = ?');
				$App->itemOld = Sql::getRecord();
				if (Core::$resultOp->error == 0) {							
					/* preleva il filename dal form */	  
					ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
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
				   		if (file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
								@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);	
								}	
							$_POST['filename'] = '';
				   		$_POST['org_filename'] = ''; 	
				   		}	   				
						/* parsa i post in base ai campi */
						Form::parsePostByFields($App->params->fields['item'],$_lang,array());			
						if (Core::$resultOp->error == 0) {		
							/* se scadenza controllla le date */
							if ($_POST['scadenza'] == 1) {
								DateFormat::checkDataTimeIsoIniEndInterval($_POST['datatimescaini'],$_POST['datatimescaend'],$App->nowDateTime);
								}	
							if (Core::$resultOp->error == 0) {				
								Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
								if(Core::$resultOp->error == 0) {
									if ($uploadFilename != '') {
					   				move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['item'].$uploadFilename) or die('Errore caricamento file');   			
					   				/* cancella l'immagine vecchia */
										if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {			
											@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
											}	   			
						   			}
						   		}
						   	}					
							}						
						}
					}
				
				}		
					
			} else {
				Core::$resultOp->error = 1;
				} 
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getUpdateRecordFromPostResults($App->id,Core::$resultOp,'',
			array(
				'label modified'=>preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% modificata']),
				'label modify'=>preg_replace('/%ITEM%/',$_lang['voce'],$_lang['modifica %ITEM%']),
				'label insert'=>preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%']),
				'label modify applied'=>$_lang['modifiche applicate']
			)
		);	
	break;
	
	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
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


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'formNew':
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->id_cat = 0;
		$App->item->datatimeins = $App->nowDateTime;
		$App->item->datatimescaini = $App->nowDateTime;
		$App->item->datatimescaend = $App->nowDateTime;
		$App->item->created = $App->nowDateTime;
		$App->item->filenameRequired = false;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'insertItem';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		if (!isset($App->item->datatimeins)) $App->item->datatimeins = $App->nowDateTime;
		if (isset($App->item->id_cat)) $App->id_cat = $App->item->id_cat;
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : false);
		$App->templateApp = 'formItem.html';
		$App->methodForm = 'updateItem';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->item = new stdClass;		
		$App->item->datatimeins = $App->nowDateTime;	
		$App->item->datatimescaini = $App->nowDateTime;
		$App->item->datatimescaend = $App->nowDateTime;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array();
		$qryFields[] = 'ite.*';	
		
		$qryFields[] = "(SELECT COUNT(img.id) FROM ".$App->params->tables['resources']." AS img WHERE img.id_owner = ite.id AND resource_type = 1) AS images";
		$qryFields[] = "(SELECT COUNT(fil.id) FROM ".$App->params->tables['resources']." AS fil WHERE fil.id_owner = ite.id AND resource_type = 2) AS files";
		$qryFields[] = "(SELECT COUNT(gal.id) FROM ".$App->params->tables['resources']." AS gal WHERE gal.id_owner = ite.id AND resource_type = 3) AS gallery";		
		$qryFields[] = "(SELECT COUNT(vid.id) FROM ".$App->params->tables['resources']." AS vid WHERE vid.id_owner = ite.id AND resource_type = 4) AS videos";	

		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item'],'');
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
		Sql::initQuery($App->params->tables['item']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('datatimeins '.$App->params->ordersType['item']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$field = 'title_'.$_lang['user'];	
				$value->title = $value->$field;
				
				$date = DateTime::createFromFormat('Y-m-d H:i:s',$value->datatimeins);				
				$value->datatimeinsOutput = (isset($_lang['datatime format']) ? $date->format($_lang['datatime format']) : $date->format('Y-m-d H:i:s'));
				$date = DateTime::createFromFormat('Y-m-d H:i:s',$value->datatimescaini);				
				$value->datatimescainiOutput = (isset($_lang['datatime format']) ? $date->format($_lang['datatime format']) : $date->format('Y-m-d H:i:s'));
				$date = DateTime::createFromFormat('Y-m-d H:i:s',$value->datatimescaend);				
				$value->datatimescaendOutput = (isset($_lang['datatime format']) ? $date->format($_lang['datatime format']) : $date->format('Y-m-d H:i:s'));
				 
				$arr[] = $value;
				}
			}
		$App->items = $arr;
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = $_lang['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);
		
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voci'],$_lang['lista delle %ITEM%']);
		$App->templateApp = 'listItem.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItem.js"></script>';
	break;	
	
	default:
	break;
	}	
?>