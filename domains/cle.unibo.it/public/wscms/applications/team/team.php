<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * wscms/homeinfobox/items.php v.4.0.0. 15/12/2021
*/

if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,['page'=>1,'ifp'=>'10','srcTab'=>'']);

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	
	case 'moreOrderingTeam':
		Utilities::increaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['team'],'orderingType'=>$App->params->orderTypes['team'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) Config::$langVars['voce']).' '.Config::$langVars['spostata']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');	
	break;
	case 'lessOrderingTeam':
		Utilities::decreaseFieldOrdering($App->id,$_lang,['table'=>$App->params->tables['team'],'orderingType'=>$App->params->orderTypes['team'],'parent'=>0,'parentField'=>'','label'=>ucfirst((string) Config::$langVars['voce']).' '.Config::$langVars['spostata']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');		
	break;

	case 'activeTeam':
	case 'disactiveTeam':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-4),$App->params->tables['team'],$App->id,['label'=>Config::$langVars['voce'],'attivata'=>Config::$langVars['attivato'],'disattivata'=>Config::$langVars['disattivato']]);
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');	
	break;

	case 'deleteTeam':		
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }						
		$App->itemOld = new stdClass;
		Sql::initQuery($App->params->tables['team'],['filename'],[$App->id],'id = ?');
		$App->itemOld = Sql::getRecord();	
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		Sql::initQuery($App->params->tables['team'],['id'],[$App->id],'id = ?');
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['team'].$App->itemOld->filename)) {
			@unlink($App->params->uploadPaths['team'].$App->itemOld->filename);			
		}
						
		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['%ITEM% cancellato'])).'!';	
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');			
	break;

	
	case 'newTeam':				
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->ordering = 0;
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['inserisci %ITEM%']);
		$App->viewMethod = 'form';
		$App->methodForm = 'insertTeam';
	break;
	
	case 'insertTeam':
		//Config::$debugMode = 1;
		//ToolsStrings::dump($_POST);
		
		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }

		// gestione automatica dell'ordering de in input = 0
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['team'],'ordering','') + 1;


		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	   	
   		ToolsUpload::getFilenameFromForm($App->id);	 
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newTeam');
		}  		
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
	   	$tempFilename = ToolsUpload::getTempFilename();
   		$uploadFilename = $_POST['filename'];	   	
		

		Form::parsePostByFields($App->params->fields['team'],$_lang,[]);
		if (Core::$resultOp->error > 0) {
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newTeam');
		}

		//ToolsStrings::dump($_POST);

		Sql::insertRawlyPost($App->params->fields['team'],$App->params->tables['team']);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }

		if ($uploadFilename != '') {
			move_uploaded_file($tempFilename,$App->params->uploadPaths['team'].$uploadFilename) or die('Errore caricamento file');   						
		}

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) $_lang['%ITEM% inserito']).'!');
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');	
		die();
			
	break;
	
	case 'modifyTeam':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['modifica %ITEM%'],(string) Config::$langVars['voce']);
		$App->viewMethod = 'formMod';
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['team'],['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		$App->viewMethod = 'form';
		$App->methodForm = 'updateTeam';	
	break;
	
	case 'updateTeam':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); 	}
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		
		//Config::$debugMode = 1;
		//ToolsStrings::dump($_POST);

		// requpero i vecchi dati
		$App->oldItem = new stdClass;
		Sql::initQuery($App->params->tables['team'],['*'],[$App->id],'id = ?');
		$App->oldItem = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }	

		// gestione automatica dell'ordering de in input = 0
		if (!isset($_POST['ordering']) || (isset($_POST['ordering']) && $_POST['ordering'] == 0)) $_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['team'],'ordering','') + 1;
		
		/* preleva il filename dal form */	
   		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	   	
   		ToolsUpload::getFilenameFromForm($App->id);	 
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyTeam/'.$App->id);
		}  		
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
	   	$tempFilename = ToolsUpload::getTempFilename();
   		$uploadFilename = $_POST['filename'];	   	
		/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
		if ($_POST['filename'] == '' && $App->oldItem->filename != '') $_POST['filename'] = $App->oldItem->filename;
		if ($_POST['org_filename'] == '' && $App->oldItem->org_filename != '') $_POST['org_filename'] = $App->oldItem->org_filename; 
		/* opzione cancella immagine */
	   	if (isset($_POST['deleteFilename']) && $_POST['deleteFilename'] == 1) {
	   		if (file_exists($App->params->uploadPaths['team'].$App->oldItem->filename)) {			
				@unlink($App->params->uploadPaths['team'].$App->oldItem->filename);	
			}	
			$_POST['filename'] = '';
	   		$_POST['org_filename'] = ''; 	
	   	}	 
		

		Form::parsePostByFields($App->params->fields['team'],Config::$langVars,[]);
		if (Core::$resultOp->error > 0) { 
			echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyTeam/'.$App->id);
		}

		//ToolsStrings::dump($_POST);die();

		Sql::updateRawlyPost($App->params->fields['team'],$App->params->tables['team'],'id',$App->id);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }

		if ($uploadFilename != '') {
			move_uploaded_file($tempFilename,$App->params->uploadPaths['team'].$uploadFilename) or die('Errore caricamento file');   			
			/* cancella l'immagine vecchia */
			 if (file_exists($App->params->uploadPaths['team'].$App->oldItem->filename)) {			
				 @unlink($App->params->uploadPaths['team'].$App->oldItem->filename);			
			}	   			
		}

		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',(string) Config::$langVars['voce'],(string) Config::$langVars['%ITEM% modificato'])).'!';
		if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyTeam/'.$App->id);
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');
		}	
		die();
	break;

	case 'pageTeam':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,Core::$request->action,'page',$App->id);
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listTeam');
	break;

	case 'listTeam':
	default:
		$App->item = new stdClass;						
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);

		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['ite.*'];
			
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['team'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['team']." AS ite",$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering '.$App->params->orderTypes['team']);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				$field = 'role_'.$_lang['user'];	
				$value->role = $value->$field;	
				$field = 'content_'.$_lang['user'];	
				$value->content = ToolsStrings::getStringFromTotNumberChar($value->$field,['numchars'=>100,'suffix'=>'...']);
				$arr[] = $value;
			}
		}
		$App->items = $arr;
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = Config::$langVars['Mostra da %START%  a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,$App->paginationTitle);
		
		$App->pageSubTitle = preg_replace('/%ITEM%/',(string) Config::$langVars['voci'],(string) $_lang['lista %ITEM%']);
		$App->viewMethod = 'list';	
	break;
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':	
		$App->templateApp = 'formTeam.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/formTeam.js"></script>';
	break;
	
	default:
	case 'list':
		$App->templateApp = 'listTeam.html';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications. Core::$request->action.'/templates/'.$App->templateUser.'/js/listTeams.js"></script>';
	break;
}	
?>