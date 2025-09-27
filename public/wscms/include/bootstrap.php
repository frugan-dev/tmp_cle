<?php

require_once dirname(__DIR__, 2).'/vendor/autoload.php';

define('PATH_CACHE_DIR', $_SERVER['DOCUMENT_ROOT'].'/var/cache/');
define('PATH_LOG_DIR', $_SERVER['DOCUMENT_ROOT'].'/var/log/');
define('PATH_TMP_DIR', $_SERVER['DOCUMENT_ROOT'].'/var/tmp/');

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
        } elseif (empty($val) || 'null' === mb_strtolower((string) $val, 'UTF-8')) {
            $_ENV[$key] = null;
        }
    }
} catch (Exception $e) {
    // https://github.com/phpro/grumphp/blob/master/doc/tasks/phpparser.md#no_exit_statements
    exit($e->getMessage());
}

// https://stackoverflow.com/a/46634717/3929620
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

if (!function_exists('getEnvironment')) {
    function getEnvironment(): string
    {
        return match ($_SERVER['APP_ENV'] ?: $_ENV['APP_ENV'] ?? '') {
            'dev', 'develop', 'development', 'local' => 'develop',
            'stage', 'staging', => 'staging',
            'prod', 'production', 'live' => 'production',
            default => 'unknown'
        };
    }
}

if (!function_exists('isDebug')) {
    function isDebug(): bool
    {
        return (bool) $_ENV['APP_DEBUG'];
    }
}

if (!function_exists('isDevelop')) {
    function isDevelop(): bool
    {
        return 'develop' === getEnvironment();
    }
}

if (!function_exists('isStaging')) {
    function isStaging(): bool
    {
        return 'production' === getEnvironment();
    }
}

if (!function_exists('isProduction')) {
    function isProduction(): bool
    {
        return 'production' === getEnvironment();
    }
}

if (!function_exists('isCli')) {
    function isCli(): bool
    {
        return 'cli' === \PHP_SAPI;
    }
}

if (!function_exists('createDirs')) {
    function createDirs()
    {
        foreach ([
            PATH_CACHE_DIR,
            PATH_LOG_DIR,
            PATH_TMP_DIR,
        ] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
}

ini_set('display_errors', isDebug());

Kint::$enabled_mode = isDebug();

if (isDebug()) {
    //error_reporting(-1);
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

    @Kint::trace();
} else {
    error_reporting(E_ALL);
}

createDirs();

Logger::getInstance();
