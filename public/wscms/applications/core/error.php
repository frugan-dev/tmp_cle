<?php
/* wscms/core/error.php v.3.5.4. 16/09/2019 */

/* variabili ambiente */
$App->pageTitle = 'Error';
$App->pageSubTitle = 'Error';
$App->templateApp = Core::$request->action.'.html';
$App->templateBase = 'struttura-error.html';
$App->coreModule = true;

switch(Core::$request->method) {
	
	case '404':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Error 404!'];
		$App->error_content = $_lang['testo errore 404'];
	break;
	
	case 'access':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Access Error!'];
		$App->error_content = $_lang['testo errore accesso'];
	break;
	
	case 'mail':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Mail Error!'];
		$App->error_content = $_lang['testo errore mail'];
	break;
	
	case 'db':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Database Error!'];
		$App->error_content = $_lang['testo errore database'];
		$App->error_contentAlt = (Core::$request->param != '' ? Core::$request->param : '');
	break;
	
	default:
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Internal Server Error!'];
		$App->error_content = $_lang['testo errore generico'];
	break;
	
	}
?>