<?php
/* pages/default/newsletter.php v.1.0.0. 27/06/2016 */

use Soundasleep\Html2Text;

//Core::setDebugMode(1)

$App->moduleData = new stdClass();
$App->moduleConfig = new stdClass();

// preleva configurazione modulo
Sql::initQuery(DB_TABLE_PREFIX.'newsletter_config',array('*'),array(),'');	
$App->moduleConfig = Sql::getRecord();
//ToolsStrings::dump($App->moduleConfig);
$App->moduleConfig->title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
$App->moduleConfig->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig,'text_intro_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 
$App->moduleConfig->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig,'page_content_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1)); 

$App->moduleConfig->meta_title = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_title_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
$App->moduleConfig->meta_description = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_description_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
$App->moduleConfig->meta_keywords = Multilanguage::getLocaleObjectValue($App->moduleConfig,'meta_keywords_',Config::$langVars['user'],array('htmLawed'=>0,'parse'=>1));
//ToolsStrings::dump($App->moduleConfig);

// gestione titolo
$App->moduleData->title = 'F.A.Q.';
if ($App->moduleConfig->title != '') $App->moduleData->title = $App->moduleConfig->title;

// gestione innagine header
$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if ($App->moduleConfig->image_header != '') $App->moduleData->imageheader = $App->moduleConfig->image_header;
if ($App->moduleConfig->org_image_header != '') $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;

// carica configurrazione invio
$configs = array();
$configs[] = array('name'=>'user email address');
$configs[] = array('name'=>'user label email address');
$configs[] = array('name'=>'email user registration subject');
$configs[] = array('name'=>'email user registration content');
$configs[] = array('name'=>'email owner registration subject');
$configs[] = array('name'=>'email owner registration content');
$configs[] = array('name'=>'send owner notice user registration');
$configs[] = array('name'=>'url privacy page');
Config::checkModuleConfig(DB_TABLE_PREFIX.'newsletter_sendconfig',$configs);

$App->moduleConfig->url_privacy_page = ToolsStrings::parseHtmlContent(Config::$moduleConfig['url privacy page']->value_it,array());
//ToolsStrings::dump(Config::$moduleConfig);

$App->emailFrom = '';
if (isset($_POST['email'])) $App->emailFrom = $_POST['email'];
			
if (Core::$resultOp->error == 0) {
	switch (Core::$request->method) {	
		
		case 'delete':
			//ToolsStrings::dump(Core::$request);
			$App->subview = 'confirm';
			$App->item = new stdClass;
			$hash = (isset(Core::$request->param) && Core::$request->param != '' ? Core::$request->param : '');
			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',array('*'),array(Core::$request->param),'hash = ?');
			$App->item = Sql::getRecord();	
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			if ( !isset($App->item->id) || ( isset($App->item->id) && $App->item->id == 0) ) {
				$foo = Config::$langVars['Indirizzo email da cancellare non è presente!'];
				$result = array('error' => 0,'message' => $foo);
				//echo json_encode($result); die();
				$_SESSION['message'] = '1|'.$foo;
				$App->subview = 'confirm';
				break;
			}

			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',array('*'),array($App->item->id),'id = ?');
			Sql::deleteRecord();	
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// procedura completata
			$foo = Config::$langVars['Indirizzo email confermato!'];
			$result = array('error' => 0,'message' => $foo);
			//echo json_encode($result); die();
			$_SESSION['message'] = '0|'.$foo;
		break;
	
		case 'confirm':
			//ToolsStrings::dump(Core::$request);
			$App->subview = 'confirm';
			$hash = (isset(Core::$request->param) && Core::$request->param != '' ? Core::$request->param : '');
			//echo $hash;

			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',array('*'),array(Core::$request->param),'hash = ?');
			$App->item = Sql::getRecord();	
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			if ( !isset($App->item->id) || ( isset($App->item->id) && $App->item->id == 0) ) {
				$foo = Config::$langVars['Indirizzo email da confermare non è presente!'];
				$result = array('error' => 0,'message' => $foo);
				//echo json_encode($result); die();
				$_SESSION['message'] = '1|'.$foo;
				$App->subview = 'confirm';
				break;
			}

			if ($App->item->confirmed == 1) {
				$foo = Config::$langVars['Indirizzo email da confermare è già stato confermato!'];
				$result = array('error' => 0,'message' => $foo);
				//echo json_encode($result); die();
				$_SESSION['message'] = '0|'.$foo;
				$App->subview = 'confirm';
				break;
			}

			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',
				array('confirmed','dateconfirmed','active'),
				array(1,Config::$nowDateTimeIso,1,$App->item->id),'id = ?');
			Sql::updateRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// procedura completata
			$foo = Config::$langVars['Indirizzo email confermato!'];
			$result = array('error' => 0,'message' => $foo);
			//echo json_encode($result); die();
			$_SESSION['message'] = '0|'.$foo;
			$App->subview = 'confirm';

		break;

		case 'register':
			header('Content-Type: charset=utf-8');

			$fieldRif = 'value_'.$_lang['user'];
			// in caso di NON categoria la imposta a zero
			if (!isset($_POST['id_cat'])) $_POST['id_cat'] = '1';

			//ToolsStrings::dump($_POST);

			if (!isset($_POST['g-recaptcha-response'])) {
				$result = array(
					'error' => 1,
					'message' =>'aaaa'.Config::$langVars['Sei stato identificato come robot!']
				);
				//echo json_encode($result);
				//die();
				$_SESSION['message'] = '1|2222'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
			}
			
			$recaptcha = new \ReCaptcha\ReCaptcha($globalSettings['google recaptcha secret']);
			$resp = $recaptcha->verify($_POST['g-recaptcha-response'],$_SERVER['REMOTE_ADDR']);
			if (!$resp->isSuccess()) {
				$result = array(
					'error' => 1,
					'message' => 'bbbb'.Config::$langVars['Sei stato identificato come robot!']
				);
				//echo json_encode($result);
				//die();
				$_SESSION['message'] = '1|1111'.Config::$langVars['Sei stato identificato come robot!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
			}

			// controllo POST
			$fields = array(	
				'name'						=> array(	
					'required'					=> true,
					'name'						=> 'object',
					'error message'             => preg_replace('/%ITEM%/',Config::$langVars['nome'],Config::$langVars['Devi inserire un %ITEM%!'])
				),
				'surname'						=> array(	
					'required'					=> true,
					'name'						=> 'surname',
					'error message'             => preg_replace('/%ITEM%/',Config::$langVars['cognome'],Config::$langVars['Devi inserire un %ITEM%!'])
				),
				'email'						=> array(	
					'required'					=> true,
					'field'						=> 'email',
					'error message'             => preg_replace('/%ITEM%/',Config::$langVars['indirizzo email valido'],Config::$langVars['Devi inserire un %ITEM%!']),
					'validate'					=> 'isemail',
				),
				'privacy'						=> array(	
					'required'					=> true,
					'field'						=> 'privacy',
					'error message'             => Config::$langVars['Devi autorizzare il trattamento della privacy!'],
					'validate'					=> 'issameintvalue',
					'valuerif'					=> 1
				),
				
			);
			Form::parsePostByFields($fields,Config::$langVars,array('stripmagicfields'=>false));
			//ToolsStrings::dump(Core::$resultOp);
			if (Core::$resultOp->error > 0) {
				$result = array('error' => 1,'message' => implode('<br>',Core::$resultOp->messages));
				//echo json_encode($result);
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
			}

			// controlla se l'email è gia registrata
			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',array('id'),array($_POST['email']),'email = ?');
			$count = Sql::countRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$foo =  Config::$langVars['Errore! Indirizzo email è già presente nel nostro database! Sei pregato di contattare amministratore!'];
				$result = array('error' => 1,'message' => $foo);
				//echo json_encode($result); die();
				$_SESSION['message'] = '0|'.$foo;
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
			};

			$hash = md5(SITE_CODE_KEY.$_POST['name'].$_POST['email'].$_POST['surname']);
			$urlConfirm = rtrim(URL_SITE.Config::$moduleConfig['admin url confirm address']->$fieldRif,'/').'/'.$hash;

			// invia email gestore sito 
			if (Config::$moduleConfig['send owner notice user registration']->$fieldRif == 1) {
				
				$subject = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email owner registration subject'],'value_',$_lang['user'],array());
				$subject = Mails::parseMailContent($_POST,$subject,$optt=array());
				$subject = ToolsStrings::parseHtmlContent($subject,array());
				$content = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email owner registration content'],'value_',$_lang['user'],array());
				$content = Mails::parseMailContent($_POST,$content,$optt=array());
				$content = ToolsStrings::parseHtmlContent($content,array());
			   	$content_plain = Html2Text::convert($content);

				/*
				echo '<br>'.$subject;
				echo '<br>'.$content;
				echo '<br>'.$content_plain;
				//die();
				*/
				
				$opt = array();
				//FIXED - DKIM requirements
			   	$opt['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? Config::$moduleConfig['user email address']->$fieldRif;
			   	$opt['from label'] = Config::$moduleConfig['user label email address']->$fieldRif;
				$opt['replyTo'] = [$_POST['email']];
			   	$address = Config::$moduleConfig['admin email address']->$fieldRif;				
			   	$opt['send copy'] = Config::$moduleConfig['send emails for debug']->$fieldRif;
			   	$opt['send copy email'] = Config::$moduleConfig['email address for debug']->$fieldRif;

				//ToolsStrings::dump(Config::$moduleConfig);
				//ToolsStrings::dump($opt);
	
			   	Mails::sendEmail($address,$subject,$content,$content_plain,$opt);	
				if (Core::$resultOp->error > 0) {
					$foo = Config::$langVars['Errore server! Non è possibile inviare email! Sei pregato di contattare amministratore!'];
					$result = array('error' => 1,'message' => $foo);
					//echo json_encode($result); die();
					$_SESSION['message'] = '1|'.$foo;
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
				}			 
			}

			// invia email utente */
			$subject = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email user registration subject'],'value_',$_lang['user'],array());
			$subject = Mails::parseMailContent($_POST,$subject,$optt=array());
			$subject = ToolsStrings::parseHtmlContent($subject,array());
			$content = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email user registration content'],'value_',$_lang['user'],array());
			$content = Mails::parseMailContent($_POST,$content,$optt=array());
			$content = ToolsStrings::parseHtmlContent($content,array());
			$content = preg_replace('/%URLCONFIRM%/',$urlConfirm,$content);
			$content_plain = Html2Text::convert($content);

			/*
			echo '<br>'.$subject;
			echo '<br>'.$content;
			echo '<br>'.$content_plain;
			//die();
			*/
		
			//FIXED - DKIM requirements
			$opt['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? Config::$moduleConfig['user email address']->$fieldRif;
			$opt['from label'] = Config::$moduleConfig['user label email address']->$fieldRif;
			$opt['replyTo'] = [Config::$moduleConfig['user email address']->$fieldRif => Config::$moduleConfig['user label email address']->$fieldRif];
			$address = $_POST['email'];				
			$opt['send copy'] = Config::$moduleConfig['send emails for debug']->$fieldRif;
			$opt['send copy email'] = Config::$moduleConfig['email address for debug']->$fieldRif;
			Mails::sendEmail($address,$subject,$content,$content_plain,$opt);	
			if (Core::$resultOp->error > 0) {
				$foo = Config::$langVars['Errore server! Non è possibile inviare email! Sei pregato di contattare amministratore!'];
				$result = array('error' => 1,'message' => $foo);
				//echo json_encode($result); die();
				$_SESSION['message'] = '1|'.$foo;
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
			}
			//ToolsStrings::dump($opt);

			// memorizza nel db
			Config::$debugMode = 1;
			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi',
			array('name','surname','email','hash','language','coda_invio','language_invio','confirmed','dateconfirmed','created','active'),
			array($_POST['name'],$_POST['surname'],$_POST['email'],$hash,$_lang['user'],0,Config::$langVars['user'],0,Config::$nowDateTimeIso,Config::$nowDateTimeIso,1)
			);
			Sql::insertRecord();	
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$id_item = Sql::getLastInsertedIdVar();

			// salva riferimenti categoria
			Sql::initQuery(DB_TABLE_PREFIX.'newsletter_cat_ind',array('id_cat','id_ind'),array(intval($_POST['id_cat']),$id_item));
			Sql::insertRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }			
			
			// procedura completata
			$foo = Config::$langVars['La tua richiesta di iscrizione è stata inviata!'];
			$result = array('error' => 0,'message' => $foo);
			//echo json_encode($result); die();
			$_SESSION['message'] = '0|'.$foo;
			$App->subview = 'register';

		break;

		default:
			$App->fromFormEmail = (isset($_POST['fromFormEmail']) ? $_POST['fromFormEmail'] : '');
			$App->breadcrumbs->items[] = array('class'=>'breadcrumb-item active','url'=>'','title'=>strip_tags($App->moduleData->title));				
			$App->breadcrumbs->title = $App->moduleData->title;
			$App->breadcrumbs->tree =  Utilities:: generateBreadcrumbsTree($App->breadcrumbs->items,$_lang,array('template'=>$templateBreadcrumbsBar));	
			
			$App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
			$App->metaDescriptionPage = $App->moduleConfig->meta_description;
			$App->metaKeywordsPage = $App->moduleConfig->meta_keywords;

			$App->meta_og_url = URL_SITE.Core::$request->action;
			$App->meta_og_type = 'website';
			$App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
			$App->meta_og_image = '';
			if ($App->moduleData->imageheader != '') $App->meta_og_image = UPLOAD_DIR.'tema/'.$App->moduleData->imageheader;
			$App->meta_og_description = $App->moduleConfig->meta_description;

			$App->jscript[] = '<script src="https://www.google.com/recaptcha/api.js"></script>';	
			$App->view = '';
		break;
		}
}	
	

//echo $App->view;

switch ($App->view) {
	case 'confirm':
		$App->templateApp = 'newsletter-confirm';
	break;
	
	case 'delete':
		$App->templateApp = 'newsletter-confirm';
	break;

	
	default:
		$App->urlPrivacyPage = Config::$moduleConfig['url privacy page']->value_it;
		$App->urlPrivacyPage = preg_replace('/%URLSITE%/',URL_SITE,$App->urlPrivacyPage);
	break;	
}
