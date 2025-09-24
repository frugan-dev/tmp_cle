<?php

//die('error page php');
/* error.php v.3.5.4. 15/04/2019 */

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



// BREADCRUMBS

$App->breadcrumbs->items = array();
$App->breadcrumbs->items[] = array('class'=>'home','url'=>URL_SITE,'title'=>'Home');
$App->breadcrumbs->items[] = array('class'=>'active','title'=>$App->error_subtitle);		
$App->breadcrumbs->title = $App->error_title ;
		
// SEO
$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->error_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
$App->metaDescriptionPage .= '';
$App->metaKeywordsPage .= '';

$App->templateApp = 'error';
?>