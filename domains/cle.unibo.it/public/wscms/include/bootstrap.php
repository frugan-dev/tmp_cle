<?php

use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require_once dirname(__DIR__, 2).'/vendor/autoload.php';

$suffix = '';
$env = '.env';

if (defined('_APP_ENV')) {
    $suffix .= '.'._APP_ENV;
}

// docker -> minideb
if (!empty($_SERVER['APP_ENV'])) {
    $suffix .= '.'.$_SERVER['APP_ENV'];
}

if (file_exists(__DIR__.'/.env'.$suffix)) {
    $env = '.env'.$suffix;
}

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, $env);
    $dotenv->load();
    $dotenv->required(['DB_1_HOST', 'DB_1_NAME', 'DB_1_USER', 'DB_1_PASS', 'DB_1_PREFIX']);

    // https://github.com/vlucas/phpdotenv/issues/231#issuecomment-663879815
    foreach ($_ENV as $key => $val) {
        if (ctype_digit((string) $val)) {
            $dotenv->required($key)->isInteger();
            $_ENV[$key] = (int) $val;
        } elseif (!empty($val) && !is_numeric($val) && ($newVal = \filter_var($_ENV[$key], \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE)) !== null) {
            $dotenv->required($key)->isBoolean();
            $_ENV[$key] = $newVal;
        }
    }
} catch (\Exception $e) {
    // https://github.com/phpro/grumphp/blob/master/doc/tasks/phpparser.md#no_exit_statements
    exit($e->getMessage());
}

ini_set('display_errors', (bool) $_ENV['APP_DEBUG']);

Kint::$enabled_mode = (bool) $_ENV['APP_DEBUG'];

if (!empty($_ENV['APP_DEBUG'])) {
    //error_reporting(-1);
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

    @Kint::trace();
} else {
    error_reporting(E_ALL);
}

$logger = new Logger('app');

// Note: Without $app->addErrorMiddleware() in Slim, it's necessary to manually catch and log errors with $logger->error().
// This is different from a plain PHP context where Monolog's ErrorHandler automatically handles and logs errors.
ErrorHandler::register($logger);

$logger->pushProcessor(function (LogRecord $record) {
    if (isset($_REQUEST) && (\is_array($_REQUEST) || $_REQUEST instanceof \ArrayAccess)) {
        $record->extra['_REQUEST'] = $_REQUEST;
    }

    if (isset($_POST) && (\is_array($_POST) || $_POST instanceof \ArrayAccess)) {
        $record->extra['_POST'] = $_POST;
    }

    if (isset($_FILES) && (\is_array($_FILES) || $_FILES instanceof \ArrayAccess)) {
        $record->extra['_FILES'] = $_FILES;
    }

    if (isset($_SESSION) && (\is_array($_SESSION) || $_SESSION instanceof \ArrayAccess)) {
        $record->extra['_SESSION'] = $_SESSION;
    }

    if (isset($_SERVER) && (\is_array($_SERVER) || $_SERVER instanceof \ArrayAccess)) {
        $record->extra['_SERVER'] = array_diff_key($_SERVER, $_ENV);
    }

    return $record;
});

if (in_array($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'], ['develop'], true)) {
    $handler = new ErrorLogHandler(level: Level::Debug);
} else {
    $handler = new RotatingFileHandler(dirname(__FILE__, 2).'/tmp/log/app.log', 100, Level::Debug);
}

// The last "true" here tells monolog to remove empty []'s
$handler->setFormatter(new LineFormatter(null, null, false, true));

$logger->pushHandler($handler);

$transports = [];

// https://github.com/symfony/mailer
// https://symfony.com/doc/current/mailer.html
// https://github.com/swiftmailer/swiftmailer/issues/866
// https://github.com/swiftmailer/swiftmailer/issues/633
foreach (array_map('trim', explode(',', (string) $_ENV['MAIL_TRANSPORTS'])) as $key => $val) {
    switch ($val) {
        // it requires proc_*() functions
        case 'smtp':
        case 'smtps':
            $transports[$val] = $val.'://';
            if (!empty($_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_USERNAME']) && !empty($_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_PASSWORD'])) {
                $transports[$val] .= rawurlencode((string) $_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_USERNAME']).':'.rawurlencode((string) $_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_PASSWORD']).'@';
            }

            $transports[$val] .= $_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_HOST'].':'.$_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_PORT'].'?';

            foreach ([
                'verify_peer',
                'local_domain',
                'restart_threshold',
                'restart_threshold_sleep',
                'ping_threshold',
            ] as $item) {
                if (isset($_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_'.mb_strtoupper((string) $item, 'UTF-8')])) {
                    $transports[$val] .= '&'.$item.'='.rawurlencode((string) $_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_'.mb_strtoupper((string) $item, 'UTF-8')]);
                }
            }
            break;

        // if 'command' isn't specified, it will fallback to '/usr/sbin/sendmail -bs' (no ini_get() detection)
        case 'sendmail':
            $transports[$val] = $val.'://default?';
            foreach ([
                'command',
            ] as $item) {
                if (isset($_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_'.mb_strtoupper((string) $item, 'UTF-8')])) {
                    $transports[$val] .= '&'.$item.'='.strtr(rawurlencode((string) $_ENV['MAIL_'.mb_strtoupper((string) $val, 'UTF-8').'_'.mb_strtoupper((string) $item, 'UTF-8')]), [
                        '%2F' => '/',
                    ]);
                }
            }
            break;

        // it uses sendmail or smtp transports with ini_get() detection
        // When using native://default, if php.ini uses the sendmail -t command, you won't have error reporting and Bcc headers won't be removed.
        // It's highly recommended to NOT use native://default as you cannot control how sendmail is configured (prefer using sendmail://default if possible).
        case 'native':
            $transports[$val] = $val.'://default';
            break;

        //TODO
        // only if proc_*() functions are not available...
        case 'mail':
        case 'mail+api':
            $transports[$val] = $val.'://default';
            break;
    }
}
if (empty($transports)) {
    // https://symfony.com/doc/current/mailer.html#disabling-delivery
    $transports['null'] = 'null://null';
}
$transport = Transport::fromDsn($_ENV['MAIL_TRANSPORTS_TECHNIQUE'].'('.implode(' ', $transports).')', null, null, $logger);

$mailer = new Mailer($transport);

// https://github.com/symfony/symfony/issues/41322
// https://stackoverflow.com/a/14253556/3929620
// https://stackoverflow.com/a/25873119/3929620
$message = new Email()
    ->returnPath($_ENV['MAIL_RETURN_PATH']) // overwritten by the Sender
    ->sender(new Address($_ENV['MAIL_SENDER_EMAIL'], $_ENV['MAIL_SENDER_NAME']))
    ->from(new Address($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']))
    ->subject(sprintf(_('Error reporting from %1$s - %2$s'), $_SERVER['HTTP_HOST'], $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV']))
    ->to(...array_map('trim', explode(',', (string) $_ENV['MAIL_TO_EMAILS'])))
;

$handler = new SymfonyMailerHandler($mailer, $message, Level::Error);
$handler->setFormatter(new HtmlFormatter());

if (in_array($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'], ['production'], true)) {
    $handler = new DeduplicationHandler($handler, dirname(__FILE__, 2).'/tmp/log/dedup.log', Level::Error, 3600);
}

$logger->pushHandler($handler);

// https://stackoverflow.com/a/46634717/3929620
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}
