<?php
/* wscms/contacts/config.php v.3.5.4. 31/07/2019 */
switch(Core::$request->method) {
	case 'updateConf':
		//Sql::setDebugMode(1);
		//ToolsStrings::dump($_POST);

		// requpero i vecchi dati
		$App->oldItem = new stdClass;
		Sql::initQuery($App->params->tables['conf'],array('*'),array(),'id = 1');
		$App->oldItem = Sql::getRecord();
		//if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); die(); }	

		/* preleva il filename dal form */
		ToolsUpload::setFieldPostImage('image_header');
		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	   	
		ToolsUpload::getFilenameFromForm(1);	 
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/formConf');
		}  		
		$_POST['image_header'] = ToolsUpload::getFilenameMd5();
		$_POST['org_image_header'] = ToolsUpload::getOrgFilename();
		$tempFilename = ToolsUpload::getTempFilename();
		$uploadFilename = $_POST['image_header'];	   	
		/* imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)*/
		if ($_POST['image_header'] == '' && $App->oldItem->image_header != '') $_POST['image_header'] = $App->oldItem->image_header;
		if ($_POST['org_image_header'] == '' && $App->oldItem->org_image_header != '') $_POST['org_image_header'] = $App->oldItem->org_image_header; 
	 	/* opzione cancella immagine */
		if (isset($_POST['deleteFilenameHeader']) && $_POST['deleteFilenameHeader'] == 1) {
			if (file_exists($App->params->uploadPaths['conf'].$App->oldItem->image_header)) {			
			 @unlink($App->params->uploadPaths['conf'].$App->oldItem->image_header);	
		 	}	
		 	$_POST['image_header'] = '';
			$_POST['org_image_header'] = ''; 	
		}	 

		Form::parsePostByFields($App->params->fields['conf'],$_lang,array());
		
		//ToolsStrings::dump($_POST);

		if (Core::$resultOp->error == 0) {							
			Sql::updateRawlyPost($App->params->fields['conf'],$App->params->tables['conf'],'id',1);

			if ($uploadFilename != '') {
				move_uploaded_file($tempFilename,$App->params->uploadPaths['conf'].$uploadFilename) or die('Errore caricamento file');   			
				/* cancella l'immagine vecchia */
				 if (file_exists($App->params->uploadPaths['conf'].$App->oldItem->image_header)) {			
					 @unlink($App->params->uploadPaths['conf'].$App->oldItem->image_header);			
				}	   			
			}
			
			if (Core::$resultOp->error == 0) {
				$App->pageSubTitle = preg_replace('/%ITEM%/', $_lang['configurazione'], $_lang['%ITEM% modificata']).'!';		
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Core::$langVars['configurazione'],Core::$langVars['%ITEM% modificata'])).'!';
				ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/listItem');							
				}	
			}
		$App->viewMethod = 'formMod';
	break;
	
	default;	
		$App->pageSubTitle = preg_replace('/%ITEM%/', $_lang['configurazione'], $_lang['modifica %ITEM%']);
		$App->viewMethod = 'formMod';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	default:
	case 'formMod':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['conf'],array('*'),array());
		$App->item = Sql::getRecord();
		$App->methodForm = 'updateConf';
		$App->templateApp = 'formConf.tpl.php';
		$App->css[] = '<link href="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formConf.css" rel="stylesheet">';		
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formConf.js"></script>';			
	break;

	}	
?>