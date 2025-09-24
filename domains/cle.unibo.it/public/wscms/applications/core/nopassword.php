<?php
/* wscms/core/nopassword.php v.3.5.5. 15/05/2019 */

use Soundasleep\Html2Text;

//Core::setDebugMode(1);

$App->pageTitle = $_lang['titolo sezione richiesta password'];
$App->pageSubTitle = $_lang['titolo sezione richiesta password'];
$App->pathApplications = 'application/core/';

$App->templateApp = Core::$request->action.'.html';
$App->action = '';
$App->item = new stdClass;
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$App->templateBase = 'struttura-login.html';

$section = preg_replace('/%ITEM%/',(string) $_lang['login'],(string) $_lang['torna al %ITEM%']);
$section1 = '<a href="'.URL_SITE_ADMIN.'" title="'.ucfirst($section).'">'.ucfirst((string) $_lang['login']).'</a>';
$App->returnlink = ucfirst(preg_replace('/%ITEM%/',$section1,(string) $_lang['torna al %ITEM%']));

if (isset($_POST['submit'])) {	
	if ($_POST['username'] == "") {
		Core::$resultOp->error = 1;
		Core::$resultOp->message = preg_replace('/%ITEM%/',(string) $_lang['nome utente'],(string) $_lang['Devi inserire un %ITEM%!']);
		} else {
			$username = SanitizeStrings::stripMagic(strip_tags((string) $_POST['username']));
			}
	if (Core::$resultOp->error == 0) {	
		/* legge username dalla email */
		/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
		Sql::initQuery(DB_TABLE_PREFIX.'users',['id','username','email'],[$username],"username = ? AND active = 1");
		$App->item = Sql::getRecord();		
		if(Core::$resultOp->error == 0) {
			if (Sql::getFoundRows() > 0) {
				/* crea la nuova password */	
				$passw = ToolsStrings::setNewPassword('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890',8);
				//$passw = 'master'; // per test
				$criptPassw = password_hash((string) $passw, PASSWORD_DEFAULT);			
				$titolo = $_lang['titolo email sezione richiesta password'];
				$titolo = preg_replace('/%SITENAME%/',(string) SITE_NAME,(string) $titolo);
				$testo = $_lang['testo email sezione richiesta password'];
				$testo = preg_replace('/%SITENAME%/',(string) SITE_NAME,(string) $testo);
				$testo = preg_replace('/%PASSWORD%/',(string) $passw,$testo);
				$testo = preg_replace('/%USERNAME%/',(string) $App->item->username,$testo);
				$text_plain = Html2Text::convert($testo);
				//echo '<br>titolo: '.$titolo;	
				//echo '<br>testo: '.$testo; 	
				//die();			
				/* aggiorno la password nel db */						
				/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
				Sql::initQuery(DB_TABLE_PREFIX.'users',['password'],[$criptPassw,$App->item->id],"id = ?");
				Sql::updateRecord();
				if (Core::$resultOp->error == 0) {	
					$opt = [];
					//FIXED - DKIM requirements
					$opt['fromEmail'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['default email'];
					$opt['fromLabel'] = $globalSettings['default email label'];	
					$opt['replyTo'] = [$globalSettings['default email'] => $globalSettings['default email label']];
					$opt['sendDebug'] = $globalSettings['send email debug'];
					$opt['sendDebugEmail'] = $globalSettings['email debug'];								
					Mails::sendEmail($App->item->email,$titolo,$testo,$text_plain,$opt);
					//Core::$resultOp->error = 1; //per test
					if (Core::$resultOp->error == 0) {
						Core::$resultOp->message = $_lang['La nuova password vi è stata inviata con email indirizzo associato ed è stata memorizzata nel sistema!'];
						} else {
							Core::$resultOp->message = $_lang['Errore invio della email! Vi invitiamo a ripetere la procedura o contattare amministratore.']; 
							}
											
					} else { 
						Core::$resultOp->message = $_lang['Errore database! La nuova password NON è stata memorizzata nel sistema! Vi invitiamo a ripetere la procedura o contattare amministratore'];							
						}										
				}	else {	
					Core::$resultOp->error = 1;
					Core::$resultOp->message = Config::$langVars['Il nome utente inserito non esiste! Vi invitiamo a ripetere la procedura o contattare amministratore del sistema.'];
					}
			}
		}	
	}
?>