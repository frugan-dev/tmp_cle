<?php
/* wscms/newsletter/indirizzisos.php v.3.1.0. 09/01/2017 */

if(isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if(isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

/* GESTIONE CATEGORIE */
$App->id_cat = ($_MY_SESSION_VARS[$App->sessionName]['id_cat'] ?? 0);

if ($App->params->categories == 1) {
	Sql::initQuery($App->tableIndCat,['id','title_it'],[]);
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->item_cats = Sql::getRecords();
	if (Core::$resultOp->error > 0) {echo Core::$resultOp->message; die;}
	if (!is_array($App->item_cats) || (is_array($App->item_cats) && count($App->item_cats) == 0)) {
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/messageIndCat/2/'.urlencode('Devi creare o attivare almeno un'.$App->labels['indsos']['ownerSex'].' '.$App->labels['indsos']['owner'].'!'));
		die();
		}
	}

switch ($Core::$request->method) {
	case 'confirmIndSos':
		Sql::initQuery($App->tableInd,['confirmed','dateconfirmed','active'],[1,$App->nowDateTime,1,$App->id],'id = ?');
		Sql::updateRecord();
		if(Core::$resultOp->error == 0) {
			Core::$resultOp->message = $App->labels['indsos']['item'].' confermat'.$App->labels['indsos']['itemSex'].'!';			
			}	
		$App->viewMethod = 'list';	
	break;
	
	case 'deleteIndSos':
		if ($App->id > 0) {
			Sql::initQuery($App->tableInd,[],[$App->id],'id = ? AND confirmed = 0');
			Sql::deleteRecord();
			if (Core::$resultOp->error == 0) {
				/* cancella i riferimenti cat->ind */
				Sql::initQuery($App->tableRifCatInd,[],[$App->id],'id_ind = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					Core::$resultOp->message = ucfirst((string) $App->labels['indsos']['item']).' cancellat'.$App->labels['indsos']['itemSex'].'!';
					}						
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'modifyIndSos':				
		$App->pageSubTitle = 'modifica '.$App->labels['indsos']['item'];
		$App->viewMethod = 'formMod';
	break;
	
	case 'updateIndSos':
		if ($_POST) {
			if (!isset($_POST['active'])) $_POST['active'] = 0;	
			if (!isset($_POST['created'])) $_POST['created'] = $App->nowDateTime;
			if (!isset($_POST['dateconfirmed'])) $_POST['dateconfirmed'] = $App->nowDateTime;		
		
			if ($App->params->categories == 1 && !isset($_POST['id_cats']) || (isset($_POST['id_cats']) && !is_array($_POST['id_cats']))) {
				Core::$resultOp->error = 1;
				Core::$resultOp->message = 'Devi scegliere almeno un'.$App->labels['indsos']['ownerSex'].' '.$App->labels['indsos']['owner'].'!';	
				} else {
					if ($App->params->categories == 0) $_POST['id_cats'] = ['0'];
					}
			
			if (Core::$resultOp->error == 0) {				
				/* controlla i campi obbligatori */
				Sql::checkRequireFields($App->fieldsInd);
				if (Core::$resultOp->error == 0) {
					Sql::stripMagicFields($_POST);
					Sql::updateRawlyPost($App->fieldsInd,$App->tableInd,'id',$App->id);
					if (Core::$resultOp->error == 0) {		
						/* cancella i vecchi riferimenti */
						Sql::initQuery($App->tableRifCatInd,[],[$App->id],'id_ind = ?');
						Sql::deleteRecord();
						if (Core::$resultOp->error == 0) {										
							if (isset($_POST['id_cats']) && is_array($_POST['id_cats']) && count($_POST['id_cats']) > 0) {
								foreach ($_POST['id_cats'] AS $value) {
									/* salva i riferimenti cat->ind */	
									Sql::initQuery($App->tableRifCatInd,['id_cat','id_ind'],[intval($value),$App->id]);
									Sql::insertRecord();
									}
								}
							}
						}															
					}	
				}
			} else {					
				Core::$resultOp->error = 1;
				}
		if (Core::$resultOp->error > 0) {
			$App->pageSubTitle = 'modifica '.$App->labels['indsos']['item'];
			$App->viewMethod = 'formMod';					
			} else {
				if (isset($_POST['submitForm'])) {	
					$App->viewMethod = 'list';
					Core::$resultOp->message = ucfirst((string) $App->labels['indsos']['item']).' modificat'.$App->labels['indsos']['itemSex'].'!';								
					} else {						
						if (isset($_POST['id'])) {
							$App->id = $_POST['id'];
							$App->pageSubTitle = 'modifica '.$App->labels['indsos']['item'];
							$App->viewMethod = 'formMod';	
							Core::$resultOp->message = "Modifiche applicate!";
							} else {
								$App->viewMethod = 'formNew';	
								$App->pageSubTitle = 'inserisci '.$App->labels['indsos']['item'];
								}
						}				
				}	
	break;
	
	case 'pageIndSos':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';
	break;
	
	case 'listIndSos':
		$App->viewMethod = 'list';
	break;
	
	case 'deleteOldIndSos':
		$datarif = date('Y-m-d', strtotime('-1 months'));
		//$datarif = date('Y-m-d', strtotime('-1 days'));
		Sql::initQuery($App->tableInd,[],[$datarif],'created < ? AND confirmed = 0');
		Sql::deleteRecord();
		if(Core::$resultOp->error == 0) {
			/* cancella i riferimenti cat->ind */
			Sql::initQuery($App->params->tables['rifcatind'],[],[$App->id],'id_ind = ?');
			Sql::deleteRecord();
			if(Core::$resultOp->error == 0) {
				Core::$resultOp->message = "Vecchi ".$App->labels['indsos']['items']." cancellat".$App->labels['indsos']['itemsSex']."!";
				}				
			}
		$App->viewMethod = 'list';
	break;

	case 'messageIndSos':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode((string) Core::$request->params[0]);
		$App->viewMethod = 'list';		
	break;

	default;	
		$App->viewMethod = 'list';	
	break;	
	}
	
switch((string)$App->viewMethod) {
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->tableInd,['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->fieldsInd);
		/* gestione categorie */	
		$App->item->id_cats = '';	
		$cats = [];
		$obj = new stdClass;	
		Sql::initQuery($App->tableRifCatInd,['*'],[$App->id],'id_ind = ?');
		$obj = Sql::getRecords();
		if (isset($obj) && is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {
				if (isset($value->id_cat) && $value->id_cat != '') $cats[] =  $value->id_cat;
				}			
			}
		if (count($cats) > 0) $App->item->id_cats = implode(',',$cats);
		
		$App->templatePage = 'formIndSos.tpl.php';
		$App->methodForm = 'updateIndSos';
	break;

	case 'list':
		$App->items = new stdClass;
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['*'];
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = 'confirmed = 0';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->fieldsInd,'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= ' AND ('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->tableInd,$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('id ASC');
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = 'la lista degli '.$App->labels['indsos']['items'].' del sito';			
		$App->templatePage = 'listIndSos.tpl.php';			
	break;	
	
	default:
	break;
	}	
?>
