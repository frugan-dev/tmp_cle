<?php
/**
* Framework App PHP-MySQL
* PHP Version 8.4
* @copyright 2025 Websync
* classes/class.Logger.php v.1.0.0. 24/09/2025
*/

use Monolog\ErrorHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\LogRecord;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Logger {

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
        
        // Add processor for request data
        self::$logger->pushProcessor(function (LogRecord $record) {
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
                $record->extra['_SERVER'] = str_contains(\ini_get('variables_order') ?: '', 'E') ? $_SERVER : array_diff_key($_SERVER, $_ENV);
            }

            return $record;
        });
        
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
        if (isDevelop()) {
            $handler = new ErrorLogHandler(level: Level::Debug);
        } else {
            $handler = new RotatingFileHandler(
                PATH_LOG_DIR . '/app.log', 
                100, 
                Level::Debug
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
        $transports = Mails::buildTransports();
        
        if (empty($transports)) {
            // https://symfony.com/doc/current/mailer.html#disabling-delivery
            $transports['null'] = 'null://null';
        }
        
        $transport = Transport::fromDsn(
            ($_ENV['MAIL_TRANSPORTS_TECHNIQUE'] ?? 'failover') . '(' . implode(' ', $transports) . ')', 
            null, 
            null, 
            self::$logger
        );

        $mailer = new Mailer($transport);

        // https://github.com/symfony/symfony/issues/41322
        // https://stackoverflow.com/a/14253556/3929620
        // https://stackoverflow.com/a/25873119/3929620
        $message = new Email()
            ->returnPath($_ENV['MAIL_RETURN_PATH'])
            ->sender(new Address($_ENV['MAIL_SENDER_EMAIL'], $_ENV['MAIL_SENDER_NAME']))
            ->from(new Address($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']))
            ->subject(sprintf(
                _('Error reporting from %1$s - %2$s'), 
                $_SERVER['HTTP_HOST'], 
                getEnvironment()
            ))
            ->to(...array_map('trim', explode(',', (string) $_ENV['MAIL_TO_EMAILS'])));

        $handler = new SymfonyMailerHandler($mailer, $message, Level::Error);
        $handler->setFormatter(new HtmlFormatter());

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
