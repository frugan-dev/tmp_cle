<?php

/**
* Framework App PHP-MySQL
* PHP Version 8.4
* @copyright 2025 Websync
* classes/class.Logger.php v.1.0.0. 24/09/2025
*/

use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\LogRecord;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\Mime\Email;

class Logger
{
    private static $logger = null;

    /**
     * Initialize and return logger instance
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$logger === null) {
            self::initializeLogger();
        }
        return self::$logger;
    }

    /**
     * Magic method to delegate all calls to Monolog logger instance
     * Supports: emergency, alert, critical, error, warning, notice, info, debug, log
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return self::getInstance()->$method(...$arguments);
    }

    /**
     * Initialize the logger with handlers and processors
     */
    private static function initializeLogger(): void
    {
        self::$logger = new MonologLogger('app');

        // Register error handler
        ErrorHandler::register(self::$logger);

        // For placeholder substitution
        self::$logger->pushProcessor(new PsrLogMessageProcessor());

        if (!isCli()) {
            // Add processor for request data
            self::$logger->pushProcessor(function (LogRecord $record) {
                if (isset($_REQUEST) && (\is_array($_REQUEST) || $_REQUEST instanceof ArrayAccess)) {
                    $record->extra['_REQUEST'] = $_REQUEST;
                }

                if (isset($_POST) && (\is_array($_POST) || $_POST instanceof ArrayAccess)) {
                    $record->extra['_POST'] = $_POST;
                }

                if (isset($_FILES) && (\is_array($_FILES) || $_FILES instanceof ArrayAccess)) {
                    $record->extra['_FILES'] = $_FILES;
                }

                if (isset($_SESSION) && (\is_array($_SESSION) || $_SESSION instanceof ArrayAccess)) {
                    $record->extra['_SESSION'] = $_SESSION;
                }

                if (isset($_SERVER) && (\is_array($_SERVER) || $_SERVER instanceof ArrayAccess)) {
                    $record->extra['_SERVER'] = str_contains(\ini_get('variables_order') ?: '', 'E') ? $_SERVER : array_diff_key($_SERVER, $_ENV);
                }

                return $record;
            });
        }

        // Add file/error log handler
        self::addFileHandler();

        // Add email handler if mail transports are configured
        if (!empty($_ENV['MAIL_TRANSPORTS'])) {
            self::addEmailHandler();
        }
    }

    /**
     * Add file/error log handler
     */
    private static function addFileHandler(): void
    {
        if (isDevelop() || isCli()) {
            $handler = new ErrorLogHandler(level: Level::Debug);
        }

        if (isProduction()) {
            $handler = new RotatingFileHandler(
                PATH_LOG_DIR . '/app.log',
                100,
                // IMPORTANT: don't use level Debug in production!!!
                Level::Info
            );
        }

        // The last "true" tells monolog to remove empty []'s
        $handler->setFormatter(new LineFormatter(null, null, false, true));
        self::$logger->pushHandler($handler);
    }

    /**
     * Add email handler for error notifications
     */
    private static function addEmailHandler(): void
    {
        // Use the new FailoverEmailHandler that handles both SymfonyMailer and Native fallback
        $handler = new FailoverEmailHandler(Level::Error);

        if (isProduction()) {
            $handler = new DeduplicationHandler(
                $handler,
                PATH_LOG_DIR . '/dedup.log',
                Level::Error,
                3600
            );
        }

        self::$logger->pushHandler($handler);
    }
}
