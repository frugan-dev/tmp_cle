<?php
/* wscms/newsletter/invio.php v.3.1.0. 09/01/2016 */

$table = DB_TABLE_PREFIX.'newsletter_indirizzi';
$Module = new Module(Core::$request->action,$table,$_MY_SESSION_VARS);

/* variabili ambiente */
$App->pageTitle = 'Invio Newsletter';
$App->pageSubTitle = 'indica gli indirizzi email per la lista invio';
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> Invio Newsletter</li>';
$App->newsletter = new stdClass;
$App->newsletter->id = 0;
$App->newsletterCheck = 0;

switch(Core::$request->method) {	

	case 'ajaxGetListAddressTemp':
		Sql::initQuery($App->tableIndInvio,['*']);
		Sql::setOrder('email ASC');
		$foo = Sql::getRecords();
		if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		echo json_encode($foo);
		die();
	break;
	
	case 'ajaxMoveAddressToSendList':
		if (isset($_POST['listAddress'])) {

			$arr = explode(',',(string) $_POST['listAddress']);
			/* crea il ciclo */
			foreach($arr AS $keyAddress) {

				//echo '<br>$keyAddress: '.$keyAddress;

				// preleva i dati dell'indirizzo
				$obj = new stdClass();
				Sql::initQuery($App->tableInd,['*'],[$keyAddress],'active = 1 AND id = ?');
				$obj = Sql::getRecord();
				if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

				//ToolsStrings::dump($obj);

				if (isset($obj->email) || $obj->email != '') {	

					//echo '<br>esiste email';

					// controlla se esiste gia memorizzata la email
					Sql::initQuery($App->tableIndInvio,['*'],[$obj->email],'email = ?');
					$foo = Sql::getRecords();	
					if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

					$count = count($foo);	

					//echo '<br>count: '.$count;

					if ($count == 0) {
						// lo memorizza nella tabella
						Sql::initQuery($App->tableIndInvio,['email','hash','inviata'],[$obj->email,$obj->hash,'0']);
						Sql::insertRecord();
						if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
					}				
				}
			}					
		}
		die();
	break;
	
	case 'ajaxDeleteAddressToSendList':
		if (isset($_POST['listAddress'])) {
			$arr = explode(',',(string) $_POST['listAddress']);			
			foreach($arr AS $keyAddress) {
				Sql::initQuery($App->tableIndInvio,[],[$keyAddress],'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { die('Errore database'); ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }					
			}						
		}
		die();
	break;

	case 'previewInvio':

		$App->item = new stdClass;	
		Sql::initQuery($App->tableNew,['*'],[$App->id],'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }

		$App->item->finalOutput = '';
		$file = ADMIN_PATH_UPLOAD_DIR.$App->templatesFolder.$App->item->template;
		$urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;

		$App->item->content_it = ToolsStrings::parseHtmlContent($App->item->content_it,['customtag'=>'{{PATHNEWSLETTER}}','customtagvalue'=>UPLOAD_DIR.$App->templatesFolder]);
		if (file_exists($file) == true) {
			$App->item->finalOutput = file_get_contents($file);
			$App->item->finalOutput = preg_replace('/%PATHNEWSLETTER%/',UPLOAD_DIR.$App->templatesFolder,$App->item->finalOutput);	
			$App->item->finalOutput = preg_replace('/%DATATIMEINS%/',(string) $App->item->datatimeins,(string) $App->item->finalOutput);
			$App->item->finalOutput = preg_replace('/%TITLE%/', htmlspecialchars((string) $App->item->title_it),(string) $App->item->finalOutput);
			$App->item->finalOutput = preg_replace('/%CONTENT%/',(string) $App->item->content_it,(string) $App->item->finalOutput);	
			$App->item->finalOutput = preg_replace('/%URLDELETE%/',$urldelete,(string) $App->item->finalOutput);
		} else {
		    $App->item->finalOutput = $App->item->content_it;
		}   
		echo $App->item->finalOutput;			
		die();
	break;

	
	default;
		//Config::$debugMode = 1;
		$App->newsletter = new stdClass;
		$App->newsletterSelect = new stdClass;
		$App->newsletter->id = 0;

		if (isset($_POST['id_news']) && $_POST['id_news'] != '') {	
			$_SESSION['newsletter']['newsletter da inviare'] = $_POST['id_news'];
		}	
		$App->newsletter->id = intval($_SESSION['newsletter']['newsletter da inviare']);

		Sql::initQuery($App->tableNew,['*']);
		$App->newsletterSelect = Sql::getRecords();
		if (Core::$resultOp->error == 0) {
			
			if ($App->newsletter->id > 0) {
				Sql::initQuery($App->tableNew,['*'],[$App->newsletter->id],'active = 1 AND id = ?');
				$obj = Sql::getRecord();
				if (Core::$resultOp->error == 0) {
					$App->newsletter = $obj;
					}
			
				} else {
					Core::$resultOp->message = "Devi scegliere una newsletter!";
					Core::$resultOp->error = 1;
					}
					
			/* trova almeno una newsletter */
			//$App->newsletter = $Module->getNewsletter($App->tableNew,$App->newsletter->id);
			//Core::$resultOp->error = $Module->error;
			//Core::$resultOp->message = $Module->message;
			$App->newsletterCheck = Core::$resultOp->error;

			/* preleva gli indirizzi per la select */
			Sql::initQuery($App->tableInd,['*']);
			$clause = 'active = 1';
			Sql::setClause($clause);
			Sql::setOrder('email ASC');
			$App->listAddress = Sql::getRecords();

			//ToolsStrings::dump($App->listAddress);
			
			if (Core::$resultOp->error == 0) {				
				if(Sql::getFoundRows() == 0) {
					$App->newsletterCheck = 1;
					Core::$resultOp->error = 1;
					Core::$resultOp->message = "Devono esserci degli indirizzi attivi!";
					}
				}	
			}	
		$App->templateApp = 'listInvio.html';	
	break;	
	}
?>