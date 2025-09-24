<?php
/* wscms/menu/items.php v.4.0.0. 10/12/2021 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

/* preleva i link moduli (alias) */

switch(Core::$request->method) {
	case 'ajaxGetSectionModuleLinkInfoItem':
		$menutypevars = (isset($_POST['menutypevars']) && $_POST['menutypevars'] != '') ? $_POST['menutypevars'] : '';
		if ($menutypevars != '') {
			if (isset($_lang['menu-type-vars'][$menutypevars]['info'])) echo $_lang['menu-type-vars'][$menutypevars]['info'];
		} 
		die();
	break;

	case 'moreOrderingMenu':
		Utilities::increaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['menu'],'orderingType'=>$App->params->orderTypes['menu'],'parent'=>1,'parentField'=>'parent','label'=>ucfirst($_lang['voce']).' '.$_lang['spostato']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	case 'lessOrderingMenu':
		Utilities::decreaseFieldOrdering($App->id,$_lang,array('table'=>$App->params->tables['menu'],'orderingType'=>$App->params->orderTypes['item'],'parent'=>1,'parentField'=>'parent','label'=>ucfirst($_lang['voce']).' '.$_lang['spostato']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	
	case 'activeMenu':
	case 'disactiveMenu':
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }	
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['menu'],$App->id,array('label'=>Config::$langVars['menu'],'attivata'=>Config::$langVars['attivato'],'disattivata'=>Config::$langVars['disattivato']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;

	case 'deleteMenu':		
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }
		//Config::$debugMode = 1;

		/* controlla se ha figli associati */			
		Sql::initQuery($App->params->tables['menu'],array('id'),array($App->id),'parent = ?');
		if (Sql::countRecord() > 0) {
			$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Core::$langVars['voci'],Core::$langVars['Ci sono ancora %ITEM% associati!']));
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
		}

		Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
		Sql::deleteRecord();
					
		// cancello il file associato
		if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['item'].$App->itemOld->filename)) {
			@unlink($App->params->uploadPaths['item'].$App->itemOld->filename);			
		}
		
		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Core::$langVars['voce'],Core::$langVars['%ITEM% cancellato'])).'!';	
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');
	break;
	
	case 'newItem':
		$App->item = new stdClass();
		$App->item->created = Config::$nowDateTimeIso;
		$App->templateItem = new stdClass();
		$App->subCategories = new stdClass();	
		/* select per parent */
		$opt = array('lang'=>$_lang['user'],'tableCat'=>$App->params->tables['item']);
		Subcategories::$countItems = 0;
		Subcategories::$dbTable = $App->params->tables['item'];
		$App->subCategories = Subcategories::getObjFromSubCategories($opt);
		/* altri campi */
		$App->item->active = 1;
		$App->item->menu = 1;
		$App->item->alias = "";
		$App->item->parent = 0;	
		$App->item->ordering = 0;
		$App->item->filenameRequired = false;
		$App->methodForm = 'insertItem';
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%']);
		$App->viewMethod = 'form';	
	break;
	
	case 'insertItem':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		//Config::$debugMode = 1;

		$App->templateItem = new stdClass;
		
		/* gestione automatica dell'ordering de in input = 0 */
		$_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','parent = '.intval($_POST['parent'])) + 1;
	
		/* imposta alias */
		$_POST['alias'] = $Module->getAlias(0,$_POST['alias'],$_POST['title_'.$_lang['user']]);
		
		/* imposta tipo se presente */
		switch($_POST['type']) {
			case 'module-link':
				if ($_POST['module'] != '') {
					$_POST['url'] = $_POST['module'];
					$_POST['alias'] = $_POST['module'];
				}
			break;
			case 'module-menu':
				if ($_POST['menutypevars'] != '' && isset($_lang['menu-type-vars'][$_POST['menutypevars']])) {
					$_POST['url'] = $_lang['menu-type-vars'][$_POST['menutypevars']]['varreplace'];
					$_POST['alias'] = $_lang['menu-type-vars'][$_POST['menutypevars']]['varreplace'];
				}
			break;
			default:
			break;
		}

		//ToolsStrings::dump($_POST);

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['item'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/newItem');
		}

		//ToolsStrings::dump($_POST);

		Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
		if (Core::$resultOp->error > 0) { die('error insert db'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */	
		
		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['voce'],Config::$langVars['%ITEM% inserito'])).'!';
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');

	break;
	
	case 'modifyMenu':
	case 'modifyItem':
		$App->item = new stdClass();
		$App->subCategories = new stdClass();	
		/* select per parent */
		$opt = array(
			'lang'=>$_lang['user'],
			'tableCat'=>$App->params->tables['item'],
			'hideId'=>1,
			'hideSons'=>1,
			'rifId'=>'id',
			'rifIdValue'=>$App->id
			);
			Subcategories::$countItems = 0;
			Subcategories::$dbTable = $App->params->tables['item'];
		$App->subCategories = Subcategories::getObjFromSubCategories($opt);
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->item->filenameRequired = (isset($App->item->filename) && $App->item->filename != '' ? false : false);
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['modifica %ITEM%'],$_lang['voce']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
	break;
	
	case 'updateItem':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/404'); }

		//Config::$debugMode = 1;
		// requpero i vecchi dati
		$App->oldItem = new stdClass;
		Sql::initQuery($App->params->tables['menu'],array('*'),array($App->id),'id = ?');
		$App->oldItem = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }	
	
		/* gestione automatica dell'ordering de in input = 0 */
		$_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['menu'],'ordering','parent = '.intval($_POST['parent'])) + 1;

		// imposta alias
		$_POST['alias'] = $Module->getAlias($App->id,$_POST['alias'],$_POST['title_'.$_lang['user']]);
			
		// imposta tipo se presente
		switch($_POST['type']) {
			case 'module-link':
				if ($_POST['module'] != '') {
					$_POST['url'] = $_POST['module'];
					$_POST['alias'] = $_POST['module'];
				}
			break;
			case 'module-menu':
				if ($_POST['menutypevars'] != '' && isset($_lang['menu-type-vars'][$_POST['menutypevars']])) {
					$_POST['url'] = $_lang['menu-type-vars'][$_POST['menutypevars']]['varreplace'];
					$_POST['alias'] = $_lang['menu-type-vars'][$_POST['menutypevars']]['varreplace'];
				}
			break;
			default:
			break;
		}
				
		// se cambia parent aggiorna l'ordering
	   	if ($_POST['parent'] != $App->oldItem->parent) {
			$_POST['ordering'] = Sql::getMaxValueOfField($App->params->tables['item'],'ordering','parent = '.intval($_POST['parent'])) + 1;  
		} 	

		Form::parsePostByFields($App->params->fields['menu'],Config::$langVars,array());
		if (Core::$resultOp->error > 0) { 
			echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyMenu/'.$App->id);
		}

		Sql::updateRawlyPost($App->params->fields['menu'],$App->params->tables['menu'],'id',$App->id);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }

   		
		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$langVars['voce'],Config::$langVars['%ITEM% modificato'])).'!';
		if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyMenu/'.$App->id);
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listMenu');
		}	
		die();
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
	default;	
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 10);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		Sql::setItemsForPage($App->itemsForPage);
		$Module->setMySessionApp($_MY_SESSION_VARS[$App->sessionName]);

		$opt = array('langUser'=>$_lang['user'],'hideactive'=>0);
		$App->items = Menu::setMenuTreeData($opt);
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voci'],$_lang['lista %ITEM%']);
		$App->viewMethod = 'list';	
	break;	

	case 'ajaxLoadTemplateDataItem':
		$template = $Module->getTemplatePredefinito(0);		
		if (isset($template->id) && (int)$template->id > 0) {	
			include_once(PATH.$App->pathApplications.Core::$request->action."/templates/".$App->templateUser."/formTemplatesData.tpl.php");
			}
		$renderTpl = false;
		die();		
	break;
	
	case 'ajaxReloadTemplateDataItem':
		if ($App->id > 0) {
			$template = $Module->getTemplatePredefinito($App->id);
			if (isset($template->id) && (int)$template->id > 0) {	
				include_once(PATH.$App->pathApplications.Core::$request->action."/templates/".$App->templateUser."/formTemplatesData.tpl.php");
				}
			}
		$renderTpl = false;
		die();	
	break;
	}

/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':	
		$App->templateApp = 'formItem.html';
		$App->defaultJavascript = "var moduleName = '".Core::$request->action."';";
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js" type="text/javascript"></script>';
	break;
	
	case 'formSeoMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->templateApp = 'formSeoItem.tpl.php';
		$App->methodForm = 'updateSeoItem';	
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formSeoItem.js"></script>';
	break;

	case 'list':
	default:	
		$App->templateApp = 'listItems.html';
		$App->css[] = '<link href="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.cookie/jquery.cookie.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.min.js" type="text/javascript"></script>';
		//$App->jscript[] = '<script src="'.URL_SITE_ADMIN.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.bootstrap3.js" type="text/javascript"></script>';		
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js" type="text/javascript"></script>';		
	break;
	}
?>
