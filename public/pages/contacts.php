<?php
/* contacts.php v.3.5.4. 07/05/2019 */

use Soundasleep\Html2Text;

// lucia.manservisi@unibo.it

$App->moduleData = new stdClass();

// preleva configurazione modulo
Sql::initQuery(DB_TABLE_PREFIX.'contacts_config',['*'],[],'');	
$App->moduleConfig = Sql::getRecord();
$App->moduleConfig->title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
$App->moduleConfig->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig,'text_intro_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 
$App->moduleConfig->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'page_content_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]); 

$App->moduleConfig->meta_title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_title_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
$App->moduleConfig->meta_description = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_description_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
$App->moduleConfig->meta_keywords = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_keywords_',Config::$langVars['user'],['htmLawed'=>0,'parse'=>1]);
//ToolsStrings::dump($App->moduleConfig);

// gestione titolo
$App->moduleData->title = 'Team';
if ($App->moduleConfig->title != '') $App->moduleData->title = $App->moduleConfig->title;

// gestione innagine header
$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if ($App->moduleConfig->image_header != '') $App->moduleData->imageheader = $App->moduleConfig->image_header;
if ($App->moduleConfig->org_image_header != '') $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;

$App->moduleConfig->url_privacy_page = ToolsStrings:: parseHtmlContent($App->moduleConfig->url_privacy_page,[]);


switch (Core::$request->method) 
{ 

	case 'send':

		// controllo recaptcha 
		if (!isset($_POST['recaptcha_response'])) {
			echo json_encode(['error'=>1,'message'=>'Recacptcha mancante! Il sistema ti ha identificato come robot!']);
			die();
		} else {
			$captcha = $_POST['recaptcha_response'];
			$secret = $globalSettings['google recaptcha secret'];
			$json = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=". $secret . "&response=" . $captcha), true);
			
			if (!$json['success']) {
				echo json_encode(['error'=>1,'message' =>'Recacptcha! Il sistema ti ha identificato come robot!']);
				die();
			}
		}
		// controllo recaptcha 
		
		// controllo POST
		$fields = [
			
			'message'						=> [	
				'required'					=> true,
				'name'						=> 'message',
				'error message'             => preg_replace('/%ITEM%/',(string) Config::$langVars['messaggio'],(string) Config::$langVars['Devi inserire un %ITEM%!'])
			],
			
			'name'						=> [	
				'required'					=> true,
				'name'						=> 'object',
				'error message'             => preg_replace('/%ITEM%/',(string) Config::$langVars['nome'],(string) Config::$langVars['Devi inserire un %ITEM%!'])
			],
			
			'object'						=> [	
				'required'					=> true,
				'field'						=> 'object',
				'error message'             => preg_replace('/%ITEM%/',(string) Config::$langVars['oggetto'],(string) Config::$langVars['Devi inserire un %ITEM%!'])
			],
			'email'						=> [	
				'required'					=> true,
				'field'						=> 'email',
				'error message'             => preg_replace('/%ITEM%/',(string) Config::$langVars['indirizzo email valido'],(string) Config::$langVars['Devi inserire un %ITEM%!']),
				'validate'					=> 'isemail',
			],
			'privacy'						=> [	
				'required'					=> true,
				'field'						=> 'privacy',
				'error message'             => Config::$langVars['Devi autorizzare il trattamento della privacy!'],
				'validate'					=> 'issameintvalue',
				'valuerif'					=> 1
			],
			
		];
		Form::parsePostByFields($fields,Config::$langVars,['stripmagicfields'=>false]);
		//ToolsStrings::dump(Core::$resultOp);
		if (Core::$resultOp->error > 0) {
			$result = [
				'error' => 1,
				'message' => implode('<br>',Core::$resultOp->messages)
			];
			//echo json_encode($result);
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
		}

		// manda la email con il messaggio del modulo allo staff del sito
		$opt = [];	
		$subject = $App->moduleConfig->admin_email_subject;					
		$content = $App->moduleConfig->admin_email_content;	
		$subject = Mails::parseMailContent($_POST,$subject,$optt=[]);
		$content = Mails::parseMailContent($_POST,$content,$optt=[]);	
		//echo '<br>'.$subject;
		//echo '<br>'.$content;	
		$content_plain = Html2Text::convert($content);
		//FIXED - DKIM requirements
		$opt['fromEmail'] = $_ENV['MAIL_FROM_EMAIL'] ?? $App->moduleConfig->email_address;
		$opt['fromLabel'] = $App->moduleConfig->label_email_address;
		$opt['replyTo'] = [$_POST['email']];
		$address = $App->moduleConfig->email_address;				
		$opt['sendDebug'] = $App->moduleConfig->send_email_debug;
		$opt['sendDebugEmail'] = $App->moduleConfig->email_debug;
		Mails::sendEmail($address,$subject,$content,$content_plain,$opt);
		if (Core::$resultOp->error > 0) {
			$result = [
				'error' => 1,
				'message' => Config::$langVars['Il tuo messaggio NON è stato spedito! Riprova.']
			];
			//echo json_encode($result); die();
			$_SESSION['message'] = '1|'.Config::$langVars['Il tuo messaggio NON è stato spedito! Riprova.'];
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
		}

		// manda la email con il messaggio del modulo allo utente 
		$opt = [];	
		$subject = Multilanguage::getLocaleObjectValue($App->moduleConfig,'user_email_subject_',Config::$langVars['user'],[]);			
		$content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'user_email_content_',Config::$langVars['user'],[]);				
		$subject = Mails::parseMailContent($_POST,$subject,$optt=[]);
		$content = Mails::parseMailContent($_POST,$content,$optt=[]);	
		$content_plain = Html2Text::convert($content);
		
		/*
		echo '<br>'.$subject;
		echo '<br>'.$content;	
		echo '<br>'.$content_plain;
		die();	
		*/
		//FIXED - DKIM requirements
		$opt['fromEmail'] = $_ENV['MAIL_FROM_EMAIL'] ?? $App->moduleConfig->email_address;
		$opt['fromLabel'] = $App->moduleConfig->label_email_address;
		$opt['replyTo'] = [$App->moduleConfig->email_address => $App->moduleConfig->label_email_address];
		$address = $_POST['email'];				
		$opt['sendDebug'] = $App->moduleConfig->send_email_debug;
		$opt['sendDebugEmail'] = $App->moduleConfig->email_debug;
		Mails::sendEmail($address,$subject,$content,$content_plain,$opt);
		if (Config::$resultOp->error > 0) {
			$result = [
				'error' => 1,
				'message' => Config::$langVars['Il tuo messaggio di conferma NON è stato spedito! Riprova.']
			];
			//echo json_encode($result); die();	
			$_SESSION['message'] = '1|'.Config::$langVars['Il tuo messaggio di conferma NON è stato spedito! Riprovaa.'];
			ToolsStrings::redirect(URL_SITE.Core::$request->action);		
		}

		// procedura completata
		$result = [
			'error' => 0,
			'message' => Config::$langVars['Il tuo messaggio è stato spedito! Riceverai un messaggio di conferma.']
		];
		//echo json_encode($result); die();
		$_SESSION['message'] = '0|'.Config::$langVars['Il tuo messaggio è stato spedito! Riceverai un messaggio di conferma.'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);

		die();

	break;

		
	default:

		/*
		// campi form passati da home
		$App->passform_name = (isset($_POST['name2']) && $_POST['name2'] != '' ? $_POST['name2'] : '');
		$App->passform_email = (isset($_POST['email2']) && $_POST['email2'] != '' ? $_POST['email2'] : '');
		$App->passform_message = (isset($_POST['message2']) && $_POST['message2'] != '' ? $_POST['message2'] : '');
		
		$App->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig,'text_intro_',$_lang['user'],array());
		$App->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'page_content_',$_lang['user'],array());
		$App->urlPrivacyPage = $App->contact_config->url_privacy_page ;
		$App->urlPrivacyPage = ToolsStrings::parseHtmlContent($App->urlPrivacyPage,array());		
		$contentString = '<div id="content-map">'.addslashes($globalSettings['azienda referente']).'</h3><p>'.addslashes($globalSettings['azienda indirizzo'].'<br>'.$globalSettings['azienda cap']).' '.$globalSettings['azienda comune'].'</p></div>';
	
		
		$messages_error_chars_nome = preg_replace('/%ITEM%/',$_lang['nome'],$_lang['Il %ITEM% deve essere più lungo di %CHAR% caratteri']);
		$messages_error_chars_nome = preg_replace('/%CHAR%/','10',$messages_error_chars_nome);
		
		$messages_error_chars_messaggio = preg_replace('/%ITEM%/',$_lang['messaggio'],$_lang['Il %ITEM% deve essere più lungo di %CHAR% caratteri']);
		$messages_error_chars_messaggio = preg_replace('/%CHAR%/','10',$messages_error_chars_messaggio);
	
		$App->addPageJavascriptIniBody .= "
			var contentString = '".$contentString."';
						
			var messages = new Array();
			messages['error confirm name'] = '".addslashes(preg_replace('/%ITEM%/',$_lang['nome'],$_lang['Devi inserire un %ITEM%!']))."';
			messages['error confirm email'] = '".addslashes(preg_replace('/%ITEM%/',$_lang['indirizzo email'],$_lang['Devi inserire un %ITEM%!']))."';
			messages['error confirm valid email'] = '".addslashes(preg_replace('/%ITEM%/',$_lang['indirizzo email valido'],$_lang['Devi inserire un %ITEM%!']))."';
			messages['error confirm object'] = '".addslashes(preg_replace('/%ITEM%/',$_lang['oggetto'],$_lang['Devi inserire un %ITEM%!']))."';
			messages['error confirm message'] = '".addslashes(preg_replace('/%ITEM%/',$_lang['messaggio'],$_lang['Devi inserire un %ITEM%!']))."';
			messages['error confirm captcha'] = '".addslashes($_lang['Devi verificare che non sei un robot!'])."';
			messages['error match captcha'] = '".addslashes($_lang['Sei stato identificato come robot!'])."';
			messages['error confirm privacy'] = '".addslashes($_lang['Devi autorizzare il trattamento della privacy!'])."';
			
			messages['error chars name'] = '".addslashes($messages_error_chars_nome)."';
			messages['error chars message'] = '".addslashes($messages_error_chars_messaggio)."';
					
			messages['message sent'] = 'Messaggio inviato';
			messages['message sent'] = 'attendi...';
			";
			*/


			$App->breadcrumbs->items[] = ['class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags((string) $App->moduleData->title)];				
			$App->breadcrumbs->title = $App->moduleData->title;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,['template'=>$templateBreadcrumbsBar]);	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage .= ' '.$App->moduleConfig->meta_description;
			$App->metaKeywordsPage .= $App->moduleConfig->meta_keywords;

			$App->meta_og_url = URL_SITE.Core::$request->action;
			$App->meta_og_type = 'website';
			$App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
			$App->meta_og_image = '';
			if ($App->moduleData->imageheader != '') $App->meta_og_image = UPLOAD_DIR.'tema/'.$App->moduleData->imageheader;
			$App->meta_og_description = $App->moduleConfig->meta_description;
	break;

}		


switch ($App->view) 
{
	default:	

	$App->jscriptCodeTop = "
		let recaptchakey = '".Config::$globalSettings['google recaptcha key']."';
		";

		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/css/pages/contacts.css" rel="stylesheet">';
		$App->jscript[] = '<script src="https://www.google.com/recaptcha/api.js?render='.Config::$globalSettings['google recaptcha key'].'"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/js/contacts.js"></script>';
	break;
}
?>