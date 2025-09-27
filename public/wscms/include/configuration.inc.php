<?php

/**
 * Framework Siti PHP-MySQL
 * PHP Version 7
 * @copyright 2021 Websync
 * app/include/configuration.inc.php v.4.0.0. 22/10/2021
 */

require_once __DIR__.'/bootstrap.php';

$servermode = 'remote';
if ($_SERVER['HTTP_HOST'] == '192.168.1.11') {
    $servermode = 'local';
}

/* SERVER */
$globalSettings['folder site'] = '';
$globalSettings['folder admin'] = 'wscms/';
$globalSettings['site host'] = 'cle.unibo.it/';
if ($servermode == 'local') {
    $globalSettings['folder site'] = 'websync.framework.sito.400/';
    $globalSettings['folder admin'] = 'wscms/';
    $globalSettings['site host'] = '192.168.1.11/';
}

$globalSettings['server timezone'] = '';
$http = 'https://';
if ($servermode == 'local') {
    $http = 'http://';
}
if (isset($_SERVER['HTTPS'])) {
    $http = 'https://';
}

/* DATABASE */
$database = 'local';
if ($servermode == 'remote') {
    $database = 'remote';
}
$globalSettings['database'] =  [
    $database => [
        'user' => $_ENV['DB_1_USER'],
        'password' => $_ENV['DB_1_PASS'],
        'host' => $_ENV['DB_1_HOST'],
        'name' => $_ENV['DB_1_NAME'],
        'tableprefix' => $_ENV['DB_1_PREFIX'],
    ],
];

/* COOKIES */
$globalSettings['cookiestecnicidatabase'] = 'websyncframeworksiti400database';
$globalSettings['cookiestecnici'] = 'websyncframeworksiti400site';
$globalSettings['cookiesterzeparti'] = 'websyncframeworksiti400thirdyparts';
if ($servermode == 'local') {
    $globalSettings['cookiestecnicidatabase'] = 'loc'.$globalSettings['cookiestecnicidatabase'];
    $globalSettings['cookiestecnici'] = 'loc'.$globalSettings['cookiestecnici'];
    $globalSettings['cookiesterzeparti'] = 'loc'.$globalSettings['cookiesterzeparti'];
}

/* EMAILS */
$globalSettings['default email'] = 'programmazione@websync.it';
$globalSettings['default email label'] = 'Programmazione Websync';
$globalSettings['send email debug'] = 1;
$globalSettings['email debug'] = 'programmazione@websync.it';

/* send email */
// use class for mails:
// 0 = no class (PHP's native mail() function)
// 1 = Symfony Mailer class
// 2 = PHPMailer 6.x class
$globalSettings['use send mail class'] = 1;
$globalSettings['mail server'] = 'SMTP'; /* SMTP, PHP or sendmail (only used w/ 'use send mail class' = 2) */
$globalSettings['sendmail server'] = $_ENV['MAIL_SENDMAIL_COMMAND'] ?? '/usr/sbin/sendmail -bs';
$globalSettings['SMTP server'] = $_ENV['MAIL_SMTP_HOST'];
$globalSettings['SMTP port'] = $_ENV['MAIL_SMTP_PORT'] ?? 587;
$globalSettings['SMTP username'] = $_ENV['MAIL_SMTP_USERNAME'];
$globalSettings['SMTP password'] = $_ENV['MAIL_SMTP_PASSWORD'];

/* CHIAVE HASH */
$globalSettings['site code key'] = $_ENV['SITE_CODE_KEY'];

/* SITE */
/* meta for admin */
$globalSettings['site name'] = 'Master CLE Erasmus Mundus';
$globalSettings['code version'] = '4.0.0.';
$globalSettings['site owner'] = 'Websync';
$globalSettings['copyright'] = '&copy; 2021 Websync';

/* meta for site */
$globalSettings['meta tags page'] = [
    'title ini' => '',
    'title separator' => ' | ',
    'title end' => 'Master CLE Erasmus Mundus',
    'description' => '',
    'keyword' => '',
    ];

/* CONFIGURAZIONI GENERALI */
$globalSettings['mesi'] = ['','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novenbre','Dicembre'];
$globalSettings['anno creazione'] = '2022';
$globalSettings['azienda referente'] = 'Websync s.r.l.';
$globalSettings['azienda breve'] = 'Websync';
$globalSettings['azienda slogan'] = 'Dipartimento di Lingue Letterature e Culture Moderne';
$globalSettings['azienda sito'] = 'www.websync.it';
$globalSettings['azienda sito url'] = 'https://www.websync.it';
$globalSettings['azienda indirizzo'] = 'Via Cartoleria, 5';
$globalSettings['azienda comune'] = 'Bologna';
$globalSettings['azienda cap'] = '40124';
$globalSettings['azienda provincia'] = 'Bologna';
$globalSettings['azienda targa'] = 'BO';
$globalSettings['azienda nazione'] = 'Italy';
$globalSettings['azienda email'] = 'bruna.conconi@unibo.it';
$globalSettings['azienda email1'] = 'lucia.manservisi@unibo.it';
$globalSettings['azienda email pec'] = 'info@websync.it';
$globalSettings['azienda telefono'] = '+39 051 2097105';
$globalSettings['azienda telefono1'] = '+39 051 2097230';
$globalSettings['azienda fax'] = '+39 333 5298401';
$globalSettings['azienda cellulare'] = '';
$globalSettings['azienda codice fiscale'] = '80007010376';
$globalSettings['azienda partita iva'] = '01131710376 ';
$globalSettings['azienda latitudine'] = '';
$globalSettings['azienda longitudine'] = '';
$globalSettings['sito credits'] = 'WebSync.it';
$globalSettings['sito credits url'] = 'https://www.websync.it';

/* LANGUAGE */
$globalSettings['default language'] = 'en';
$globalSettings['languages'] = ['it','en','fr','el'];

/* UPLOAD */
$globalSettings['image type available'] = ['jpg','png','gif'];
$globalSettings['file type available'] = ['doc','pdf','sql'];

/* LINK SOCIAL */
$globalSettings['facebook link'] = 'https://www.facebook.com/ErasmusMundusCle';
$globalSettings['linkedin link'] = '';
$globalSettings['twitter link'] = '';
$globalSettings['google-plus link'] = '';
$globalSettings['pinterest link'] = '';
$globalSettings['vimeo link'] = '';
$globalSettings['youtube link'] = 'https://www.youtube.com/channel/UCTDz42YFHyEnJ5BwBGP4H-Q';
$globalSettings['instagram link'] = 'https://www.instagram.com/erasmus_mundus_cle';

/* GOOGLE ReCaptcha */
$globalSettings['google recaptcha key'] = $_ENV['GOOGLE_RECAPTCHA_KEY'];
$globalSettings['google recaptcha secret'] = $_ENV['GOOGLE_RECAPTCHA_SECRET'];

$globalSettings['google_map_api_key'] = $_ENV['GOOGLE_MAP_API_KEY'];

$globalSettings['session_random_key'] = $_ENV['SESSION_RANDOM_KEY'];

/* DA NON MODIFICARE */

$globalSettings['requestoption'] = [
    'coremodules' => ['requestsajax','confirmaccount','register','login','logout','account','password','profile','nopassword','nousername','moduleassociated','error'],
    'templateuser' => 'default',
    'defaulttemplate' => 'default',
    'templatesforusers' => ['default'],
    'managechangeaction' => 0,
    'defaultaction' => '',
    'othermodules' => ['404','error','customer','search','test'],
    ];

$globalSettings['months'] = ['01' => 'Gennaio','02' => 'Febbraio','03' => 'Marzo','04' => 'Aprile','05' => 'Maggio','06' => 'Giugno','07' => 'Luglio','08' => 'Agosto','09' => 'Settembre','10' => 'Ottobre','11' => 'Novenbre','12' => 'Dicembre'];
$globalSettings['page-type'] = ['default' => 'Default','label' => 'Etichetta','url' => 'Url','module-link' => 'Link a modulo'];
$globalSettings['url-targets'] = ['_self','_blank'];
$globalSettings['module sections'] = ['Moduli Core','Moduli Personalizzati','Moduli Vecchi','Impostazioni','Root'];
$globalSettings['menu-type'] = ['default' => 'Default','label' => 'Etichetta','url' => 'url','module-link' => 'Link a modulo','module-menu' => 'Menu generato da modulo'];

define('FOLDER_SITE', $globalSettings['folder site']);
define('FOLDER_ADMIN', $globalSettings['folder admin']);
define('SITE_HOST', $globalSettings['site host']);
define('TIMEZONE', $globalSettings['server timezone']);
define('URL_SITE', $http.SITE_HOST.FOLDER_SITE);
define('URL_SITE_ADMIN', $http.SITE_HOST.FOLDER_SITE.FOLDER_ADMIN);
define('URL_SITE_APPLICATION', $http.SITE_HOST.FOLDER_SITE.FOLDER_ADMIN.'application/');
define('PATH_DOCUMENT', $_SERVER['DOCUMENT_ROOT'].'/');
define('PATH_SITE', $_SERVER['DOCUMENT_ROOT'].'/'.FOLDER_SITE);
define('PATH_SITE_ADMIN', $_SERVER['DOCUMENT_ROOT'].'/'.FOLDER_SITE.FOLDER_ADMIN);
// moved to bootstrap.php (specifically PATH_TMP_DIR) to allow the Logger to use the mail transport 'file'
//define('PATH_CACHE_DIR', PATH_SITE.'var/cache/');
//define('PATH_LOG_DIR', PATH_SITE.'var/log/');
//define('PATH_TMP_DIR', PATH_SITE.'var/tmp/');
/* upload */
define('UPLOAD_DIR', $http.SITE_HOST.FOLDER_SITE.'uploads/');
define('PATH_UPLOAD_DIR', 'uploads/');
define('ADMIN_PATH_UPLOAD_DIR', '../'.PATH_UPLOAD_DIR);
define('DATABASE', $database);
define('DATABASEUSED', $database);
define('SESSIONS_TABLE_NAME', $globalSettings['database'][DATABASE]['tableprefix'].'sessions');
//define('SESSIONS_TIME',86400*10);
define('SESSIONS_TIME', 0);
define('SESSIONS_GC_TIME', 2592000);
define('SESSIONS_COOKIE_NAME', $globalSettings['cookiestecnicidatabase']);
define('AD_SESSIONS_COOKIE_NAME', 'admin_'.$globalSettings['cookiestecnicidatabase']);
define('DATA_SESSIONS_COOKIE_NAME', 'data_'.$globalSettings['cookiestecnicidatabase']);
define('TEMPLATE_DEFAULT', $globalSettings['requestoption']['defaulttemplate']);
define('SITE_CODE_KEY', $globalSettings['site code key']);
define('SITE_NAME', $globalSettings['site name']);
define('CODE_VERSION', $globalSettings['code version']);
define('SITE_OWNER', $globalSettings['site owner']);
define('COPYRIGHT', $globalSettings['copyright']);
