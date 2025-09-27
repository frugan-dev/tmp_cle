<?php

/**
* Framework App PHP-MySQL
* PHP Version 8.4
* @copyright 2025 Websync
* classes/class.Mails.php v.2.0.0. 24/09/2025
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\FailoverTransport;
use Symfony\Component\Mailer\Transport\RoundRobinTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class Mails extends Core
{
    public static function sendEmail($address, $subject, $content, $text_content, $opt)
    {
        $optDef = ['sendDebug' => 0,'sendDebugEmail' => '','fromEmail' => 'n.d','fromLabel' => 'n.d','attachments' => ''];
        $opt = array_merge($optDef, $opt);

        match (self::$globalSettings['use send mail class']) {
            // Symfony Mailer (new default)
            1 => self::sendMailSymfony($address, $subject, $content, $text_content, $opt),
            // PHPMailer 6.x via composer
            2 => self::sendMailPHPMAILER($address, $subject, $content, $text_content, $opt),
            // Native PHP mail()
            default => self::sendMailPHP($address, $subject, $content, $text_content, $opt),
        };
    }

    /**
     * Send email using Symfony Mailer with OAuth2 support
     *
     * Email sending process overview:
     * 1. Transport Creation Phase:
     *    - buildTransports() creates available transport DSNs (SMTP OAuth2, Graph API, File, etc.)
     *    - createMailerTransport() handles both DSN strings and transport objects centrally
     *    - OAuth2TransportFactoryDecorator handles OAuth2 scheme conversion and authentication
     *    - Office365TokenProvider obtains access tokens for Microsoft OAuth2
     *
     * 2. SMTP OAuth2 Flow:
     *    - OAuth2TransportFactoryDecorator converts oauth2:// DSN to smtp://
     *    - Configures EsmtpTransport with OAuth2 access token as password
     *    - XOAuth2Authenticator handles SASL XOAUTH2 authentication with SMTP server
     *    - In development: forces OAuth2-only to prevent plain/login fallback
     *
     * 3. Graph API Flow:
     *    - GraphAPITransport uses Graph API instead of SMTP (requires explicit provider configuration)
     *    - Sends emails via REST API calls to configured Graph API endpoint
     *    - Handles attachments and complex email structures through API
     *
     * 4. File Transport Flow:
     *    - FileTransport saves emails to .eml files in specified directory
     *    - Can be used for debugging or as fallback option
     *    - Generates unique filenames with timestamp and hash
     *
     * 5. Transport Selection:
     *    - Failover/roundrobin techniques handle multiple transport fallback
     *    - Logger uses same centralized transport system for error emails
     *
     * Classes involved:
     * - OAuth2TransportFactoryDecorator: OAuth2 SMTP transport factory with dynamic provider support
     * - Office365TokenProvider: Microsoft-specific OAuth2 token management
     * - GraphAPITransport: Graph API email transport
     * - FileTransport: File-based email storage transport
     * - XOAuth2Authenticator: SMTP OAuth2 SASL authentication
     */
    public static function sendMailSymfony($address, $subject, $content, $text_content, $opt)
    {
        $optDef = ['replyTo' => [],'addBCC' => [],'sendDebug' => 0,'sendDebugEmail' => '','fromEmail' => 'n.d','fromLabel' => 'n.d','attachments' => ''];
        $opt = array_merge($optDef, $opt);

        try {
            // Create transport using centralized method
            $transport = self::createMailerTransport();
            $mailer = new Mailer($transport);

            // Create email message
            $email = new Email();

            // Set sender
            $fromEmail = $opt['fromEmail'] !== 'n.d' ? $opt['fromEmail'] : ($_ENV['MAIL_FROM_EMAIL'] ?? '');
            $fromLabel = $opt['fromLabel'] !== 'n.d' ? $opt['fromLabel'] : ($_ENV['MAIL_FROM_NAME'] ?? '');

            if (!empty($fromEmail)) {
                $email->from(new Address($fromEmail, $fromLabel));
            }

            // Set recipient
            $email->to($address);

            // Set subject and content
            $email->subject($subject);
            $email->html($content);
            if (!empty($text_content)) {
                $email->text($text_content);
            }

            // Add reply-to addresses
            if (!empty($opt['replyTo']) && is_array($opt['replyTo'])) {
                foreach ($opt['replyTo'] as $key => $value) {
                    if (is_string($key)) {
                        $email->addReplyTo(new Address($key, $value));
                    } else {
                        $email->addReplyTo($value);
                    }
                }
            }

            // Add BCC addresses
            if (!empty($opt['addBCC']) && is_array($opt['addBCC'])) {
                foreach ($opt['addBCC'] as $key => $value) {
                    if (is_string($key)) {
                        $email->addBcc(new Address($key, $value));
                    } else {
                        $email->addBcc($value);
                    }
                }
            }

            // Add debug BCC if enabled
            if (($opt['sendDebug'] ?? 0) == 1 && !empty($opt['sendDebugEmail'])) {
                $email->addBcc($opt['sendDebugEmail']);
            }

            // Add attachments
            if (!empty($opt['attachments']) && is_array($opt['attachments'])) {
                foreach ($opt['attachments'] as $attachment) {
                    $email->attachFromPath($attachment['filename'], $attachment['title'] ?? null);
                }
            }

            // Send email
            $sentMessage = $mailer->send($email);
            Core::$resultOp->error = 0;

            Logger::info('Email sent successfully', [
                'to' => $address,
                'subject' => $subject,
            ]);

            return true;

        } catch (Exception $e) {
            Core::$resultOp->error = 1;
            Logger::error('Failed to send email via Symfony Mailer', [
                'exception' => $e,
                'to' => $address,
                'subject' => $subject,
            ]);
            return false;
        }
    }

    /**
     * Create final transport from configuration
     * Public method so it can be used by both sendMailSymfony and Logger::addEmailHandler
     */
    public static function createMailerTransport(): TransportInterface
    {
        // Build transports array from environment configuration
        $transports = self::buildTransports();

        // Create transport instances using custom transport factory
        $transportInstances = [];
        foreach ($transports as $key => $dsn) {
            try {
                // Check if it's already a TransportInterface object or a DSN string
                if ($dsn instanceof TransportInterface) {
                    // It's already a transport object, use it directly
                    $transportInstances[] = $dsn;
                    Logger::debug('Using existing transport object', [
                        'key' => $key,
                        'class' => $dsn::class,
                    ]);
                } else {
                    // It's a DSN string, create transport from it
                    $transportInstances[] = self::createTransportWithCustomFactories($dsn);
                    Logger::debug('Created transport from DSN', [
                        'key' => $key,
                        'dsn' => preg_replace('/:[^:@]*@/', ':***@', (string) $dsn),
                    ]);
                }
            } catch (Exception $e) {
                Logger::error('Failed to create transport from DSN', [
                    'exception' => $e,
                    'key' => $key,
                    'value_type' => get_debug_type($dsn),
                ]);
                continue;
            }
        }

        if (empty($transportInstances)) {
            throw new Exception('No transports available');
        }

        // Create final transport based on technique
        $technique = $_ENV['MAIL_TRANSPORTS_TECHNIQUE'] ?? 'failover';
        if (count($transportInstances) === 1) {
            $transport = $transportInstances[0];
        } elseif ($technique === 'roundrobin') {
            $transport = new RoundRobinTransport($transportInstances);
        } else {
            $transport = new FailoverTransport($transportInstances);
        }

        Logger::debug('Final transport created', [
            'technique' => $technique,
            'transport_count' => count($transportInstances),
            'transports' => array_keys($transports),
        ]);

        return $transport;
    }

    /**
     * Build transports configuration for Symfony Mailer with OAuth2 support
     * Public method so Logger class can use it too
     */
    public static function buildTransports(): array
    {
        $transports = [];

        if (empty($_ENV['MAIL_TRANSPORTS']) && $_ENV['MAIL_TRANSPORTS'] !== null) {
            throw new Exception('MAIL_TRANSPORTS environment variable not configured. Use "null" to disable email sending.');
        }

        // https://github.com/symfony/mailer
        // https://symfony.com/doc/current/mailer.html
        // https://github.com/swiftmailer/swiftmailer/issues/866
        // https://github.com/swiftmailer/swiftmailer/issues/633
        foreach (array_map('trim', explode(',', (string) $_ENV['MAIL_TRANSPORTS'])) as $val) {
            switch ($val) {
                case 'oauth2-smtp':
                    if (self::isOAuth2SMTPConfigured()) {
                        try {
                            $dsn = self::createOAuth2SMTPTransport();
                            $transports['oauth2-smtp'] = $dsn;
                            Logger::debug('OAuth2 SMTP transport DSN created');
                        } catch (Exception $e) {
                            Logger::error('Failed to create OAuth2 SMTP transport DSN', [
                                'exception' => $e,
                            ]);
                        }
                    } else {
                        Logger::warning('OAuth2 SMTP transport skipped - not configured');
                    }
                    break;

                case 'oauth2-graph':
                    if (self::isOAuth2GraphConfigured()) {
                        try {
                            $transport = new GraphAPITransport();
                            if ($transport->isConfigured()) {
                                $transports['oauth2-graph'] = $transport;
                                Logger::debug('OAuth2 Graph API transport created');
                            } else {
                                Logger::warning('OAuth2 Graph API transport configuration invalid');
                            }
                        } catch (Exception $e) {
                            Logger::error('Failed to create OAuth2 Graph API transport', [
                                'exception' => $e,
                            ]);
                        }
                    } else {
                        Logger::warning('OAuth2 Graph API transport skipped - not configured');
                    }
                    break;

                    // it requires proc_*() functions
                case 'smtp':
                case 'smtps':
                    $transports[$val] = $val . '://';
                    if (!empty($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_USERNAME']) &&
                        !empty($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PASSWORD'])) {
                        $transports[$val] .= rawurlencode((string) $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_USERNAME']) .
                                            ':' . rawurlencode((string) $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PASSWORD']) . '@';
                    }

                    $transports[$val] .= $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_HOST'] .
                                        ':' . $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_PORT'] . '?';

                    foreach ([
                        'verify_peer',
                        'local_domain',
                        'restart_threshold',
                        'restart_threshold_sleep',
                        'ping_threshold',
                    ] as $item) {
                        if (isset($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')])) {
                            $transports[$val] .= '&' . $item . '=' .
                                rawurlencode((string) $_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')]);
                        }
                    }
                    break;

                    // if 'command' isn't specified, it will fallback to '/usr/sbin/sendmail -bs' (no ini_get() detection)
                case 'sendmail':
                    $transports[$val] = $val . '://default?';
                    foreach (['command'] as $item) {
                        if (isset($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')])) {
                            $transports[$val] .= '&' . $item . '=' .
                                strtr(rawurlencode($_ENV['MAIL_' . mb_strtoupper($val, 'UTF-8') . '_' . mb_strtoupper($item, 'UTF-8')]), [
                                    '%2F' => '/',
                                ]);
                        }
                    }
                    break;

                    // it uses sendmail or smtp transports with ini_get() detection
                    // When using native://default, if php.ini uses the sendmail -t command, you won't have error reporting and Bcc headers won't be removed.
                    // It's highly recommended to NOT use native://default as you cannot control how sendmail is configured (prefer using sendmail://default if possible).
                case 'native':
                    $transports[$val] = $val . '://default';
                    break;

                    //TODO
                    // only if proc_*() functions are not available...
                    // case 'mail':
                    // case 'mail+api':
                    // 	$transports[$val] = $val . '://default';
                    // 	break;

                case 'file':
                    $filePath = $_ENV['MAIL_FILE_PATH'] ?? PATH_TMP_DIR . 'emails';
                    $continueOnSuccess = ($_ENV['MAIL_FILE_CONTINUE_ON_SUCCESS'] ?? false) === true;

                    try {
                        $transports[$val] = FileTransportFactory::createTransport($filePath, $continueOnSuccess);
                        Logger::debug('File transport created', [
                            'path' => $filePath,
                            'continue_on_success' => $continueOnSuccess,
                        ]);
                    } catch (Exception $e) {
                        Logger::error('Failed to create file transport', [
                            'exception' => $e,
                            'path' => $filePath,
                        ]);
                    }
                    break;

                case null:
                    // https://symfony.com/doc/current/mailer.html#disabling-delivery
                    $transports['null'] = 'null://null';
                    Logger::debug('Null transport configured - emails will be discarded');
                    break;

                default:
                    Logger::warning('Unknown transport type: {transport_type}', [
                        'transport_type' => $val,
                    ]);
                    break;
            }
        }

        if (empty($transports)) {
            throw new Exception('No mail transports configured. Check MAIL_TRANSPORTS environment variable.');
        }

        return $transports;
    }

    /**
     * Check if OAuth2 SMTP is configured
     */
    public static function isOAuth2SMTPConfigured(): bool
    {
        $required = [
            'MAIL_OAUTH2_TENANT_ID',
            'MAIL_OAUTH2_CLIENT_ID',
            'MAIL_OAUTH2_CLIENT_SECRET',
        ];

        foreach ($required as $var) {
            if (empty($_ENV[$var])) {
                return false;
            }
        }

        $username = $_ENV['MAIL_OAUTH2_SMTP_USERNAME'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? '';
        return !empty($username);
    }

    /**
     * Check if OAuth2 Graph API is configured
     */
    public static function isOAuth2GraphConfigured(): bool
    {
        $required = [
            'MAIL_OAUTH2_TENANT_ID',
            'MAIL_OAUTH2_CLIENT_ID',
            'MAIL_OAUTH2_CLIENT_SECRET',
        ];

        foreach ($required as $var) {
            if (empty($_ENV[$var])) {
                return false;
            }
        }

        $userId = $_ENV['MAIL_OAUTH2_GRAPH_USER_ID'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? '';
        return !empty($userId);
    }

    /**
     * Send email using PHPMailer 6.x
     */
    public static function sendMailPHPMAILER($address, $subject, $content, $text_content, $opt)
    {
        $optDef = ['replyTo' => [],'addBCC' => [],'sendDebug' => 0,'sendDebugEmail' => '','fromEmail' => 'n.d','fromLabel' => 'n.d','attachments' => ''];
        $opt = array_merge($optDef, $opt);

        try {
            $mail = new PHPMailer(true); // Enable exceptions

            // Server settings based on global configuration
            switch (self::$globalSettings['mail server']) {
                case 'SMTP':
                    $mail->isSMTP();
                    $mail->Host = self::$globalSettings['SMTP server'];
                    $mail->Port = (int)self::$globalSettings['SMTP port'];

                    if (!empty(self::$globalSettings['SMTP username'])) {
                        $mail->SMTPAuth = true;
                        $mail->Username = self::$globalSettings['SMTP username'];
                        $mail->Password = self::$globalSettings['SMTP password'];
                    }

                    // Auto-detect encryption
                    if ((int)self::$globalSettings['SMTP port'] === 465) {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } elseif ((int)self::$globalSettings['SMTP port'] === 587) {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    break;

                case 'sendmail':
                    $mail->isSendmail();
                    if (!empty(self::$globalSettings['sendmail server'])) {
                        $mail->Sendmail = self::$globalSettings['sendmail server'];
                    }
                    break;

                default:
                    $mail->isMail();
                    break;
            }

            // Recipients and content
            $mail->setFrom($opt['fromEmail'], $opt['fromLabel']);
            $mail->addAddress($address);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->AltBody = $text_content;
            $mail->Body = $content;

            // Reply-to addresses
            if (is_array($opt['replyTo']) && count($opt['replyTo'])) {
                foreach ($opt['replyTo'] as $key => $value) {
                    if (is_string($key)) {
                        $mail->addReplyTo($key, $value);
                    } else {
                        $mail->addReplyTo($value);
                    }
                }
            }

            // BCC addresses
            if (is_array($opt['addBCC']) && count($opt['addBCC'])) {
                foreach ($opt['addBCC'] as $key => $value) {
                    if (is_string($key)) {
                        $mail->addBCC($key, $value);
                    } else {
                        $mail->addBCC($value);
                    }
                }
            }

            // Debug BCC
            if ($opt['sendDebug'] == 1 && !empty($opt['sendDebugEmail'])) {
                $mail->addBCC($opt['sendDebugEmail']);
            }

            // Attachments
            if (is_array($opt['attachments']) && count($opt['attachments'])) {
                foreach ($opt['attachments'] as $attachment) {
                    $mail->addAttachment($attachment['filename'], $attachment['title'] ?? '');
                }
            }

            $mail->send();
            Core::$resultOp->error = 0;

        } catch (PHPMailerException $exception) {
            Core::$resultOp->error = 1;
            Logger::error($exception->getMessage(), [
                'exception' => $exception,
            ]);
        }
    }

    /**
     * Send email using native PHP mail() function
     */
    public static function sendMailPHP($address, $subject, $content, $text_content, $opt)
    {
        $optDef = ['sendDebug' => 0,'sendDebugEmail' => '','fromEmail' => 'n.d','fromLabel' => 'n.d','attachments' => ''];
        $opt = array_merge($optDef, $opt);

        $mail_boundary = '=_NextPart_' . md5(uniqid(time()));
        $headers = 'From: ' . $opt['fromLabel'] . ' <' . $opt['fromEmail'] . ">\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/alternative;\n\tboundary=\"$mail_boundary\"\n";
        $headers .= 'X-Mailer: PHP ' . phpversion();

        // Build message body
        $msg = "This is a multi-part message in MIME format.\n\n";
        $msg .= "--$mail_boundary\n";
        $msg .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
        $msg .= "Content-Transfer-Encoding: 8bit\n\n";
        $msg .= $text_content; // Add text version

        $msg .= "\n--$mail_boundary\n";
        $msg .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $msg .= "Content-Transfer-Encoding: 8bit\n\n";
        $msg .= $content; // Add HTML version

        // End multipart/alternative boundary
        $msg .= "\n--$mail_boundary--\n";

        $sender = $opt['fromEmail'];
        // Set Return-Path (works only on Windows hosting)
        ini_set('sendmail_from', $sender);
        // Send message, the fifth parameter "-f$sender" sets Return-Path on Linux hosting
        $result = mail((string) $address, (string) $subject, $msg, $headers, "-f$sender");

        if (!$result) {
            Core::$resultOp->error = 1;
            Logger::error('PHP mail() function failed for address: {address}', [
                'address' =>  $address,
            ]);
        } else {
            Core::$resultOp->error = 0;
        }
    }

    /**
     * Parse mail template content with placeholders
     */
    public static function parseMailContent($post, $content, $opt = [])
    {
        $optDef = ['customFields' => [],'customFieldsValue' => []];
        $opt = array_merge($optDef, $opt);

        $content = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $content);

        $content = preg_replace('/{{/', '%', (string) $content);
        $content = preg_replace('/}}/', '%', (string) $content);

        if (isset($post['urlconfirm'])) {
            $content = preg_replace('/%URLCONFIRM%/', $post['urlconfirm'], (string) $content);
        }
        if (isset($post['hash'])) {
            $content = preg_replace('/%HASH%/', $post['hash'], (string) $content);
        }
        if (isset($post['username'])) {
            $content = preg_replace('/%USERNAME%/', $post['username'], (string) $content);
        }
        if (isset($post['name'])) {
            $content = preg_replace('/%NAME%/', $post['name'], (string) $content);
        }
        if (isset($post['surname'])) {
            $content = preg_replace('/%SURNAME%/', $post['surname'], (string) $content);
        }
        if (isset($post['email'])) {
            $content = preg_replace('/%EMAIL%/', $post['email'], (string) $content);
        }
        if (isset($post['subject'])) {
            $content = preg_replace('/%SUBJECT%/', $post['subject'], (string) $content);
        }
        if (isset($post['object'])) {
            $content = preg_replace('/%OBJECT%/', $post['object'], (string) $content);
        }
        if (isset($post['message'])) {
            $content = preg_replace('/%MESSAGE%/', $post['message'], (string) $content);
        }

        if ((is_array($opt['customFields']) && count($opt['customFields']))
            && (is_array($opt['customFieldsValue']) && count($opt['customFieldsValue']))
            && (count($opt['customFields']) == count($opt['customFieldsValue']))
        ) {
            foreach ($opt['customFields'] as $key => $value) {
                $content = preg_replace('/'.$opt['customFields'][$key].'/', (string) $opt['customFieldsValue'][$key], (string) $content);
            }
        }

        return $content;
    }

    /**
     * Create transport with custom factories (following cleca approach)
     */
    private static function createTransportWithCustomFactories(string $dsnString): TransportInterface
    {
        // Get default factories
        $factories = Transport::getDefaultFactories(null, null, Logger::getInstance());

        // Convert to array to allow modifications
        $factoriesArray = iterator_to_array($factories);

        // Add OAuth2 transport factory at the beginning (higher priority)
        array_unshift($factoriesArray, new OAuth2TransportFactoryDecorator(null, null, Logger::getInstance()));

        Logger::debug('Custom transport factories registered', [
            'factory_count' => count($factoriesArray),
            'dsn' => preg_replace('/:[^:@]*@/', ':***@', $dsnString),
        ]);

        // Create Transport factory instance with custom factories
        $transportFactory = new Transport($factoriesArray);

        // Create the transport
        return $transportFactory->fromString($dsnString);
    }

    /**
     * Create OAuth2 SMTP transport DSN
     */
    private static function createOAuth2SMTPTransport(): string
    {
        $provider = $_ENV['MAIL_OAUTH2_PROVIDER'] ?? throw new Exception('MAIL_OAUTH2_PROVIDER must be configured for OAuth2 SMTP transport');

        // Supported providers for OAuth2 SMTP
        $supportedProviders = [
            'microsoft-office365',
            // Future providers can be added here:
            // 'google',
            // 'amazon-ses',
        ];

        if (!in_array($provider, $supportedProviders)) {
            throw new Exception("OAuth2 provider '{$provider}' is not supported for SMTP. Supported providers: " . implode(', ', $supportedProviders));
        }

        $host = $_ENV['MAIL_OAUTH2_SMTP_HOST'] ?? $_ENV['MAIL_SMTP_HOST'] ?? throw new Exception('MAIL_OAUTH2_SMTP_HOST or MAIL_SMTP_HOST must be configured');
        $port = (int)($_ENV['MAIL_OAUTH2_SMTP_PORT'] ?? $_ENV['MAIL_SMTP_PORT'] ?? throw new Exception('MAIL_OAUTH2_SMTP_PORT or MAIL_SMTP_PORT must be configured'));
        $username = $_ENV['MAIL_OAUTH2_SMTP_USERNAME'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? throw new Exception('MAIL_OAUTH2_SMTP_USERNAME or MAIL_FROM_EMAIL must be configured');

        // Build SMTP DSN with OAuth2 provider parameter (instead of oauth2:// scheme)
        $dsn = 'smtp://' . rawurlencode((string) $username) . ':@' . $host . ':' . $port;

        // Add OAuth2 provider parameter
        $dsn .= '?oauth2_provider=' . urlencode((string) $provider);

        Logger::debug('OAuth2 SMTP DSN configured', [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'provider' => $provider,
            'dsn' => preg_replace('/:[^:@]*@/', ':***@', $dsn), // Hide credentials in log
        ]);

        return $dsn;
    }
}
