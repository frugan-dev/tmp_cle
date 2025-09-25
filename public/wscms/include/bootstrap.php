<?php

/**
 * Copy custom variables from $_SERVER to $_ENV for PHP-FPM clear_env=yes compatibility
 *
 * PROBLEM CONTEXT:
 * When using Docker with PHP-FPM and `clear_env = yes` (default),
 * environment variables passed via Docker and defined in `environment.conf`
 * (e.g. /etc/php/*\/fpm/pool.d/www.conf or /opt/bitnami/php/etc/environment.conf w/ Bitnami)
 * are only injected into the $_SERVER superglobal — not into $_ENV or via getenv().
 *
 * This happens especially when `variables_order = GPCS` (default w/ Bitnami), which excludes `E` (ENV).
 *
 * SPECIFIC ENVIRONMENT FLOW:
 * 1. Docker Compose passes APP_ENV to container via 'environment:' section
 * 2. Bitnami PHP-FPM entrypoint saves it to /opt/bitnami/php/etc/environment.conf
 * 3. PHP-FPM loads variables into $_SERVER but not $_ENV (due to variables_order)
 * 4. phpdotenv createImmutable() detects existing system env vars and won't override them
 * 5. The APP_ENV value in application's .env file gets ignored by Dotenv (immutable behavior)
 * 6. But since $_ENV is empty, the variable is not accessible to the application
 *
 * SOLUTION:
 * Manually transfer custom environment variables from $_SERVER to $_ENV
 * before loading .env files, so phpdotenv can work correctly with createImmutable().
 *
 * TECHNICAL NOTES:
 * All environment variables received by PHP are strings; type casting should be handled 
 * manually by the application or by libraries like oscarotero/env.
 *
 * @see https://github.com/oscarotero/env/pull/6
 * @see https://www.php.net/manual/en/function.getenv.php
 * @see https://jolicode.com/blog/what-you-need-to-know-about-environment-variables-with-php
 * @see https://stackoverflow.com/a/42389720/3929620
 *
 * @param array $prefixes If empty, copy from start until first system variable.
 *                       If not empty, copy only variables with these prefixes.
 * @param array $systemVars System variables that act as "stop" when $prefixes is empty
 * @param bool $debug Optional logging of copied variables
 * @return array Copied variables
 */
function fixMissingEnvVars(
    array $prefixes = [],
    array $systemVars = ['PATH', 'USER', 'HOME', 'SHELL', 'PWD']
): array {
    // If variables_order includes 'E', $_ENV is already populated
    if (str_contains(ini_get('variables_order') ?: '', 'E')) {
        return [];
    }

    $copied = [];

    if (empty($prefixes)) {
        // Sequential mode: copy from start until first system variable
        foreach ($_SERVER as $key => $value) {
            // If we encounter a system variable, stop
            if (in_array($key, $systemVars, true)) {
                break;
            }

            // Copy the variable if it doesn't already exist in $_ENV
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                $copied[$key] = $value;
            }
        }
    } else {
        // Prefix mode: copy only variables with specific prefixes
        foreach ($_SERVER as $key => $value) {
            // Skip system variables
            if (in_array($key, $systemVars, true)) {
                continue;
            }

            // Check if the variable starts with one of the allowed prefixes
            foreach ($prefixes as $prefix) {
                if (str_starts_with($key, $prefix) && !isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    $copied[$key] = $value;
                    break;
                }
            }
        }
    }

    return $copied;
}

fixMissingEnvVars();

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
