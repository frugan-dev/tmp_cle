<?php

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

// https://stackoverflow.com/a/46634717/3929620
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

Logger::getInstance();
