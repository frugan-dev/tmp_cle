<?php
/* vendors.php v.3.5.3. 19/06/2018 */

$App->view = 'error';

/* se ha dati pagina li carica */
$App->modulePageData = new stdClass();
Sql::initQuery(Sql::getTablePrefix().'pages',['*'],['campagne'],'active = 1 AND (alias LIKE ?)');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->modulePageData = $obj;

$App->breadcrumbs = new stdClass();
$App->breadcrumbs->items = [];
$App->breadcrumbs->items[] = ['class'=>'','url'=>URL_SITE,'title'=>ucfirst((string) $_lang['home'])];

/* gestione immagine top e bottom pagina */
$App->modulePageData->image_top =  UPLOAD_DIR.'pages/default/default-image-top-pages.jpg';
$App->modulePageData->image_bottom = UPLOAD_DIR.'pages/default/default-image-bottom-pages.jpg';
if (is_object($App->modulePageData)) {
	if (isset($App->modulePageData->filename) && $App->modulePageData->filename != '') $App->modulePageData->image_top =  UPLOAD_DIR.'pages/'.$App->modulePageData->filename;
	if (isset($App->modulePageData->filename1) && $App->modulePageData->filename1 != '') $App->modulePageData->image_bottom =  UPLOAD_DIR.'pages/'.$App->modulePageData->filename1;
	}
/* gestione titoli pagina */ 
$App->titles = Utilities::getTitlesPage(ucfirst((string) $_lang['fiere']),$App->modulePageData,$_lang['user'],[]);


$itemsForPage = 9;
$App->page = Core::$request->page;
$id_state = 0;

$App->listStates = true;
$App->listVendors = false;
$listDetails = false;
$mapMaxZoom = 9;

	
if (Core::$resultOp->error == 0) {
	switch (Core::$request->method) {		
		case 'sendajax':
			if ($_POST['captcha'] == $_MY_SESSION_VARS['site']['captcha_id']) {
				if ($_POST['company'] != '' || $_POST['name'] != '' || $_POST['surname'] != '' || $_POST['email'] != '' || $_POST['message'] != '') {

					/* preleva l'indirizzo email della rete vendita */
					/* se esiste un indirizzo email */
					/* preleva i dati della voce */	
					$id = ($_POST['id'] ?? 0);
					Sql::initQuery(DB_TABLE_PREFIX.'vendors',['*'],[$id],'active = 1 AND id = ?');
					$App->item = Sql::getRecord();
					if (Core::$resultOp->error == 0) {
						if ((isset($App->item->email) && $App->item->email != '') && (isset($_POST['email']) && $_POST['email'] != '')) {
							//$App->item->email = 'robyfofo@gmail.com'; // per debug
							
							/* manda email alla sede */
							$subject = 'Messaggio inviato dal modulo contatti Rete Vendita del sito MBF';
							$content = Config::$moduleConfig['email content']->value_it;
							$content = preg_replace('/{COMPANY}/',(string) $_POST['company'],(string) $content);
							$content = preg_replace('/{NAME}/',(string) $_POST['name'],(string) $content);
							$content = preg_replace('/{SURNAME}/',(string) $_POST['surname'],(string) $content);
							$content = preg_replace('/{EMAIL}/',(string) $_POST['email'],(string) $content);
							$content = preg_replace('/{TELEPHONE}/',(string) $_POST['telephone'],(string) $content);	
							$content = preg_replace('/{OBJECT}/',(string) $_POST['object'],(string) $content);	
							$content = preg_replace('/{MESSAGE}/',(string) $_POST['message'],(string) $content);				
							
							$opt = [];
							$opt['fromEmail'] = $_POST['email'];
							$opt['fromLabel'] = $_POST['email'];
							
							if (Config::$moduleConfig['send copy email for debug']->value_it == 1) {
								if (Config::$moduleConfig['email address for debug']->value_it != '') {
									$opt['sendDebug'] = 1;
									$opt['sendDebugEmail'] = Config::$moduleConfig['email address for debug']->value_it;
								}
							}
							
							$textContent = strip_tags((string) $content);
							Mails::sendEmail(Config::$moduleConfig['email address']->value_it, $subject, $content, $textContent, $opt);
							
							if (Core::$resultOp->error == 0) {					
								/* manda la email alla rete vendita */
								$subject = 'Messagge sent from contacts module Rete Vendita of MBF site';
								$content = "Was sent a message from contacts module Rete Vendita of MBF site.<br><br><b>Company:</b> {COMPANY}<br><b>Name:</b> {NAME}<br><b>Surname:</b> {SURNAME}<br><b>Email:</b> {EMAIL}<br><b>Telephone:</b> {TELEPHONE}<br><br><b>Object:</b> {OBJECT}<br><br><b>Message:</b><br>{MESSAGE}";
								$content = preg_replace('/{COMPANY}/',(string) $_POST['company'],$content);
								$content = preg_replace('/{NAME}/',(string) $_POST['name'],(string) $content);
								$content = preg_replace('/{SURNAME}/',(string) $_POST['surname'],(string) $content);
								$content = preg_replace('/{EMAIL}/',(string) $_POST['email'],(string) $content);
								$content = preg_replace('/{TELEPHONE}/',(string) $_POST['telephone'],(string) $content);	
								$content = preg_replace('/{OBJECT}/',(string) $_POST['object'],(string) $content);	
								$content = preg_replace('/{MESSAGE}/',(string) $_POST['message'],(string) $content);	
								
								$opt = [];
								$opt['fromEmail'] = $_POST['email'];
								$opt['fromLabel'] = $_POST['email'];
								
								if (Config::$moduleConfig['send copy email for debug']->value_it == 1) {
									if (Config::$moduleConfig['email address for debug']->value_it != '') {
										$opt['sendDebug'] = 1;
										$opt['sendDebugEmail'] = Config::$moduleConfig['email address for debug']->value_it;
									}
								}
								
								$textContent = strip_tags((string) $content);
								Mails::sendEmail($App->item->email, $subject, $content, $textContent, $opt);
								
								if (Core::$resultOp->error == 0) {
									/* manda la email di conferma all'utente */							
									$subject = Config::$moduleConfig['user email subject']->value_it;
									$content = Config::$moduleConfig['user email content']->value_it;					
									
									$opt = [];
									$opt['fromEmail'] = $App->item->email;
									$opt['fromLabel'] = $App->item->email;
									
									if (Config::$moduleConfig['send copy email for debug']->value_it == 1) {
										if (Config::$moduleConfig['email address for debug']->value_it != '') {
											$opt['sendDebug'] = 1;
											$opt['sendDebugEmail'] = Config::$moduleConfig['email address for debug']->value_it;
										}
									}
									
									$textContent = strip_tags((string) $content);
									Mails::sendEmail($_POST['email'], $subject, $content, $textContent, $opt);
								}
							}
						} else {
							echo 'email rete vendita non presente!';
						}
					}
					
				} else {
					Core::$resultOp->error = 1;
					echo $_lang['Devi inserire tutti i campi richiesti!'];
				}
			} else {
				Core::$resultOp->error = 1;
				echo $_lang['Captcha - I caratteri inseriti non corrispondono all immagine!'];
				}

			die();
		break;
		case 'vendor':
			$App->urlPrivacyPage = Config::$moduleConfig['url privacy page']->value_it;
			$App->urlPrivacyPage = preg_replace('/{URLSITE}/',URL_SITE,(string) $App->urlPrivacyPage);
			$id = intval($App->param);
			if($id > 0) {	
				if (isset($App->params[0]))	$App->targaStato = $App->params[0];
				/* preleva i dati della voce */	
				Sql::initQuery(DB_TABLE_PREFIX.'vendors',['*'],[$id],'active = 1 AND id = ?');
				$App->item = Sql::getRecord();
				//print_r($App->item);
				if (Core::$resultOp->error == 0) {
					if (isset($App->item->id)) {
						//print_r($App->item);
						$latVendor = $App->item->latitude;
						$lonVendor = $App->item->longitude;
						
						//echo $App->item->id_state; 
						/* preleva la latitudine e la longitudine del paese */
						Sql::initQuery(DB_TABLE_PREFIX.'states',['*'],[$App->item->id_state],'active = 1 AND id = ?');
						$App->statedetails = Sql::getRecord();
						
						//print_r($App->statedetails);
						
						if (Core::$resultOp->error == 0) {
							//print_r($App->statedetails);
							//$latState = $App->statedetails->latitude;
							//$lonState = $App->statedetails->longitude;
							$latState = 0;
							$lonState = 0;								
							$lat = '0'; // default
							$lon = '0'; // default							
							if ($lonVendor != '') {
								$lon = $lonVendor;
								} else if ($lonState != 0) {
									$lon = $lonState;
									}							
							if ($latVendor != '') {
								$lat = $latVendor;
								} else if($latState != 0) {
									$lat = $latState;
									}
							$App->view = 'vendor';									
							}
						}
					}
				}	
		
		break;
		
		case 'state':
		$targa = trim((string) $App->param);
		$App->listVendors = true;
		$App->listStates = false;
		
		default:
			//Sql::setDebugMode(1);
			$fieldsValue = [];
			$where = 'active = 1';				
			if (isset($targa) && $targa != '') {
				$App->targaStato = $targa;
				$targa1 = '%'.$targa.',%';
				//$targa1 = $targa;
				
				//echo 'targa :'.$targa;
				//echo 'targa1 :'.$targa1;
				
				/* prendo i deaatali dello stato */
				Sql::initQuery(DB_TABLE_PREFIX.'states',['*'],[$targa],'active = 1 AND targa = ?');
				$App->statedetails = Sql::getRecord();
				
//print_r($App->statedetails);
				
				/* prendo id vendor da targa */
				Sql::initQuery(DB_TABLE_PREFIX.'vendors',['*'],[$targa1],'active = 1 AND id_stati LIKE ?');
				Sql::setOrder('name ASC');
				$App->vendors1 = Sql::getRecords();
				if (Core::$resultOp->error == 0) {
				
					}						
				}		
				
//print_r($App->vendors1);	
			
			if (Core::$resultOp->error == 0) {
				
//print_r($fieldsValue);
				
//echo 'where :'.$where;
				/* preleva i venditori */
				Sql::initQuery(DB_TABLE_PREFIX.'vendors',['*'],$fieldsValue,$where);
				Sql::setOrder('name ASC');
				$App->vendors = Sql::getRecords();		

//print_r($App->vendors);
		
				if (Core::$resultOp->error == 0) {						
					if ($App->listStates == true) {
						
						$App->vendorsList = [];
						if (is_array($App->vendors) && count($App->vendors) > 0) {
							foreach ($App->vendors AS $value) {
								$App->vendorsList[] = $value;
								}
							}
						/* preleva tutti gli stati (targhe) associati ai rivenditori */
						Sql::initQuery(DB_TABLE_PREFIX.'vendors',['id_stati'],[],'active = 1');
						$App->statesVendors = Sql::getRecords();						
						if (Core::$resultOp->error == 0) {
							$App->vendors = $App->statesVendors;
							$strStati = '';
							if (is_array($App->vendors) && count($App->vendors) > 0) {
								foreach ($App->vendors AS $value) {
									if ($value->id_stati != '') $strStati .= $value->id_stati.',';
									}
								}
							$strStati = rtrim($strStati,',');
							$statiArr = explode(',',$strStati);
							$statiArr = array_unique($statiArr);
							/* preleva tutti gli stati corrispondenti */
							/* crea la where */
							$where1 = 'active = 1';
							$where2 = '';
							if (is_array($statiArr) && count($statiArr) > 0) {
								foreach ($statiArr AS $value) {
									$where2 .= " targa = '".$value."' OR ";
									}
								}
					
							if ($where2 != '') $where1 = $where1.' AND ('.rtrim($where2,' OR ').')';

							Sql::initQuery(DB_TABLE_PREFIX.'states',['*'],[],$where1);
							Sql::setOrder('title_'.$_lang['user'].' ASC');
							$App->statesList = Sql::getRecords();
							
							if (Core::$resultOp->error == 0) {	
								
								}
							}																
						}	
				
					if ($App->listVendors == true) {						
						$App->vendorsList = [];
						if (is_array($App->vendors1) && count($App->vendors1) > 0) {
							foreach ($App->vendors1 AS $value) {
								$App->vendorsList[] = $value;
								}
							}	
						}

					if (Core::$resultOp->error == 0) {	
						$centriJsc = '';
						if (is_array($App->vendorsList) && count($App->vendorsList) > 0) {
							foreach ($App->vendorsList AS $value) {	
								$note = '';
								$note .= '<a href="'.URL_SITE.'/vendors/vendor/'.$value->id.'" title="Scheda">'.$value->name.'</a><br>';
								if ($value->url != '') {
									$note .= '<a href="http:://'.$value->url.'" title="Vai al sito">'.$value->url.'</a>';
									}
								if($value->latitude != '' && $value->longitude != '') $centriJsc .= "['".$value->name."', ".$value->latitude.",".$value->longitude.",3,'".$note."'],";
								}
								$centriJsc = rtrim($centriJsc,',');
							}	
				
						}		
					}
				}

		break;
			
		}
	}
		
/* SEZIONE VIEW */

if (Core::$resultOp->error == 1) $App->view = 'error';
if (Core::$resultOp->error == 2) $App->view = '404';

switch ($App->view) {	
	default:
		//$optionalTplPage = 'products-details';
		$App->codeJavascriptInitBody = 'var sites = ['.$centriJsc.'];';
		$App->codeJavascriptInitBody .= "var mapMaxZoom = ".$mapMaxZoom.";".PHP_EOL;
		$App->pageJscript[] = '<script type="text/javascript" src="https://maps.google.com/maps/api/js?key='.Config::$globalSettings['google_map_api_key'].'&sensor=false"></script>';
		
		if ($App->listStates == true) {			
			$App->pageJscriptLast[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/js/pages/page_vendors.js"></script>';
			}
		if ($App->listVendors == true) {
			$App->pageJscriptLast[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/js/pages/page_vendors1.js"></script>';			
			}

		$App->addPageCss[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/css/pages/page_vendors.css">';
	break;

	case 'vendor':
		// Create a random string, leaving out 'o' to avoid confusion with '0'
		$char = strtoupper(substr(str_shuffle(Config::$globalSettings['session_random_key'] ?? md5()), 0, 4));
		$str = random_int(1, 7) . random_int(1, 7) . $char;
		$_MY_SESSION_VARS =  $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,'site','captcha_id',$str);

		$mapMaxZoom = 7;
		$App->codeJavascriptInitBody = "
		var latitude = ".$lat.";".PHP_EOL."
		var longitude = ".$lon.";".PHP_EOL."
		var markerTitle = '".ToolsStrings::html_out($App->item->name)."';".PHP_EOL."
		var formMessCompany = '".$_lang['Devi inserire una azienda!']."';".PHP_EOL."
		var formMessName = '".$_lang['Devi inserire un nome!']."';".PHP_EOL."	
		var formMessSurname = '".$_lang['Devi inserire un cognome!']."';".PHP_EOL."
		var formMessEmail = '".$_lang['Devi inserire un indirizzo email!']."';".PHP_EOL."
		var formMessEmailValid = '".$_lang['Devi inserire un indirizzo email valido!']."';".PHP_EOL."
		var formMessObject = '".$_lang['Devi inserire un oggetto!']."';".PHP_EOL."
		var formMessMessage = '".$_lang['Prego inserisci il tuo messaggio.']."';".PHP_EOL."
		var formMessPrivacy = '".addslashes((string) $_lang['Devi autorizzare il trattamento della privacy!'])."';
			
		var formMessCaptcha = '".addslashes((string) $_lang['Captcha - Inserisci i caratteri presenti nell immagine.'])."';
	    var formMessCaptchaRequired = '".addslashes((string) $_lang['Captcha - I caratteri inseriti non corrispondono all immagine!'])."';
		";

		
		$App->codeJavascriptInitBody .= "var mapMaxZoom = ".$mapMaxZoom.";".PHP_EOL;		
		
		$App->errorPage = 0;
		$App->titlePage = ucwords((string) $_lang['rete vendita']).' '.ToolsStrings::html_out($App->item->name);
		$optionalTplPage = 'vendors-vendordetails';
		$App->pageJscript[] = '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>';
		$App->pageJscript[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/plugins/gmap/gmap.js"></script>';
		$App->pageJscript[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>';
		$App->pageJscript[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>';

		$App->pageJscriptLast[] = '<script type="text/javascript" src="'.URL_SITE.'templates/'.$App->templateUser.'/assets/js/pages/page_vendordetails.js"></script>';
		$App->addPageCss[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/plugins/sky-forms-pro/skyforms/css/sky-forms.css">';
		$App->addPageCss[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/plugins/sky-forms-pro/skyforms/custom/custom-sky-forms.css">';
		
		$App->addPageCss[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/css/pages/page_vendors.css">';							
		$App->addPageCss[] = '<link rel="stylesheet" href="'.URL_SITE.'templates/'.$App->templateUser.'/assets/css/pages/page_contact.css">';							
	break;	

	case '404':	
		if (file_exists(PATH."pages/404.php")) {
			include_once(PATH."pages/404.php");	
			}
		Core::$request->action = '404';
	break;
	case 'error':
		Core::$request->action = 'errorpage';
		if (Core::$resultOp->message == '') Core::$resultOp->message = $_lang['sono presenti uno o più errori!'];
		if (file_exists(PATH."pages/errorpage.php")) {
			include_once(PATH."pages/errorpage.php");	
			}
	break;
	}
?>