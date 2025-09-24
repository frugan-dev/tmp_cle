<?php
/* wscms/galleriesimages/items.php v.4.0.0. 06/12/2021 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) {
	$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
}
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) {
	$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
}

if (Core::$request->method == 'listImag' && $App->id > 0) {
	$_SESSION[$App->sessionName]['categories_id'] = intval($App->id);
}

/* gestione sessione -> id_cat */	
if (isset($_POST['categories_id']) && $_SESSION[$App->sessionName]['categories_id'] != $_POST['categories_id']) {
	$_SESSION[$App->sessionName]['categories_id'] = intval($_POST['categories_id']);
}
$App->categories_id = $_SESSION[$App->sessionName]['categories_id'];

$App->tags = new stdClass;	
Sql::initQuery($App->params->tables['tags'],array('*'),array(),'');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('title_it ASC');
$obj = Sql::getRecords();
if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
$arr = array();
if (isset($obj) && is_array($obj) && count($obj) > 0) {
	foreach ($obj AS $key=>$value) {	
		$field = 'title_'.$_lang['user'];	
		$value->title = $value->$field;
		$arr[$key] = $value;
		}
	}
$App->tags = $arr;


$App->categoriesData = new stdClass;	
Sql::initQuery($App->params->tables['cate'],array('*'),array(),'');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('title_it ASC');
$obj = Sql::getRecords();
if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
$arr = array();
if (isset($obj) && is_array($obj) && count($obj) > 0) {
	foreach ($obj AS $key=>$value) {	
		$field = 'title_'.$_lang['user'];	
		$value->title = $value->$field;
		$arr[$key] = $value;
	}
}
$App->categoriesData = $arr;


switch(Core::$request->method) {
	
	case 'moreOrderingImag':
		Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['imag'],'orderingType'=>$App->params->orderTypes['imag'],'parent'=>1,'parentField'=>'categories_id','label'=>ucfirst(Config::$langVars['immagine']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	case 'lessOrderingImag':
		Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['imag'],'orderingType'=>$App->params->orderTypes['imag'],'parent'=>1,'parentField'=>'categories_id','label'=>ucfirst(Config::$langVars['immagine']).' '.$_lang['spostata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'activeImag':
	case 'disactiveImag':
		if ($App->id == 0) {	ToolsStrings::redirect(URL_SITE.'error/404'); }	
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['imag'],$App->id,array('label'=>Config::$langVars['immagine'],'attivata'=>Config::$langVars['attivata'],'disattivata'=>Config::$langVars['disattivata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listImag');
	break;
	
	case 'deleteImag':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }	
		// prendo i vecchi dati	
		$App->itemOld = new stdClass;
		Sql::initQuery($App->params->tables['imag'],array('*'),array($App->id),'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		Sql::initQuery($App->params->tables['imag'],array('id'),array($App->id),'id = ?');
		Sql::deleteRecord();

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['immagine'],Config::$langVars['%ITEM% cancellata'])).'!';	
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listImag');

	break;
	
	case 'newImag':
		$App->item = new stdClass;	
		$App->item->active = 1;
		$App->item->ordering = 0;	
		$App->itemTags = array();
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['immagine'],Config::$langVars['inserisci %ITEM%']);
		$App->methodForm = 'insertImag';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertImag':
		
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		// gestione automatica dell'ordering de in input = 0
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['imag'],'ordering','categories_id = '.intval($_SESSION[$App->sessionName]['categories_id'])) + 1;
		
		// tagsId			
		if (isset($_POST['id_tags']) && is_array($_POST['id_tags'])) {
			$_POST['id_tags'] = implode(',',$_POST['id_tags']);
		} else {
			$_POST['id_tags'] = '';
		}	
		// end tagsId	

		//preleva il filename dal form
		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
		ToolsUpload::getFilenameFromForm();
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyImag/'.$App->id);
		}
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 		   		   	
		$tempFilename = ToolsUpload::getTempFilename();
   		$uploadFilename = $_POST['filename'];	  
		// imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)
		if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
		if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;	 
		
		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['imag'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newImag');
		}

		Sql::insertRawlyPost($App->params->fields['imag'],$App->params->tables['imag']);
		if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		if ($uploadFilename != '') {
			move_uploaded_file($tempFilename,$App->params->uploadPaths['imag'].$uploadFilename) or die('Errore caricamento file');   			
			if (file_exists($App->params->uploadPaths['imag'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['imag'].$App->itemOld->filename);			
			}	   			
		}

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['immagine'],Config::$langVars['%ITEM% inserita'])).'!';
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listImag');

	break;

	case 'modifyImag':		
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['imag'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		$App->itemTags = array();
		if ($App->item->id_tags != '') $App->itemTags = explode(',',$App->item->id_tags);
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : true);		
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['immagine'],Config::$langVars['modifica %ITEM%']);
		$App->methodForm = 'updateImag';
		$App->viewMethod = 'form';
	break;
	
	case 'updateImag':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }	
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		// preleva dati vecchio
		Sql::initQuery($App->params->tables['imag'],array('*'),array($App->id),'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		// gestione automatica dell'ordering de in input = 0
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['imag'],'ordering','categories_id = '.intval($_SESSION[$App->sessionName]['categories_id'])) + 1;

		//ToolsStrings::dump($_FILES);die();
		//ToolsStrings::dump($globalSettings['image type available']);die();

		// tagsId			
		if (isset($_POST['id_tags']) && is_array($_POST['id_tags'])) {
			$_POST['id_tags'] = implode(',',$_POST['id_tags']);
		} else {
			$_POST['id_tags'] = '';
		}	
		// end tagsId	


		//preleva il filename dal form
		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
		ToolsUpload::getFilenameFromForm();
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyImag/'.$App->id);
		}
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
		$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 		   		   	
		$tempFilename = ToolsUpload::getTempFilename();
   		$uploadFilename = $_POST['filename'];	  
		// imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)
		if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
		if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;	 
		// opzione cancella immagine
		if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
			if (file_exists($App->params->uploadPaths['imag'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['imag'].$App->itemOld->filename);	
			}	
			$_POST['filename'] = '';
			$_POST['org_filename'] = ''; 	
		}

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['imag'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newImag');
		}
		
		Sql::updateRawlyPost($App->params->fields['imag'],$App->params->tables['imag'],'id',$App->id);
		if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		if ($uploadFilename != '') {
			move_uploaded_file($tempFilename,$App->params->uploadPaths['imag'].$uploadFilename) or die('Errore caricamento file');   			
			if (file_exists($App->params->uploadPaths['imag'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['imag'].$App->itemOld->filename);			
			}	   			
		}


		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['immagine'],$_lang['%ITEM% modificata'])).'!';
		if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyImag/'.$App->id);
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listImag');
		}							
	break;
	
	case 'pageImag':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listImag');
	break;

	case 'listImag':
	default;	
		$App->items = new stdClass;
		$App->item = new stdClass;		
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array();
		$qryFields[] = 'ite.*';		
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';

		
		if ($App->categories_id > 0) {

			$clause = 'categories_id = ?';
			$qryFieldsValues[] = $_SESSION[$App->sessionName]['categories_id'];
			$and = 'AND';
		}
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['imag'],'');
		}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
		}
		Sql::initQuery($App->params->tables['imag']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->orderTypes['imag']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && is_array($obj) && count($obj) > 0) {
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

		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$langVars['immagini'],Config::$langVars['lista delle %ITEM%']);
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formImage.html';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formImage.js"></script>';
	break;

	case 'list':	
	default:
		$App->templateApp = 'listImages.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItem.js"></script>';
	break;	
}	
?>