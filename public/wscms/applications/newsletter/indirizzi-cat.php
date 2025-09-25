<?php
/* wscms/newsletter/indirizzi-cat.php v.1.0.0. 20/06/2016 */

if(isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if(isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	case 'activeIndCat':
	case 'disactiveIndCat':
		Sql::manageFieldActive(substr((string) Core::$request->method,0,-6),$App->tableIndCat,$App->id,ucfirst((string) $App->labels['indcat']['item']));
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteIndCat':
		if ($App->id > 0) {
			$delete = true;
			/* controlla se ha indirizzi associati */
			Sql::initQuery($App->tableRifCatInd,['id'],[$App->id],'id_cat = ?');
			$count = Sql::countRecord();
			if($count > 0) {
				Core::$resultOp->error = 2;
				Core::$resultOp->message = 'Errore! La '.$App->labels['indcat']['item'].' ha ancora '.$App->labels['indcat']['sons'].' associat'.$App->labels['indcat']['sonsSex'].'!';
				$delete = false;	
				}
			
			if ($delete == true && Core::$resultOp->error == 0) {
				Sql::initQuery($App->tableIndCat,[],[$App->id],'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					Core::$resultOp->message = ucfirst((string) $App->labels['indcat']['item']).' cancellat'.$App->labels['indcat']['itemSex'].'!';		
					}
				}
			}			
		$App->viewMethod = 'list';
	break;

	case 'newIndCat':			
		$App->pageSubTitle = 'inserisci '.$App->labels['indcat']['item'];
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertIndCat':
		if ($_POST) {			
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['public'])) $_POST['public'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;		
			/* controlla i campi obbligatori */
			Sql::checkRequireFields($App->fieldsIndCat);
			if (Core::$resultOp->error == 0) {
				Sql::stripMagicFields($_POST);
				Sql::insertRawlyPost($App->fieldsIndCat,$App->tableIndCat);
				if(Core::$resultOp->error == 0) {
		   		}
				} else {					
					$App->message = Sql::$message;
					$App->error = 1;
					} 		
			} else {
				Core::$resultOp->error = 1;
				}			
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'inserisci '.$App->labels['indcat']['item'];
			$App->viewMethod = 'formNew';
			} else {
				$App->viewMethod = 'list';
				Core::$resultOp->message = ucfirst((string) $App->labels['indcat']['item']).' inserit'.$App->labels['indcat']['itemSex'].'!';				
				}		
	break;

	case 'modifyIndCat':				
		$App->pageSubTitle = 'modifica '.$App->labels['indcat']['item'];
		$App->viewMethod = 'formMod';	
	break;
	
	case 'updateIndCat':
		if ($_POST) {
			if (!isset($_POST['active'])) $_POST['active'] = 0;
			if (!isset($_POST['public'])) $_POST['public'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTimeIso;	
			/* controlla i campi obbligatori */
			Sql::checkRequireFields($App->fieldsIndCat);
			if(Core::$resultOp->error == 0) {
				Sql::stripMagicFields($_POST);
				Sql::updateRawlyPost($App->fieldsIndCat,$App->tableIndCat,'id',$App->id);
				if(Core::$resultOp->error == 0) {					
					}	
				}	

			} else {					
				Core::$resultOp->error = 1;
				}
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'modifica '.$App->labels['indcat']['item'];
			$App->viewMethod = 'formMod';					
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst((string) $App->labels['indcat']['item']).' modificat'.$App->labels['indcat']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle = 'modifica '.$App->labels['indcat']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['indcat']['item'];
								}
						}				
				}	
	break;

	case 'pageIndCat':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
	break;
	
	case 'messageIndCat':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
		$App->viewMethod = 'list';		
	break;
	
	case 'listIndCat':
		$App->viewMethod = 'list';
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
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsIndCat);
		$App->templateApp = 'formIndCat.tpl.php';
		$App->methodForm = 'insertIndCat';	
	break;
	
	case 'formMod':
		$App->item = new stdClass;
		$App->item->created = Config::$nowDateTimeIso;
		Sql::initQuery($App->tableIndCat,['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->fieldsIndCat);
		$App->templateApp = 'formIndCat.tpl.php';
		$App->methodForm = 'updateIndCat';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['c.*','(SELECT COUNT(i.id_ind) FROM '.$App->tableRifCatInd.' AS i WHERE i.id_cat = c.id) AS numitems'];
		
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsIndCat,'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $sessClause;
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableIndCat.' AS c',$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = 'la lista delle '.$App->labels['indcat']['items'].' indirizzi iscritti alla newsletter';			
		$App->templateApp = 'listIndCat.tpl.php';			
	break;	
	
	default:
	break;
	}	
?>
