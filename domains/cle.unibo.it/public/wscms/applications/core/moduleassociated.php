<?php
/* wscms/core/moduleassociated.php v.3.5.4. 17/09/2019 */

Sql::setDebugMode(1);

if (isset(Core::$request->method) && Core::$request->method == 'from') {
	$_SESSION['associatedModule'] = (Core::$request->param ?? '');
	$_SESSION['associatedReturnmethod'] = (Core::$request->params[0] ?? '');
	$_SESSION['associatedRifItem'] = strtolower(substr($_SESSION['associatedReturnmethod'],-4,4));
	$_SESSION['associatedOwnerId'] = (Core::$request->params[1] ?? '');
	$_SESSION['associatedType'] = (isset(Core::$request->params[2]) ? intval(Core::$request->params[2]) : 0);
}
/*
echo '<br>module: '.$_SESSION['associatedModule'];
echo '<br>returnmethod: '.$_SESSION['associatedReturnmethod'];
echo '<br>ownerId: '.$_SESSION['associatedOwnerId'];
echo '<br>type: '.$_SESSION['associatedType'];
*/
if (!isset($_SESSION['associatedModule']) || (isset($_SESSION['associatedModule']) && $_SESSION['associatedModule'] == '')) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); die(); }

include_once(PATH.$App->pathApplicationsCore."class.module.php");
$Module = new Module(DB_TABLE_PREFIX."module_associated");

// carica lingua e configurazione modulo
include_once(PATH.$App->pathApplications.$_SESSION['associatedModule']."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.$_SESSION['associatedModule']."/config.inc.php");

$App->associatedTypefile = '';
if ($_SESSION['associatedType'] == 1) {
	$App->associatedTypefile = $_lang['immagine'];
	$App->associatedTypefiles = $_lang['immagini'];
}
if ($_SESSION['associatedType'] == 2) {
	$App->associatedTypefile = $_lang['file'];
	$App->associatedTypefiles = $_lang['files'];
}


/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = 'Associazioni modulo';
$App->pageSubTitle = 'lista associazioni '.$App->associatedTypefiles;
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.$App->pageTitle.'</li>';
$App->templateApp = Core::$request->action.'.html';
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
$App->coreModule = true;
$App->sessionName = 'config';
//if (!isset($App->ownerData->id) || (isset($App->ownerData->id) && $App->ownerData->id == 0)) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); die(); }

if ($_SESSION['associatedOwnerId'] > 0) {
	Sql::initQuery($App->params->tables['item'],['*'],[$_SESSION['associatedOwnerId']],'active = 1 AND id = ?');
	Sql::setOptions(['fieldTokeyObj'=>'id']);
	$App->ownerData = Sql::getRecord();
	if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'/404'); die; }
	$field = 'title_'.$_lang['user'];	
	$App->ownerData->title = $App->ownerData->$field;
}


switch(Core::$request->method) {
	case 'newItem':
	
		$App->viewMethod = 'form';
	break;
	default;	
		echo '<br>creo lista';	
		$App->items = new stdClass;
		$App->itemsForPage = ($_MY_SESSION_VARS[$App->sessionName]['ifp'] ?? 5);
		$App->page = ($_MY_SESSION_VARS[$App->sessionName]['page'] ?? 1);
		$qryFields = ['*'];
		$qryFieldsValues = [];
		$qryFieldsValuesClause = [];
		$clause = 'resource_type = 1';
		$and = ' AND ';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			[$sessClause, $qryFieldsValuesClause] = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['resources'],'');
		}	
		if ($_SESSION['associatedOwnerId'] > 0) {
			$clause .= $and."id_owner = ?";
			$qryFieldsValues[] = $_SESSION['associatedOwnerId'];
			$and = ' AND ';
		}	
		if ($_SESSION['associatedModule'] > 0) {
			$clause .= $and."module_owner = ?";
			$qryFieldsValues[] = $_SESSION['associatedModule'];
			$and = ' AND ';
		}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery(DB_TABLE_PREFIX.'module_associated',$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('ordering ASC');
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = [];
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
		$App->paginationTitle = preg_replace('/%START%/',(string) $App->pagination->firstPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',(string) $App->pagination->lastPartItem,(string) $App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',(string) $App->pagination->itemsTotal,(string) $App->paginationTitle);

		$App->pageSubTitle .= $App->pageSubTitle = preg_replace('/%ITEM%/',(string) $App->associatedTypefiles,(string) $_lang['lista delle %ITEM%']);;
		$App->viewMethod = 'list';
	break;		
}

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'moduleassociated.html';
	default;	
	case 'list':
		$App->templateApp = 'moduleassociated.html';	
	break;		

}


?>