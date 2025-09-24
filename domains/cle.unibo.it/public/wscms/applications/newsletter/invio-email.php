<?php
/* wscms/newsletter/invio-email.php v.3.1.0. 09/01/2017 */

$Module = new Module(Core::$request->action,$App->tableIndInvio,$_MY_SESSION_VARS);

$newsletter_id = intval($_SESSION['newsletter']['newsletter da inviare finale']);

$App->pageTitle = 'Invio Newsletter';
$App->pageSubTitle = 'Invio della newsletter agli indirizzi email';
$App->viewMethod = 'list';
$App->newsletter = new stdClass;
$App->newsletter->id = 0;
$App->newsletterCheck = 0;

switch(Core::$request->method) {	
	case 'ajaxUpdatePanel':

		$output = '';
		$now = date('Y-m-d H:i:s');	

		$newsletter_id = intval($_SESSION['newsletter']['newsletter da inviare finale']);

		if ($newsletter_id == 0) {
			$html = '
			<div id="systemMessageID" class="alert alert-danger">
				Deve essere scelta una newsletter da inviare!
			</div>	
			';
			echo $html; 
			die();
		}

		// preleva i dati della newsletter
		Sql::initQuery($App->tableNew,['*'],[$newsletter_id],'active = 1 AND id = ?');
		$App->newsletterDetails = Sql::getRecord();	
		if (Core::$resultOp->error > 0) { die('Errore database preleva i dati della newsletter'); }
		if (!isset($App->newsletterDetails->id) || (isset($App->newsletterDetails->id) && $App->newsletterDetails->id == 0)) {
			$html = '
			<div id="systemMessageID" class="alert alert-danger">
				La newsletter scelta non esiste!
			</div>	
			';
			echo $html; 
			die();
		}

		$title = $App->newsletterDetails->title_it;
	

		$file = ADMIN_PATH_UPLOAD_DIR.$App->templatesFolder.$App->newsletterDetails->template;
		$urldelete = URL_SITE.$App->settings['admin url delete address']->value_it;
		$App->newsletterDetails->content_it = ToolsStrings::parseHtmlContent($App->newsletterDetails->content_it,['customtag'=>'{{PATHNEWSLETTER}}','customtagvalue'=>UPLOAD_DIR.$App->templatesFolder]);
		if (file_exists($file) == true) {
			$mailbody = file_get_contents($file);
			$mailbody = preg_replace('/%PATHNEWSLETTER%/',UPLOAD_DIR.$App->templatesFolder,$mailbody);	
			$mailbody = preg_replace('/%DATATIMEINS%/',$App->newsletterDetails->datatimeins,(string) $mailbody);
			$mailbody = preg_replace('/%TITLE%/', htmlspecialchars($App->newsletterDetails->title_it),(string) $mailbody);
			$mailbody = preg_replace('/%CONTENT%/',(string) $App->newsletterDetails->content_it,(string) $mailbody);
			$mailbody = preg_replace('/%URLSITE%/',URL_SITE,(string) $mailbody);	
	    } else {
			$mailbody = $App->newsletterDetails->content_it;
		}

		//echo $mailbody; die();
			
		// controlla se ci sono ancora email da inviare
		Sql::initQuery($App->tableIndInvio,['*'],[],'inviata = 0');
		Sql::setLimit(' LIMIT 1  OFFSET 0');
		$listAddress = Sql::getRecords();
		if (Core::$resultOp->error > 0) { die('Errore database controlla se ci sono ancora email da inviare'); }
		$countEmailToSend = count($listAddress);

		if ($countEmailToSend > 0) {

			$listEmail = '';							
			if (is_array($listAddress) && count($listAddress) > 0){
				foreach($listAddress AS $value) {

					if ($value->email != '' && $mailbody != ''){	

						// aggiorna il file con l'hash indirizzo
						$mailbodySend = $mailbody;
						$mailbodySend = preg_replace('/%URLDELETE%/',$urldelete.'/'.$value->hash,(string) $mailbodySend);

						// imposto la email
						$mail = new PHPMailer();			
						$mail->SetFrom($App->settings['admin email address']->value_it,$App->settings['admin label email address']->value_it);
						//$mail->addCustomHeader("Return-Receipt-To: robymant@tiscali.it");
						//$mail->AddReplyTo('robymant@tiscali.it','Framework Newsletter');	
						$mail->IsHTML(true);
						$mail->CharSet = 'UTF-8';

						// invio newsletter			
						$mail->Subject = $title;
						$mail->AltBody = strip_tags((string) $mailbodySend);
						$mail->MsgHTML($mailbodySend);
						$mail->AddAddress($value->email,$value->email);
						if (isset($App->settings['send emails for debug']->value_it) && $App->settings['send emails for debug']->value_it == 1) {
							if (isset($App->settings['email address for debug']->value_it) && $App->settings['email address for debug']->value_it != '') {
								$mail->AddBcc($App->settings['email address for debug']->value_it);
							}
						}

						if (!$mail->Send()) {							
							$listEmail .= '<li class="text-danger">'.$value->email.' -> Attenzione: errore invio!</li>';
				  			$mailSubmited = false;										  		
						} else {
							$listEmail .= '<li class="text-success">'.$value->email.'</li>';
							$mailSubmited = true;							  
						}	
						
						// imposta il flag invio
						Sql::initQuery($App->tableIndInvio,['inviata'],['1',$value->id],'id = ?');
						Sql::updateRecord();
						if (Core::$resultOp->error > 0) { die('Errore database imposta il flag invio zero'); }	

					}


					// crea ciclo attesa php
					$mul = 1;
					while ($mul <= 30000000) $mul++;


				}
			}

			// crea l'output
			$header = 'Leggo la lista invio';
			$footer = 'Attendi...';
			$output .= '<p>Ora: '.$now.'</p>';
			$output .= '<p>Newsletter spedita agli indirizzi:</p>';
			$output .= '<ul>';
			$output .= $listEmail;
			$output .= '</ul>';		
			$js = "$(document).ready(function() {  
					let url = siteAdminUrl+CoreRequestAction+'ajaxUpdatePanel';
					$('#panelInvioEmailID').load(url);
					});";


		} else {


			// setta i paremetri inviata nella newsletter
			Sql::initQuery($App->params->tables['new'],['datatimesent','sent'],[$now,'1',$newsletter_id],'id = ?');
			//Sql::updateRecord();
			if (Core::$resultOp->error > 0) { die('Errore database setta i paremetri inviata nella newsletter'); }	

			$header = 'Procedura completata';
			$footer = 'Fatto!';
			$output .= '<p>Tutte le email sono state inviate!</p>';

			// pulisco la tabella
			Sql::executeCustomQuery("TRUNCATE TABLE ".$App->tableIndInvio); 
			if (Core::$resultOp->error > 0) { die('Errore database pulisco tabella'); }
			$js = "";

		}

		$html = '
		<div class="row">	
			<div class="col-lg-12">
				<div class="panel panel-info">
					<div class="panel-heading">'.$header.'</div>
						<div id="panelBodyID" class="panel-body">
							'.$output.'					
						</div>
					<div class="panel-footer">'.$footer.'</div>
				</div>		
			</div>
		</div><!--/row-->
		<script language="javascript">
			'.$js.'
		</script>';

		echo $html;
		die();
	break;
	default;	

		$App->newsletter = new stdClass;
		$App->newsletterSelect = new stdClass;
		$App->newsletter->id = 0;
		
		/* preleva le newsletter per la select */		
		Sql::initQuery($App->tableNew,['*']);
		Sql::setOrder('datatimeins DESC');
		$App->newsletterSelect = Sql::getRecords();
		if (Core::$resultOp->error == 1) die();

		if (isset($_POST['id_news']) && $_POST['id_news'] != '') {	
			$_SESSION['newsletter']['newsletter da inviare finale'] = $_POST['id_news'];
		}	

		$App->newsletter->id = intval($_SESSION['newsletter']['newsletter da inviare finale']);

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



		





		$App->templateApp = 'listInvioEmail.html';	
	break;	
}
?>