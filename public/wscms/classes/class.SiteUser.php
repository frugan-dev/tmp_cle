<?php

/*	classes/class.SiteUser.php v.3.2.0. 14/02/2017 */

class SiteUser extends Core
{
    public static $dbtable;
    public function __construct()
    {
        parent::__construct();
        self::$dbtable = Sql::getTablePrefix().'site_users';
    }

    public static function getUserDetails($id, $opt)
    {
        $optDef = [];
        $opt = array_merge($optDef, $opt);
        if ($id > 0) {
            Sql::initQuery(self::$dbtable, ['*'], [$id], 'id = ? AND active = 1');
            $obj = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                if (isset($obj->id) && $obj->id > 0) {
                    return $obj;
                }
            }
        }
        return false;
    }

    public static function checkUser($sessionvars, $_lang, $opz)
    {
        $opzDef = [];
        $opz = array_merge($opzDef, $opz);
        $result = false;
        /* controlla se ha 'id */
        if (isset($sessionvars['id_user']) && $sessionvars['id_user'] > 0) {
            /* controlla se esiste */
            Sql::initQuery(self::$dbtable, ['id'], [$sessionvars['id_user']], 'id = ? AND active = 1');
            $obj = Sql::getRecord();
            if (Core::$resultOp->error == 0) {
                if (isset($obj->id) && $obj->id > 0) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public static function sendEmailUsernameUser($globalSettings, $_lang, $opz)
    {
        $opzDef = ['to email' => '','username' => ''];
        $opz = array_merge($opzDef, $opz);
        $subject = ($_lang['utente - soggetto email recupero username'] ?? $globalSettings['utente - soggetto email recupero username']);
        $subject = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $subject);
        $content = ($_lang['utente - contenuto email recupero username'] ?? $globalSettings['utente - soggetto email recupero username']);
        $content = preg_replace('/%USERNAME%/', (string) $opz['username'], (string) $content);
        $content = preg_replace('/%EMAIL%/', (string) $opz['to email'], (string) $content);
        $content = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $content);
        //echo '<br>subject: '.$subject;
        //echo '<br>content: '.$content;
        $opz['content'] = $content;
        //FIXED - DKIM requirements
        $opz['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['utente - from email recupero username'];
        $opz['from email label'] = $globalSettings['utente - from email label recupero username'];
        $opz['replyTo'] = [$globalSettings['utente - from email recupero username'] => $globalSettings['utente - from email label recupero username']];
        $opz['to email'] = $opz['to email'];
        $opz['send copy'] = $globalSettings['utente - send email debug recupero username'];
        $opz['copy email'] = $globalSettings['utente - email debug recupero username'];
        //print_r($opz);
        Mails::sendEmail($opz);
    }

    public static function sendEmailPasswordUser($globalSettings, $_lang, $opz)
    {
        $opzDef = ['to email' => '','username' => '','password' => ''];
        $opz = array_merge($opzDef, $opz);
        $subject = ($_lang['utente - soggetto email recupero password'] ?? $globalSettings['utente - soggetto email recupero password']);
        $subject = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $subject);
        $content = ($_lang['utente - contenuto email recupero password'] ?? $globalSettings['utente - soggetto email recupero password']);
        $content = preg_replace('/%PASSWORD%/', (string) $opz['password'], (string) $content);
        $content = preg_replace('/%USERNAME%/', (string) $opz['username'], (string) $content);
        $content = preg_replace('/%EMAIL%/', (string) $opz['to email'], (string) $content);
        $content = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $content);
        //echo '<br>subject: '.$subject;
        //echo '<br>content: '.$content;
        $opz['content'] = $content;
        //FIXED - DKIM requirements
        $opz['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['utente - from email recupero password'];
        $opz['from email label'] = $globalSettings['utente - from email label recupero password'];
        $opz['replyTo'] = [$globalSettings['utente - from email recupero password'] => $globalSettings['utente - from email label recupero password']];
        $opz['to email'] = $opz['to email'];
        $opz['send copy'] = $globalSettings['utente - send email debug recupero password'];
        $opz['copy email'] = $globalSettings['utente - email debug recupero password'];
        //print_r($opz);
        Mails::sendEmail($opz);
    }

    public static function sendEmailRegistrationUser($globalSettings, $_lang, $opz)
    {
        $opzDef = [];
        $opz = array_merge($opzDef, $opz);
        $subject = ($_lang['utente - soggetto email conferma registrazione'] ?? $globalSettings['utente - soggetto email conferma registrazione']);
        $subject = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $subject);
        $content = ($_lang['utente - contenuto email conferma registrazione'] ?? $globalSettings['utente - soggetto email conferma registrazione']);
        $content = preg_replace('/%URLCONFIRM%/', (string) $opz['urlconfirm'], (string) $content);
        $content = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $content);
        $content = preg_replace('/%USERNAME%/', (string) $_POST['username'], (string) $content);
        $content = preg_replace('/%EMAIL%/', (string) $_POST['email'], (string) $content);
        //echo '<br>subject: '.$subject;
        //echo '<br>content: '.$content;
        $opz['content'] = $content;
        //FIXED - DKIM requirements
        $opz['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['utente - from email conferma registrazione'];
        $opz['from email label'] = $globalSettings['utente - from email label conferma registrazione'];
        $opz['replyTo'] = [$globalSettings['utente - from email conferma registrazione'] => $globalSettings['utente - from email label conferma registrazione']];
        $opz['to email'] = $_POST['email'];
        $opz['send copy'] = $globalSettings['utente - send email debug conferma registrazione'];
        $opz['copy email'] = $globalSettings['utente - email debug conferma registrazione'];
        //print_r($opz);
        Mails::sendEmail($opz);
    }

    public static function sendEmailRegistrationStaff($globalSettings, $_lang, $opz)
    {
        $opzDef = [];
        $opz = array_merge($opzDef, $opz);
        $subject = (isset($_lang['utente - soggetto email staff conferma registrazione']) ? $_lang['utente - soggetto email conferma registrazione'] : $globalSettings['utente - soggetto email staff conferma registrazione']);
        $subject = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $subject);
        $content = ($_lang['utente - contenuto email staff conferma registrazione'] ?? $globalSettings['utente - contenuto email staff conferma registrazione']);
        $content = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $content);
        $content = preg_replace('/%USERNAME%/', (string) $_POST['username'], (string) $content);
        $content = preg_replace('/%IDUSER%/', (string) $_POST['id_user'], (string) $content);
        $content = preg_replace('/%EMAIL%/', (string) $_POST['email'], (string) $content);
        echo '<br>subject: '.$subject;
        echo '<br>content: '.$content;
        $opz['content'] = $content;
        //FIXED - DKIM requirements
        $opz['from email'] = $_ENV['MAIL_FROM_EMAIL'] ?? $globalSettings['utente - from email conferma registrazione'];
        $opz['from email label'] = $globalSettings['utente - from email label conferma registrazione'];
        $opz['replyTo'] = [$_POST['email']];
        $opz['to email'] = $globalSettings['utente - email staff conferma registrazione'];
        $opz['send copy'] = $globalSettings['utente - send email debug conferma registrazione'];
        $opz['copy email'] = $globalSettings['utente - email debug conferma registrazione'];
        //print_r($opz);
        Mails::sendEmail($opz);
    }

}
