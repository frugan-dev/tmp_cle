<?php

/* pages/default/newsletter.php v.1.0.0. 27/06/2016 */

use Soundasleep\Html2Text;
use ReCaptcha\ReCaptcha;

//Core::setDebugMode(1)

$App->moduleData = new stdClass();
$App->moduleConfig = new stdClass();

// preleva configurazione modulo
Sql::initQuery(DB_TABLE_PREFIX.'newsletter_config', ['*'], [], '');
$App->moduleConfig = Sql::getRecord();
//ToolsStrings::dump($App->moduleConfig);
$App->moduleConfig->title = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'title_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
$App->moduleConfig->text_intro = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'text_intro_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
$App->moduleConfig->page_content = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'page_content_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);

$App->moduleConfig->meta_title = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'meta_title_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
$App->moduleConfig->meta_description = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'meta_description_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
$App->moduleConfig->meta_keywords = Multilanguage::getLocaleObjectValue($App->moduleConfig, 'meta_keywords_', Config::$langVars['user'], ['htmLawed' => 0,'parse' => 1]);
//ToolsStrings::dump($App->moduleConfig);

// gestione titolo
$App->moduleData->title = 'F.A.Q.';
if ($App->moduleConfig->title != '') {
    $App->moduleData->title = $App->moduleConfig->title;
}

// gestione innagine header
$App->moduleData->imageheader = '';
$App->moduleData->orgImageheader = '';
if ($App->moduleConfig->image_header != '') {
    $App->moduleData->imageheader = $App->moduleConfig->image_header;
}
if ($App->moduleConfig->org_image_header != '') {
    $App->moduleData->orgImageheader = $App->moduleConfig->org_image_header;
}

// carica configurrazione invio
$configs = [];
$configs[] = ['name' => 'user email address'];
$configs[] = ['name' => 'user label email address'];
$configs[] = ['name' => 'email user registration subject'];
$configs[] = ['name' => 'email user registration content'];
$configs[] = ['name' => 'email owner registration subject'];
$configs[] = ['name' => 'email owner registration content'];
$configs[] = ['name' => 'send owner notice user registration'];
$configs[] = ['name' => 'url privacy page'];
Config::checkModuleConfig(DB_TABLE_PREFIX.'newsletter_sendconfig', $configs);

$App->moduleConfig->url_privacy_page = ToolsStrings::parseHtmlContent(Config::$moduleConfig['url privacy page']->value_it, []);
//ToolsStrings::dump(Config::$moduleConfig);

$App->emailFrom = '';
if (isset($_POST['email'])) {
    $App->emailFrom = $_POST['email'];
}

if (Core::$resultOp->error == 0) {
    switch (Core::$request->method) {

        case 'delete':
            //ToolsStrings::dump(Core::$request);
            $App->subview = 'confirm';
            $App->item = new stdClass();
            $hash = (isset(Core::$request->param) && Core::$request->param != '' ? Core::$request->param : '');
            Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi', ['*'], [Core::$request->param], 'hash = ?');
            $App->item = Sql::getRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0)) {
                $foo = Config::$langVars['Indirizzo email da cancellare non è presente!'];
                $result = ['error' => 0,'message' => $foo];
                //echo json_encode($result); die();
                $_SESSION['message'] = '1|'.$foo;
                $App->subview = 'confirm';
                break;
            }

            Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi', ['*'], [$App->item->id], 'id = ?');
            Sql::deleteRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            // procedura completata
            $foo = Config::$langVars['Indirizzo email confermato!'];
            $result = ['error' => 0,'message' => $foo];
            //echo json_encode($result); die();
            $_SESSION['message'] = '0|'.$foo;
            break;

        case 'confirm':
            //ToolsStrings::dump(Core::$request);
            $App->subview = 'confirm';
            $hash = (isset(Core::$request->param) && Core::$request->param != '' ? Core::$request->param : '');
            //echo $hash;

            Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi', ['*'], [Core::$request->param], 'hash = ?');
            $App->item = Sql::getRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0)) {
                $foo = Config::$langVars['Indirizzo email da confermare non è presente!'];
                $result = ['error' => 0,'message' => $foo];
                //echo json_encode($result); die();
                $_SESSION['message'] = '1|'.$foo;
                $App->subview = 'confirm';
                break;
            }

            if ($App->item->confirmed == 1) {
                $foo = Config::$langVars['Indirizzo email da confermare è già stato confermato!'];
                $result = ['error' => 0,'message' => $foo];
                //echo json_encode($result); die();
                $_SESSION['message'] = '0|'.$foo;
                $App->subview = 'confirm';
                break;
            }

            Sql::initQuery(
                DB_TABLE_PREFIX.'newsletter_indirizzi',
                ['confirmed','dateconfirmed','active'],
                [1,Config::$nowDateTimeIso,1,$App->item->id],
                'id = ?'
            );
            Sql::updateRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            // procedura completata
            $foo = Config::$langVars['Indirizzo email confermato!'];
            $result = ['error' => 0,'message' => $foo];
            //echo json_encode($result); die();
            $_SESSION['message'] = '0|'.$foo;
            $App->subview = 'confirm';

            break;

        case 'register':
            header('Content-Type: charset=utf-8');

            $fieldRif = 'value_'.$_lang['user'];
            // in caso di NON categoria la imposta a zero
            if (!isset($_POST['id_cat'])) {
                $_POST['id_cat'] = '1';
            }

            //ToolsStrings::dump($_POST);

            /*if (!isset($_POST['g-recaptcha-response'])) {
                $result = [
                    'error' => 1,
                    'message' => 'aaaa'.Config::$langVars['Sei stato identificato come robot!'],
                ];
                //echo json_encode($result);
                //die();
                $_SESSION['message'] = '1|2222'.implode('<br>', Core::$resultOp->messages);
                ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
            }

            $recaptcha = new ReCaptcha($globalSettings['google recaptcha secret']);
            $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if (!$resp->isSuccess()) {
                $result = [
                    'error' => 1,
                    'message' => 'bbbb'.Config::$langVars['Sei stato identificato come robot!'],
                ];
                //echo json_encode($result);
                //die();
                $_SESSION['message'] = '1|1111'.Config::$langVars['Sei stato identificato come robot!'];
                ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
            }*/

            // controllo recaptcha
            if (!isset($_POST['recaptcha_response'])) {
                echo json_encode(['error' => 1,'message' => 'Recacptcha mancante! Il sistema ti ha identificato come robot!']);
                die();
            } else {
                $captcha = $_POST['recaptcha_response'];
                $secret = $globalSettings['google recaptcha secret'];
                $json = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='. $secret . '&response=' .   $captcha), true);

                if (!$json['success']) {
                    echo json_encode(['error' => 1,'message' => 'Recacptcha! Il sistema ti ha identificato come robot!']);
                    die();
                }
            }
            // controllo recaptcha

            // controllo POST
            $fields = [
                'name'						=> [
                    'required'					=> true,
                    'name'						=> 'object',
                    'error message'             => preg_replace('/%ITEM%/', (string) Config::$langVars['nome'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
                ],
                'surname'						=> [
                    'required'					=> true,
                    'name'						=> 'surname',
                    'error message'             => preg_replace('/%ITEM%/', (string) Config::$langVars['cognome'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
                ],
                'email'						=> [
                    'required'					=> true,
                    'field'						=> 'email',
                    'error message'             => preg_replace('/%ITEM%/', (string) Config::$langVars['indirizzo email valido'], (string) Config::$langVars['Devi inserire un %ITEM%!']),
                    'validate'					=> 'isemail',
                ],
                'privacy'						=> [
                    'required'					=> true,
                    'field'						=> 'privacy',
                    'error message'             => Config::$langVars['Devi autorizzare il trattamento della privacy!'],
                    'validate'					=> 'issameintvalue',
                    'valuerif'					=> 1,
                ],

            ];
            Form::parsePostByFields($fields, Config::$langVars, ['stripmagicfields' => false]);
            //ToolsStrings::dump(Core::$resultOp);
            if (Core::$resultOp->error > 0) {
                $result = ['error' => 1,'message' => implode('<br>', Core::$resultOp->messages)];
                //echo json_encode($result);
                $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
                ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
            }

            // controlla se l'email è gia registrata
            Sql::initQuery(DB_TABLE_PREFIX.'newsletter_indirizzi', ['id'], [$_POST['email']], 'email = ?');
            $count = Sql::countRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }
            if ($count > 0) {
                $foo =  Config::$langVars['Errore! Indirizzo email è già presente nel nostro database! Sei pregato di contattare amministratore!'];
                $result = ['error' => 1,'message' => $foo];
                //echo json_encode($result); die();
                $_SESSION['message'] = '0|'.$foo;
                ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
            };

            $hash = md5(SITE_CODE_KEY.$_POST['name'].$_POST['email'].$_POST['surname']);
            $urlConfirm = rtrim(URL_SITE.Config::$moduleConfig['admin url confirm address']->$fieldRif, '/').'/'.$hash;

            // invia email gestore sito
            if (Config::$moduleConfig['send owner notice user registration']->$fieldRif == 1) {

                $subject = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email owner registration subject'], 'value_', $_lang['user'], []);
                $subject = Mails::parseMailContent($_POST, $subject, $optt = []);
                $subject = ToolsStrings::parseHtmlContent($subject, []);
                $content = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email owner registration content'], 'value_', $_lang['user'], []);
                $content = Mails::parseMailContent($_POST, $content, $optt = []);
                $content = ToolsStrings::parseHtmlContent($content, []);
                $content_plain = Html2Text::convert($content);

                /*
                echo '<br>'.$subject;
                echo '<br>'.$content;
                echo '<br>'.$content_plain;
                //die();
                */

                $opt = [];
                //FIXED - DKIM requirements
                $opt['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? Config::$moduleConfig['user email address']->$fieldRif;
                $opt['from label'] = Config::$moduleConfig['user label email address']->$fieldRif;
                $opt['replyTo'] = [$_POST['email']];
                $address = Config::$moduleConfig['admin email address']->$fieldRif;
                $opt['send copy'] = Config::$moduleConfig['send emails for debug']->$fieldRif;
                $opt['send copy email'] = Config::$moduleConfig['email address for debug']->$fieldRif;

                //ToolsStrings::dump(Config::$moduleConfig);
                //ToolsStrings::dump($opt);

                Mails::sendEmail($address, $subject, $content, $content_plain, $opt);
                if (Core::$resultOp->error > 0) {
                    $foo = Config::$langVars['Errore server! Non è possibile inviare email! Sei pregato di contattare amministratore!'];
                    $result = ['error' => 1,'message' => $foo];
                    //echo json_encode($result); die();
                    $_SESSION['message'] = '1|'.$foo;
                    ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
                }
            }

            // invia email utente */
            $subject = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email user registration subject'], 'value_', $_lang['user'], []);
            $subject = Mails::parseMailContent($_POST, $subject, $optt = []);
            $subject = ToolsStrings::parseHtmlContent($subject, []);
            $content = Multilanguage::getLocaleObjectValue(Config::$moduleConfig['email user registration content'], 'value_', $_lang['user'], []);
            $content = Mails::parseMailContent($_POST, $content, $optt = []);
            $content = ToolsStrings::parseHtmlContent($content, []);
            $content = preg_replace('/%URLCONFIRM%/', $urlConfirm, (string) $content);
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
            Mails::sendEmail($address, $subject, $content, $content_plain, $opt);
            if (Core::$resultOp->error > 0) {
                $foo = Config::$langVars['Errore server! Non è possibile inviare email! Sei pregato di contattare amministratore!'];
                $result = ['error' => 1,'message' => $foo];
                //echo json_encode($result); die();
                $_SESSION['message'] = '1|'.$foo;
                ToolsStrings::redirect(URL_SITE.Core::$request->action.'#systemMessageID');
            }
            //ToolsStrings::dump($opt);

            // memorizza nel db
            Config::$debugMode = 1;
            Sql::initQuery(
                DB_TABLE_PREFIX.'newsletter_indirizzi',
                ['name','surname','email','hash','language','coda_invio','language_invio','confirmed','dateconfirmed','created','active'],
                [$_POST['name'],$_POST['surname'],$_POST['email'],$hash,$_lang['user'],0,Config::$langVars['user'],0,Config::$nowDateTimeIso,Config::$nowDateTimeIso,1]
            );
            Sql::insertRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }
            $id_item = Sql::getLastInsertedIdVar();

            // salva riferimenti categoria
            Sql::initQuery(DB_TABLE_PREFIX.'newsletter_cat_ind', ['id_cat','id_ind'], [intval($_POST['id_cat']),$id_item]);
            Sql::insertRecord();
            if (Core::$resultOp->error > 0) {
                ToolsStrings::redirect(URL_SITE.'error/db');
            }

            // procedura completata
            $foo = Config::$langVars['La tua richiesta di iscrizione è stata inviata!'];
            $result = ['error' => 0,'message' => $foo];
            //echo json_encode($result); die();
            $_SESSION['message'] = '0|'.$foo;
            $App->subview = 'register';

            break;

        default:
            $App->fromFormEmail = ($_POST['fromFormEmail'] ?? '');
            $App->breadcrumbs->items[] = ['class' => 'breadcrumb-item active','url' => '','title' => strip_tags((string) $App->moduleData->title)];
            $App->breadcrumbs->title = $App->moduleData->title;
            $App->breadcrumbs->tree =  Utilities::generateBreadcrumbsTree($App->breadcrumbs->items, $_lang, ['template' => $templateBreadcrumbsBar]);

            $App->metaTitlePage = $globalSettings['meta tags page']['title ini'].$App->moduleConfig->meta_title.$globalSettings['meta tags page']['title separator'].$globalSettings['meta tags page']['title end'];
            $App->metaDescriptionPage = $App->moduleConfig->meta_description;
            $App->metaKeywordsPage = $App->moduleConfig->meta_keywords;

            $App->meta_og_url = URL_SITE.Core::$request->action;
            $App->meta_og_type = 'website';
            $App->meta_og_title = SanitizeStrings::cleanTitleUrl($App->moduleConfig->meta_title);
            $App->meta_og_image = '';
            if ($App->moduleData->imageheader != '') {
                $App->meta_og_image = UPLOAD_DIR.'tema/'.$App->moduleData->imageheader;
            }
            $App->meta_og_description = $App->moduleConfig->meta_description;

            $App->jscriptCodeTop = "
		    let recaptchakey = '".Config::$globalSettings['google recaptcha key']."';
		    ";

            $App->jscript[] = '<script src="https://www.google.com/recaptcha/api.js?render='.Config::$globalSettings['google recaptcha key'].'"></script>';
            $App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/js/contacts.js"></script>';
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
        $App->urlPrivacyPage = preg_replace('/%URLSITE%/', URL_SITE, (string) $App->urlPrivacyPage);
        break;
}
