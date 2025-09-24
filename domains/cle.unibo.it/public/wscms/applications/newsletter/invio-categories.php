<?php
/* wscms/newsletter/invio-cat.php v.3.1.0. 10/01/2017 */

$table = DB_TABLE_PREFIX.$App->applicationName.'_indirizzi';
$Module = new Module(Core::$request->action,$table);

/* variabili ambiente */
$App->pageTitle = 'Invio Newsletter';
$App->pageSubTitle = 'indica gli indirizzi email per la lista invio';
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> Invio Newsletter</li>';
$App->newsletter = new stdClass;
$App->newsletter->id = 0;
$App->newsletterCheck = 0;

switch(Core::$request->method) {	
	case 'ajaxGetListAddressCatTemp':
		Sql::initQuery($App->tableIndInvio,array('*'));
		Sql::setOrder('email ASC');
		$foo = Sql::getRecords();
		if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		echo json_encode($foo);
		die();
	break;
	
	
	case 'ajaxMoveAddressCatToSendList':
		//Config::$debugMode = 1;
		if (isset($_POST['listAddressCat'])) {
			$arr = explode(',',$_POST['listAddressCat']);

			foreach($arr AS $keyCat) {

				Sql::initQuery();
				Sql::setCustomQry("SELECT i.*,ic.id_cat FROM ".$App->tableInd." AS i LEFT JOIN ".$App->tableRifCatInd." AS ic ON (ic.id_ind = i.id) WHERE ic.id_cat = ? AND i.active = 1");
				Sql::setFieldsValue(array($keyCat));
				$obj = Sql::getRecords();
				if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

				if (isset($obj) && is_array($obj) && count($obj) > 0) {
					foreach($obj AS $value) {
						
						if (isset($value->email) && $value->email != '') {
							
							Sql::initQuery($App->tableIndInvio,array('*'),array($value->email),'email = ?');
							$foo = Sql::getRecords();	
							if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
							$count = count($foo);	
							if ($count == 0) {
								Sql::initQuery($App->tableIndInvio,array('email','hash','inviata'),array($value->email,$value->hash,'0'));
								Sql::insertRecord();
								if (Core::$resultOp->error > 0) { die('Errore database 1'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
							}
						}					
					}
		
				}					
					
			}						
		}		
		die();
	break;

	
	case 'ajaxDeleteAddressCatToSendList':
		if (isset($_POST['listAddress'])) {
			$arr = explode(',',$_POST['listAddress']);			
			foreach($arr AS $keyAddress) {
				Sql::initQuery($App->tableIndInvio,array(),array($keyAddress),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }					
			}						
		}
		die();
	break;

	case 'previewInvioCat':
		$App->item = new stdClass;	
		Sql::initQuery($App->tableNew,array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 0) {	
			$App->item->finalOutput = '';
			$file = PATH_UPLOAD_DIR.$App->templatesFolder.$App->item->template;
			$urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;
			$App->item->content_it = ToolsStrings::parseHtmlContent($App->item->content_it,array('customtag'=>'{{PATHNEWSLETTER}}','customtagvalue'=>UPLOAD_DIR.$App->templatesFolder));
			if (file_exists($file) == true) {
				$App->item->finalOutput = file_get_contents($file);
				$App->item->finalOutput = preg_replace('/{{PATHNEWSLETTER}}/',UPLOAD_DIR.$App->templatesFolder,$App->item->finalOutput);	
				$App->item->finalOutput = preg_replace('/{{DATATIMEINS}}/',$App->item->datatimeins,$App->item->finalOutput);
				$App->item->finalOutput = preg_replace('/{{TITLE}}/', htmlspecialchars($App->item->title_it),$App->item->finalOutput);
				$App->item->finalOutput = preg_replace('/{{CONTENT}}/',$App->item->content_it,$App->item->finalOutput);	
				$App->item->finalOutput = preg_replace('/{{URLDELETE}}/',$urldelete,$App->item->finalOutput);
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
	break;

	
	default:	
		$App->newsletter = new stdClass;
		$App->newsletterSelect = new stdClass;
		$App->newsletter->id = 0;
	
		if (isset($_POST['id_news']) && $_POST['id_news'] != '') {	
			$_SESSION['newsletter']['newsletter da inviare'] = $_POST['id_news'];
		}	
		$App->newsletter->id = intval($_SESSION['newsletter']['newsletter da inviare']);
		
		/* preleva le newsletter per la select */		
		Sql::initQuery($App->tableNew,array('*'));
		Sql::setOrder('datatimeins DESC');
		$App->newsletterSelect = Sql::getRecords();
		if (Core::$resultOp->error == 1) die();		
			
		if ($App->newsletter->id > 0) {
			Sql::initQuery($App->tableNew,array('*'),array($App->newsletter->id),'active = 1 AND id = ?');
			$obj = Sql::getRecord();
			if (Core::$resultOp->error == 0) {
				$App->newsletter = $obj;
				}
			} else {
				Core::$resultOp->message = "Devi scegliere una newsletter!";
				Core::$resultOp->error = 1;
				}			
		
		$App->newsletterCheck = Core::$resultOp->error;			
	
		$App->templatePage = 'listInvioCat.tpl.php';	
		
		Sql::initQuery();
		Sql::setCustomQry(
		"SELECT c.id AS id,c.title_it AS title_it,c.active AS active,(SELECT COUNT(id_ind) FROM ".$App->tableRifCatInd." AS ic WHERE ic.id_cat = c.id) AS numtotitems FROM ".$App->tableIndCat." AS c");
		Sql::setClause('c.active = 1');
		$App->listAddressCat = Sql::getRecords();
		/* trova i gli indirizzi veramente attivi */		
		$arr = array();
		if (is_array($App->listAddressCat) && count($App->listAddressCat) > 0) {
			foreach ($App->listAddressCat AS $key=>$value) {				
				Sql::initQuery();
				Sql::setCustomQry("SELECT COUNT(i.id) AS indirizzi,ic.id_cat FROM ".$App->tableInd." AS i LEFT JOIN ".$App->tableRifCatInd." AS ic ON (ic.id_ind = i.id) WHERE ic.id_cat = ? AND i.active = 1");
				Sql::setFieldsValue(array($value->id));
				$obj = Sql::getRecords();			
				if (isset($obj[0]->indirizzi)) $value->numitems = $obj[0]->indirizzi;
				$arr[] = $value;				
				}
			$App->listAddressCat = $arr;		
			}		
		$App->templateApp = 'listInvioCategories.html';	
	
	break;	
	}
?>