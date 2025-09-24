<?php
/* wscms/core/nousername.php v.3.5.5. 15/05/2019 */

use Soundasleep\Html2Text;

//Core::setDebugMode(1);

$App->pageTitle = $_lang['titolo sezione richiesta username'];
$App->pageSubTitle = $_lang['titolo sezione richiesta username'];
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
	if ($_POST['email'] == "") {
			Core::$resultOp->error = 1;
			Core::$resultOp->message = preg_replace('/%ITEM%/',(string) $_lang['indirizzo email'],(string) $_lang['Devi inserire un %ITEM%!']);
			} else {
				$email = SanitizeStrings::stripMagic(strip_tags((string) $_POST['email']));
				Core::$resultOp->error = 0;
				}			
	if (Core::$resultOp->error == 0) {	
		/* legge username dalla email */	
		/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
		Sql::initQuery(DB_TABLE_PREFIX.'users',['id','username'],[$email],"email = ? AND active = 1");
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 0) {		
			if (Sql::getFoundRows() > 0) {
				/* crea l'email */
				$titolo = $_lang['titolo email sezione richiesta username'];
				$titolo = preg_replace('/%SITENAME%/',(string) SITE_NAME,(string) $titolo);
				$testo = $_lang['testo email sezione richiesta username'];
				$testo = preg_replace('/%SITENAME%/',(string) SITE_NAME,(string) $testo);
				$testo = preg_replace('/%EMAIL%/',(string) $email,$testo);
				$testo = preg_replace('/%USERNAME%/',(string) $App->item->username,$testo);
				$text_plain = Html2Text::convert($testo);
				//echo $titolo;echo $testo;die();
				$opt = [];
				//FIXED - DKIM requirements
				$opt['fromEmail'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['default email'];
				$opt['fromLabel'] = $globalSettings['default email label'];
				$opt['replyTo'] = [$globalSettings['default email'] => $globalSettings['default email label']];		
				$opt['sendDebug'] = $globalSettings['send email debug'];
				$opt['sendDebugEmail'] = $globalSettings['email debug'];								
				Mails::sendEmail($email,$titolo,$testo,$text_plain,$opt);
				//Core::$resultOp->error = 1; per test
				if (Core::$resultOp->error == 0) {
					Core::$resultOp->message = $_lang['Email inviata correttamente! Nel testo troverete il username!'];
					} else {
						Core::$resultOp->message = $_lang['Errore invio della email! Vi invitiamo a ripetere la procedura o contattare amministratore.']; 
						}
				} else {	
				Core::$resultOp->error = 1;
				Core::$resultOp->message = $_lang['Indirizzo email inserito non esiste! Vi invitiamo a ripetere la procedura o contattare amministratore del sistema.'];
				}
			}			
		}
	}
?>