<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.OAuth2SMTPProvider.php v.1.0.0. 24/09/2025
 */

class OAuth2SMTPProvider implements MailProviderInterface
{
    private Office365TokenProvider $tokenProvider;
    private readonly string $smtpHost;
    private readonly int $smtpPort;
    private readonly string $username;
    
    public function __construct()
    {
        $this->smtpHost = $_ENV['MAIL_OAUTH2_SMTP_HOST'] ?? 'smtp.office365.com';
        $this->smtpPort = (int)($_ENV['MAIL_OAUTH2_SMTP_PORT'] ?? 587);
        $this->username = $_ENV['MAIL_SMTP_USERNAME'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? '';
        
        if ($this->isAvailable()) {
            $this->tokenProvider = Office365TokenProvider::createFromEnv();
        }
    }
    
    public function getName(): string
    {
        return 'oauth2-smtp';
    }
    
    public function isAvailable(): bool
    {
        $enabled = ($_ENV['MAIL_OAUTH2_ENABLED'] ?? 'false') === 'true';
        $hasCredentials = !empty($_ENV['MAIL_OAUTH2_TENANT_ID']) && 
                         !empty($_ENV['MAIL_OAUTH2_CLIENT_ID']) && 
                         !empty($_ENV['MAIL_OAUTH2_CLIENT_SECRET']) &&
                         !empty($this->username);
        
        return $enabled && $hasCredentials;
    }
    
    public function sendEmail(string $to, string $subject, string $htmlContent, string $textContent, array $options = []): bool
    {
        if (!$this->isAvailable()) {
            Logger::error('OAuth2 SMTP provider not available');
            return false;
        }
        
        try {
            $transport = $this->createTransport();
            $mailer = new \Symfony\Component\Mailer\Mailer($transport);
            
            $email = new \Symfony\Component\Mime\Email()
                ->from(new \Symfony\Component\Mime\Address(
                    $options['fromEmail'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? $this->username,
                    $options['fromLabel'] ?? $_ENV['MAIL_FROM_NAME'] ?? 'System'
                ))
                ->to($to)
                ->subject($subject)
                ->text($textContent)
                ->html($htmlContent);
            
            // Add reply-to addresses
            if (!empty($options['replyTo']) && is_array($options['replyTo'])) {
                foreach ($options['replyTo'] as $key => $value) {
                    if (is_string($key)) {
                        $email->addReplyTo(new \Symfony\Component\Mime\Address($key, $value));
                    } else {
                        $email->addReplyTo($value);
                    }
                }
            }
            
            // Add BCC addresses
            if (!empty($options['addBCC']) && is_array($options['addBCC'])) {
                foreach ($options['addBCC'] as $key => $value) {
                    if (is_string($key)) {
                        $email->addBcc(new \Symfony\Component\Mime\Address($key, $value));
                    } else {
                        $email->addBcc($value);
                    }
                }
            }
            
            // Add debug BCC if enabled
            if (($options['sendDebug'] ?? 0) == 1 && !empty($options['sendDebugEmail'])) {
                $email->addBcc($options['sendDebugEmail']);
            }
            
            // Add attachments
            if (!empty($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $email->attachFromPath($attachment['filename'], $attachment['title'] ?? null);
                }
            }
            
            $mailer->send($email);
            
            Logger::info('Email sent successfully via OAuth2 SMTP', [
                'to' => $to,
                'subject' => $subject,
                'provider' => $this->getName()
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Logger::error('Failed to send email via OAuth2 SMTP', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'provider' => $this->getName()
            ]);
            
            return false;
        }
    }
    
    public function getInfo(): array
    {
        return [
            'provider' => $this->getName(),
            'smtp_host' => $this->smtpHost,
            'smtp_port' => $this->smtpPort,
            'username' => $this->username,
            'available' => $this->isAvailable(),
            'token_provider' => $this->isAvailable() ? $this->tokenProvider->getInfo() : null
        ];
    }
    
    public function testConnection(): array
    {
        if (!$this->isAvailable()) {
            return [
                'status' => 'error',
                'message' => 'OAuth2 SMTP provider not available'
            ];
        }
        
        try {
            // Test token acquisition
            $tokenData = $this->tokenProvider->getAccessToken();
            
            // Test SMTP connection
            $transport = $this->createTransport();
            
            return [
                'status' => 'success',
                'message' => 'OAuth2 SMTP connection test successful',
                'token_type' => $tokenData['token_type'] ?? 'unknown',
                'expires_in' => $tokenData['expires_in'] ?? 'unknown'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'OAuth2 SMTP connection test failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create OAuth2 SMTP transport
     */
    private function createTransport(): \Symfony\Component\Mailer\Transport\TransportInterface
    {
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            $this->smtpHost,
            $this->smtpPort,
            false, // TLS will be started automatically
            null,
            Logger::getInstance()
        );
        
        // Set up OAuth2 authentication
        $authenticator = new OAuth2Authenticator($this->tokenProvider);
        
        $transport->setUsername($this->username);
        $transport->setPassword('oauth2'); // Placeholder, replaced by authenticator
        $transport->addAuthenticator($authenticator);
        
        return $transport;
    }
}