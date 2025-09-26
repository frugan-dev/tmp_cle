<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.GraphAPITransport.php v.1.0.0. 24/09/2025
 */

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\FileAttachment;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class GraphAPITransport implements TransportInterface
{
    private ?GraphServiceClient $graphClient = null;
    private readonly string $tenantId;
    private readonly string $clientId;
    private readonly string $clientSecret;
    private readonly string $userId;
    private readonly string $mailbox;
    private string $baseUrl;
    private readonly bool $mockEnabled;

    public function __construct()
    {
        $this->tenantId = $_ENV['MAIL_OAUTH2_TENANT_ID'] ?? '';
        $this->clientId = $_ENV['MAIL_OAUTH2_CLIENT_ID'] ?? '';
        $this->clientSecret = $_ENV['MAIL_OAUTH2_CLIENT_SECRET'] ?? '';
        $this->userId = $_ENV['MAIL_OAUTH2_GRAPH_USER_ID'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? '';
        $this->mailbox = $_ENV['MAIL_OAUTH2_GRAPH_MAILBOX'] ?? $this->userId;
        $this->baseUrl = $_ENV['MAIL_OAUTH2_GRAPH_BASE_URL'] ?? 'https://graph.microsoft.com/v1.0';
        $this->mockEnabled = ($_ENV['MAIL_GRAPH_API_MOCK_ENABLED'] ?? false) === true;

        if ($this->mockEnabled) {
            $this->baseUrl = $_ENV['MAIL_GRAPH_API_MOCK_URL'] ?? 'http://mock-graph-api:8080';
        }
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if (!$message instanceof Email) {
            throw new InvalidArgumentException('GraphAPITransport only supports Email messages');
        }

        try {
            if ($this->mockEnabled) {
                // Mock mode - call wiremock and send to Mailpit
                $this->sendEmailViaMockGraphAPI($message);
            } else {
                // Real mode - use actual Graph API
                $graphClient = $this->getGraphClient();
                $graphMessage = $this->convertEmailToGraphMessage($message);

                // Send email using Graph API
                if ($this->mailbox !== $this->userId) {
                    // Send as shared mailbox
                    $graphClient->users()->byUserId($this->mailbox)->sendMail()->post([
                        'message' => $graphMessage,
                        'saveToSentItems' => true,
                    ]);
                } else {
                    // Send as user
                    $graphClient->users()->byUserId($this->userId)->sendMail()->post([
                        'message' => $graphMessage,
                        'saveToSentItems' => true,
                    ]);
                }
            }

            Logger::info('Email sent successfully via Graph API Transport', [
                'to' => $this->getRecipientsString($message->getTo()),
                'subject' => $message->getSubject(),
                'mailbox' => $this->mailbox,
                'mock_mode' => $this->mockEnabled,
            ]);

            // Use a valid envelope or create one from the message
            $finalEnvelope = $envelope ?? Envelope::create($message);

            return new SentMessage($message, $finalEnvelope);

        } catch (Exception $e) {
            Logger::error('Failed to send email via Graph API Transport', [
                'exception' => $e,
                'to' => $this->getRecipientsString($message->getTo()),
                'subject' => $message->getSubject(),
                'mailbox' => $this->mailbox,
                'mock_mode' => $this->mockEnabled,
            ]);

            throw $e;
        }
    }

    /**
     * Send email via mock Graph API (wiremock) and also to Mailpit for     visualization
     */
    private function sendEmailViaMockGraphAPI(Email $message): void
    {
        Logger::debug('Graph API mock: Sending email via wiremock', [
            'mock_url' => $this->baseUrl,
            'to' => $this->getRecipientsString($message->getTo()),
            'subject' => $message->getSubject(),
        ]);

        // 1. Call mock Graph API (wiremock) to simulate the API behavior
        $this->callMockGraphAPI($message);

        // 2. Also send to Mailpit for visualization (like oauth2-smtp does)
        $this->sendToMailpit($message);
    }

    /**
     * Call the mock Graph API (wiremock) to simulate Microsoft Graph behavior
     */
    private function callMockGraphAPI(Email $message): void
    {
        Logger::debug('callMockGraphAPI: Starting');

        try {
            $graphMessage = $this->convertEmailToGraphMessage($message);

            // Prepare the request for wiremock (simulate Graph API sendMail    endpoint)
            $endpoint = rtrim($this->baseUrl, '/') . '/v1.0/me/sendMail';
            if ($this->mailbox !== $this->userId) {
                $endpoint = rtrim($this->baseUrl, '/') . '/v1.0/users/' . urlencode($this->mailbox) . '/sendMail';
            }

            $payload = [
                'message' => [
                    'subject' => $message->getSubject(),
                    'body' => [
                        'contentType' => $message->getHtmlBody() ? 'HTML' : 'Text',
                        'content' => $message->getHtmlBody() ?: $message->getTextBody(),
                    ],
                    'toRecipients' => array_map(function ($addr) {
                        return [
                            'emailAddress' => [
                                'address' => $addr->getAddress(),
                                'name' => $addr->getName(),
                            ],
                        ];
                    }, $message->getTo()),
                    'from' => $message->getFrom() ? [
                        'emailAddress' => [
                            'address' => $message->getFrom()[0]->getAddress(),
                            'name' => $message->getFrom()[0]->getName(),
                        ],
                    ] : null,
                ],
                'saveToSentItems' => true,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer mock-token',
                ],
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                // For local mock, disable SSL verification
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Logger::warning('Mock Graph API call failed', [
                    'error' => $error,
                    'endpoint' => $endpoint,
                ]);
            } else {
                Logger::debug('Mock Graph API call completed', [
                    'endpoint' => $endpoint,
                    'http_code' => $httpCode,
                    'response_length' => strlen($response),
                ]);
            }
        } catch (Exception $e) {
            Logger::error('callMockGraphAPI failed', [
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * Send email to Mailpit for visualization (similar to oauth2-smtp behavior)
     */
    private function sendToMailpit(Email $message): void
    {
        try {
            // Get Mailpit SMTP settings from environment
            $mailpitHost = $_ENV['MAIL_OAUTH2_SMTP_HOST'] ?? 'mailpit';
            $mailpitPort = (int)($_ENV['MAIL_OAUTH2_SMTP_PORT'] ?? 1025);

            Logger::debug('Sending email to Mailpit for visualization', [
                'host' => $mailpitHost,
                'port' => $mailpitPort,
            ]);

            // Create a simple SMTP transport for Mailpit
            $mailpitTransport = new EsmtpTransport(
                $mailpitHost,
                $mailpitPort,
                false, // no encryption for Mailpit
                null,
                Logger::getInstance()
            );

            $mailpitMailer = new Mailer($mailpitTransport);

            // Clone the message and add a header to identify it as from Graph  API mock
            $mailpitMessage = clone $message;
            $mailpitMessage->getHeaders()->addTextHeader('X-Graph-API-Mock', 'true');
            $mailpitMessage->getHeaders()->addTextHeader('X-Original-Transport', 'GraphAPITransport');

            $mailpitMailer->send($mailpitMessage);

            Logger::debug('Email sent to Mailpit successfully');

        } catch (Exception $e) {
            Logger::warning('Failed to send email to Mailpit', [
                'exception' => $e->getMessage(),
            ]);
            // Don't throw - this is just for visualization, not critical
        }
    }

    public function __toString(): string
    {
        // Use a valid DSN format with recognized scheme like in the gist example
        return $this->mockEnabled ? 'smtp://graph-api-mock' : 'smtp://graph-api-live';
    }

    /**
     * Convert Symfony Email to Graph API Message
     */
    private function convertEmailToGraphMessage(Email $email): Message
    {
        Logger::debug('convertEmailToGraphMessage: Starting conversion');

        try {
            $message = new Message();
            Logger::debug('convertEmailToGraphMessage: Message object created');

            $message->setSubject($email->getSubject());
            Logger::debug('convertEmailToGraphMessage: Subject set');

            // Set body - prefer HTML if available
            $body = new ItemBody();
            Logger::debug('convertEmailToGraphMessage: ItemBody created');

            if ($email->getHtmlBody()) {
                Logger::debug('convertEmailToGraphMessage: Setting HTML body');
                $body->setContentType(new BodyType(BodyType::HTML));
                $body->setContent($email->getHtmlBody());
            } else {
                Logger::debug('convertEmailToGraphMessage: Setting text body');
                $body->setContentType(new BodyType(BodyType::TEXT));
                $body->setContent($email->getTextBody());
            }
            Logger::debug('convertEmailToGraphMessage: Body content type and content set');

            $message->setBody($body);
            Logger::debug('convertEmailToGraphMessage: Body attached to message');

            // Set recipients
            if ($email->getTo()) {
                Logger::debug('convertEmailToGraphMessage: Processing TO recipients');
                $toRecipients = [];
                foreach ($email->getTo() as $address) {
                    $emailAddress = new EmailAddress();
                    $emailAddress->setAddress($address->getAddress());
                    if ($address->getName()) {
                        $emailAddress->setName($address->getName());
                    }

                    $recipient = new Recipient();
                    $recipient->setEmailAddress($emailAddress);

                    $toRecipients[] = $recipient;
                }
                $message->setToRecipients($toRecipients);
                Logger::debug('convertEmailToGraphMessage: TO recipients set');
            }

            // Set CC recipients
            if ($email->getCc()) {
                Logger::debug('convertEmailToGraphMessage: Processing CC recipients');
                $ccRecipients = [];
                foreach ($email->getCc() as $address) {
                    $emailAddress = new EmailAddress();
                    $emailAddress->setAddress($address->getAddress());
                    if ($address->getName()) {
                        $emailAddress->setName($address->getName());
                    }

                    $recipient = new Recipient();
                    $recipient->setEmailAddress($emailAddress);

                    $ccRecipients[] = $recipient;
                }
                $message->setCcRecipients($ccRecipients);
                Logger::debug('convertEmailToGraphMessage: CC recipients set');
            }

            // Set BCC recipients
            if ($email->getBcc()) {
                Logger::debug('convertEmailToGraphMessage: Processing BCC recipients');
                $bccRecipients = [];
                foreach ($email->getBcc() as $address) {
                    $emailAddress = new EmailAddress();
                    $emailAddress->setAddress($address->getAddress());
                    if ($address->getName()) {
                        $emailAddress->setName($address->getName());
                    }

                    $recipient = new Recipient();
                    $recipient->setEmailAddress($emailAddress);

                    $bccRecipients[] = $recipient;
                }
                $message->setBccRecipients($bccRecipients);
                Logger::debug('convertEmailToGraphMessage: BCC recipients set');
            }

            // Set from address
            if ($email->getFrom() && count($email->getFrom()) > 0) {
                Logger::debug('convertEmailToGraphMessage: Processing FROM address');
                $fromAddress = $email->getFrom()[0];

                $fromEmailAddress = new EmailAddress();
                $fromEmailAddress->setAddress($fromAddress->getAddress());
                if ($fromAddress->getName()) {
                    $fromEmailAddress->setName($fromAddress->getName());
                }

                $fromRecipient = new Recipient();
                $fromRecipient->setEmailAddress($fromEmailAddress);
                $message->setFrom($fromRecipient);
                Logger::debug('convertEmailToGraphMessage: FROM address set');
            }

            // Set reply-to addresses
            if ($email->getReplyTo()) {
                Logger::debug('convertEmailToGraphMessage: Processing REPLY-TO addresses');
                $replyToRecipients = [];
                foreach ($email->getReplyTo() as $address) {
                    $emailAddress = new EmailAddress();
                    $emailAddress->setAddress($address->getAddress());
                    if ($address->getName()) {
                        $emailAddress->setName($address->getName());
                    }

                    $recipient = new Recipient();
                    $recipient->setEmailAddress($emailAddress);

                    $replyToRecipients[] = $recipient;
                }
                $message->setReplyTo($replyToRecipients);
                Logger::debug('convertEmailToGraphMessage: REPLY-TO addresses set');
            }

            // Handle attachments
            if ($email->getAttachments()) {
                Logger::debug('convertEmailToGraphMessage: Processing attachments');
                $attachments = [];
                foreach ($email->getAttachments() as $attachment) {
                    $fileAttachment = new FileAttachment();
                    $fileAttachment->setName($attachment->getFilename() ?: 'attachment');
                    $fileAttachment->setContentType($attachment->getContentType() ?: 'application/octet-stream');
                    $fileAttachment->setContentBytes(base64_encode($attachment->getBody()));

                    $attachments[] = $fileAttachment;
                }
                $message->setAttachments($attachments);
                Logger::debug('convertEmailToGraphMessage: Attachments set');
            }

            Logger::debug('convertEmailToGraphMessage: Conversion completed successfully');
            return $message;

        } catch (Exception $e) {
            Logger::error('convertEmailToGraphMessage failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get Graph Service Client instance (only for live mode)
     */
    private function getGraphClient(): GraphServiceClient
    {
        if ($this->mockEnabled) {
            throw new LogicException('getGraphClient() should not be called in mock mode');
        }

        if ($this->graphClient === null) {
            // For real Graph API, we use the SDK's built-in authentication
            $tokenRequestContext = new ClientCredentialContext(
                $this->tenantId,
                $this->clientId,
                $this->clientSecret
            );

            $this->graphClient = new GraphServiceClient($tokenRequestContext);

            Logger::debug('Graph API client initialized', [
                'tenant_id' => $this->tenantId,
                'base_url' => $this->baseUrl,
            ]);
        }

        return $this->graphClient;
    }

    /**
     * Helper to get recipients as string for logging
     */
    private function getRecipientsString($recipients): string
    {
        if (!$recipients) {
            return '';
        }

        return implode(', ', array_map(fn ($addr) => $addr->getAddress(), $recipients));
    }

    /**
     * Check if transport is properly configured
     */
    public function isConfigured(): bool
    {
        $configured = !empty($this->tenantId) &&
                  !empty($this->clientId) &&
                  !empty($this->clientSecret) &&
                  !empty($this->userId);

        // Debug logging to identify missing configuration
        Logger::debug('GraphAPITransport configuration check', [
            'tenant_id' => !empty($this->tenantId) ? 'SET' : 'EMPTY',
            'client_id' => !empty($this->clientId) ? 'SET' : 'EMPTY',
            'client_secret' => !empty($this->clientSecret) ? 'SET' : 'EMPTY',
            'user_id' => $this->userId ?: 'EMPTY',
            'mailbox' => $this->mailbox ?: 'EMPTY',
            'configured' => $configured,
            'mock_enabled' => $this->mockEnabled,
        ]);

        return $configured;
    }
}
