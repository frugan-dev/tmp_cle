<?php
/* wscms/newsletter/newsletter.php v.3.1.0. 10/01/2016 */

/* prende i codici newsletter */
Sql::initQuery($App->tableNewCode,array('*'),array(),'active = 1');
$App->newsletter_code = Sql::getRecords();

if(isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if(isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	case 'activeNew':
	case 'disactiveNew':
		Sql::manageFieldActive(substr(Core::$request->method,0,-3),$App->tableNew,$App->id,ucfirst($App->labels['new']['item']),$App->labels['new']['itemSex']);
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteNew':
		if ($App->id > 0) {
			Sql::initQuery($App->tableNew,array(),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error == 0) {
				Core::$resultOp->message = ucfirst($App->labels['new']['item']).' cancellat'.$App->labels['new']['itemSex'].'!';				
				}
			}		
		$App->viewMethod = 'list';
	break;

	case 'newNew':			
		$App->pageSubTitle = 'inserisci '.$App->labels['new']['item'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertNew':
		if ($_POST) {
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;
			if (!isset($_POST['datatimesend'])) $_POST['datatimesend'] = Config::$nowDateTimeIso;
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['sent'])) $_POST['sent'] = 0;

			/* trasforma la data per sql datetime */
	   	if (isset($_POST['datatimeins'])) {				
				$datetime = DateTime::createFromFormat('d/m/Y H:i',$_POST['datatimeins']);
				$errors = DateTime::getLastErrors();
				if (!empty($errors['warning_count']) && !empty($errors['error_count'])) {
					$_POST['datatimeins'] = Config::$nowDateTimeIso;					
					} else {
						$_POST['datatimeins'] = $datetime->format('Y-m-d H:i:s');
						}
				} else {
					$_POST['datatimeins'] = Config::$nowDateTimeIso;
				   }	

		   /* controlla i campi obbligatori */
			Sql::checkRequireFields($App->fieldsNew);
			if (Core::$resultOp->error == 0) {
				Sql::stripMagicFields($_POST);
				Sql::insertRawlyPost($App->fieldsNew,$App->tableNew);
				if (Core::$resultOp->error == 0) {
					
		   		}
				}
			} else {
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'inserisci '.$App->labels['new']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst($App->labels['new']['item']).' inserit'.$App->labels['new']['itemSex'].'!';				
				}		
	break;

	case 'modifyNew':			
		$App->pageSubTitle = 'modifica '.$App->labels['new']['item'];	
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateNew':
		if ($_POST) {
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['sent'])) $_POST['sent'] = 0;

			/* trasforma la data per sql datetime */
	   	if (isset($_POST['datatimeins'])) {				
				$datetime = DateTime::createFromFormat('d/m/Y H:i',$_POST['datatimeins']);
				$errors = DateTime::getLastErrors();
				if (!empty($errors['warning_count']) && !empty($errors['error_count'])) {
					$_POST['datatimeins'] = Config::$nowDateTimeIso;					
					} else {
						$_POST['datatimeins'] = $datetime->format('Y-m-d H:i:s');
						}
				} else {
					$_POST['datatimeins'] = Config::$nowDateTimeIso;
				   }	

			/* controlla i campi obbligatori */
			Sql::checkRequireFields($App->fieldsNew);
			if (Core::$resultOp->error == 0) {
			
				$_POST['content_it'] = ToolsStrings::encodeHtmlContent($_POST['content_it'],array('customtag'=>'uploads\/','customtagvalue'=>'%ABSURLFOLDER%'));
				
				Sql::stripMagicFields($_POST);
				Sql::updateRawlyPost($App->fieldsNew,$App->tableNew,'id',$App->id);
				if (Core::$resultOp->error == 0) {					
					}	
				}
			} else {					
				Core::$resultOp->error = 1;
				}
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'modifica '.$App->labels['new']['item'];
			$App->viewMethod = 'formMod';					
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst($App->labels['new']['item']).' modificat'.$App->labels['new']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle = 'modifica '.$App->labels['new']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['new']['item'];
								}
						}				
				}	
	break;

	case 'pageNew':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'messageNew':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';		
	break;
	
	case 'listNew':
		$App->viewMethod = 'list';
	break;
	
	case 'previewNew':
		$App->item = new stdClass;	
		Sql::initQuery($App->tableNew,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 0) {	
			$App->item->finalOutput = '';
			$file = ADMIN_PATH_UPLOAD_DIR.$App->templatesFolder.$App->item->template;
			$urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;
			$App->item->content_it = ToolsStrings::parseHtmlContent($App->item->content_it,array('customtag'=>'%PATHNEWSLETTER%','customtagvalue'=>UPLOAD_DIR.$App->templatesFolder));
			if (file_exists($file) == true) {
				$App->item->finalOutput = file_get_contents($file);
				$App->item->finalOutput = preg_replace('/{{PATHNEWSLETTER%/',UPLOAD_DIR.$App->templatesFolder,$App->item->finalOutput);	
				$App->item->finalOutput = preg_replace('/{{DATATIMEINS%/',$App->item->datatimeins,$App->item->finalOutput);
				$App->item->finalOutput = preg_replace('/{{TITLE%/', htmlspecialchars($App->item->title_it),$App->item->finalOutput);
				$App->item->finalOutput = preg_replace('/{{CONTENT%/',$App->item->content_it,$App->item->finalOutput);	
				$App->item->finalOutput = preg_replace('/{{URLDELETE%/',$urldelete,$App->item->finalOutput);
				$App->item->finalOutput = preg_replace('/{{URLSITE%/',URL_SITE,$App->item->finalOutput);
		      } else {
		         $App->item->finalOutput = $App->item->content_it;
		         }   
			/* INIZIO LAYOUT */
			echo $App->item->finalOutput;			
			$renderTpl = false;
			$App->viewMethod = 'NULL';	
			} else {
				$App->viewMethod = 'list';	
				}
		die();
	break;
	
	case 'previewNew1':
		$App->item = new stdClass;	
		$templateDefault = 'default-it.html';
		$id_newletter = (isset(Core::$request->params[0]) ? intval(Core::$request->params[0]) : 0);
		if ($id_newletter == 0) {
			/* trova la prima newsletter */
			Sql::initQuery($App->tableNew,array('*'),array(),'');
			$App->item = Sql::getRecord();		
			if (Core::$resultOp->error > 0) die();
			} else {
				Sql::initQuery($App->tableNew,array('*'),array($id_newletter),'id = ?');
				$App->item = Sql::getRecord();		
				}
			
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0)) {
				$App->item->template = $templateDefault;
				$App->item->datatimeins = Config::$nowDateTimeIso;
				}
	
//print_r($App->item);

		Sql::initQuery($App->tableNewCode,array('*'),array($App->id),'id = ?');
		$App->item_code = Sql::getRecord();
		
		if (Core::$resultOp->error == 0) {	
			$App->item->finalOutput = '';
			$file = PATH_UPLOAD_DIR.$App->templatesFolder.$App->item->template;
			$urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;
			$App->item_code->content_it = ToolsStrings::parseHtmlContent($App->item_code->content_it,array('customtag'=>'{{PATHNEWSLETTER%','customtagvalue'=>UPLOAD_DIR.$App->templatesFolder));
			if (file_exists($file) == true) {
				$App->item->finalOutput = file_get_contents($file);
				$App->item->finalOutput = preg_replace('/%PATHNEWSLETTER%/',UPLOAD_DIR.$App->templatesFolder,$App->item->finalOutput);	
				if (isset($App->item->datatimeins)) $App->item->finalOutput = preg_replace('/%DATATIMEINS%/',$App->item->datatimeins,$App->item->finalOutput);
				if (isset($App->item->title_it)) $App->item->finalOutput = preg_replace('/%TITLE%/', htmlspecialchars($App->item->title_it),$App->item->finalOutput);
				if (isset($App->item->content_it)) $App->item->finalOutput = preg_replace('/%CONTENT%/',$App->item_code->content_it,$App->item->finalOutput);	
				$App->item->finalOutput = preg_replace('/%URLDELETE%/',$urldelete,$App->item->finalOutput);
		      } else {
		         $App->item->finalOutput = $App->item_code->content_it;
		         }   
			/* INIZIO LAYOUT */
			echo $App->item->finalOutput;			
			$renderTpl = false;
			$App->viewMethod = 'NULL';	
			} else {
				$App->viewMethod = 'list';	
				}			
	break;


	default;
		$App->viewMethod = 'list';		
	break;	
	}
	
switch((string)$App->viewMethod) {
	case 'formNew':
		$App->item = new stdClass;
		$App->item->created = Config::$nowDateTimeIso;
		$App->item->active = 1;
		$App->item->sent = 0;
		$App->item->datatimeins = Config::$nowDateTimeIso;
		$App->item->datatimesent = Config::$nowDateTimeIso;
		$App->item->templatesAvaiable = $Module->getTemplatesArray($App->templatesFolder);
		$App->item->template = 'default-it.html';
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsNew);		
		$App->templateApp = 'formNewsletter.html';
		$App->methodForm = 'insertNew';	
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->tableNew,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsNew);
		if (!isset($App->item->datatimeins)) $App->item->datatimeins = Config::$nowDateTimeIso;
		$App->item->templatesAvaiable = $Module->getTemplatesArray($App->templatesFolder);
		$App->item->content_it = ToolsStrings::parseHtmlContent($App->item->content_it,array('ulrsite'=>1));
		$App->templateApp = 'formNewsletter.html';
		$App->methodForm = 'updateNew';
	break;

	case 'list':
		$App->item = new stdClass;		
		$App->item->datatimeins =Config::$nowDateTimeIso;
		$App->items = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsNew,'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $sessClause;
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableNew,$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error == 0) $App->items = Sql::getRecords();	
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = 'la lista delle '.$App->labels['new']['items'].' del sito';			
		$App->templateApp = 'listNewsletter.html';
	break;	
	
	default:
	break;
	}	

$datetime = DateTime::createFromFormat('Y-m-d H:i:s',$App->item->datatimeins);
$errors = DateTime::getLastErrors();
if ($errors['error_count'] > 0) { 
	$defaultdateins = date('Y-m-d H:i:s');  	
	} else {
		$defaultdateins = $datetime->format('Y-m-d H:i:s');
		}
	

/* imposta le variabili Savant */
$App->defaultJavascript = "defaultdate = '".$defaultdateins."';";
?>
