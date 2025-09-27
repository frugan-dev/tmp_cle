<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.GraphAPITransport.php v.1.0.0. 24/09/2025
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
            Logger::debug('GraphAPITransport initialized in mock mode', ['base_url' => $this->baseUrl]);
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

            Logger::debug('Email sent successfully via Graph API Transport', [
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

    public function __toString(): string
    {
        // Use a valid DSN format with recognized scheme
        return $this->mockEnabled ? 'smtp://graph-api-mock' : 'smtp://graph-api-live';
    }

    /**
     * Check if transport is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->tenantId) &&
               !empty($this->clientId) &&
               !empty($this->clientSecret) &&
               !empty($this->userId);
    }

    /**
     * Send email via mock Graph API and also to Mailpit for visualization
     */
    private function sendEmailViaMockGraphAPI(Email $message): void
    {
        // Even though it is not used here in the mock, but only in production,
        // let's call it anyway to test its functioning
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
                'toRecipients' => array_map(fn ($addr) => [
                    'emailAddress' => [
                        'address' => $addr->getAddress(),
                        'name' => $addr->getName(),
                    ],
                ], $message->getTo()),
                'from' => $message->getFrom() ? [
                    'emailAddress' => [
                        'address' => $message->getFrom()[0]->getAddress(),
                        'name' => $message->getFrom()[0]->getName(),
                    ],
                ] : null,
            ],
            'saveToSentItems' => true,
        ];

        $options = [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer mock-token',
            ],
            'timeout' => 10,
            'connect_timeout' => 5,
            'verify' => false, // For local mock, disable SSL verification
        ];

        try {
            $client = new Client();
            $response = $client->post($endpoint, $options);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 202 && $statusCode !== 200) {
                Logger::warning('Mock Graph API call returned unexpected status', [
                    'http_code' => $statusCode,
                    'response' => $response->getBody()->getContents(),
                ]);
            }

        } catch (RequestException $e) {
            Logger::warning('Mock Graph API call failed', [
                'exception' => $e,
                'endpoint' => $endpoint,
            ]);

            if ($e->hasResponse()) {
                Logger::warning('Response details', [
                    'http_code' => $e->getResponse()->getStatusCode(),
                    'response' => $e->getResponse()->getBody()->getContents(),
                ]);
            }
        }

        // Also send to Mailpit for visualization
        try {
            $mailpitHost = $_ENV['MAIL_SMTP_HOST'] ?? 'mailpit';
            $mailpitPort = (int)($_ENV['MAIL_SMTP_PORT'] ?? 1025);

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

            // Clone the message and add a header to identify it as from Graph API mock
            $mailpitMessage = clone $message;
            $mailpitMessage->getHeaders()->addTextHeader('X-Graph-API-Mock', 'true');
            $mailpitMessage->getHeaders()->addTextHeader('X-Original-Transport', 'GraphAPITransport');

            $mailpitMailer->send($mailpitMessage);

        } catch (Exception $e) {
            Logger::warning('Failed to send email to Mailpit', [
                'exception' => $e,
            ]);
            // Don't throw - this is just for visualization, not critical
        }
    }

    /**
     * Convert Symfony Email to Graph API Message
     */
    private function convertEmailToGraphMessage(Email $email): Message
    {
        $message = new Message();
        $message->setSubject($email->getSubject());

        // Set body - prefer HTML if available
        $body = new ItemBody();
        if ($email->getHtmlBody()) {
            $body->setContentType(new BodyType(BodyType::HTML));
            $body->setContent($email->getHtmlBody());
        } else {
            $body->setContentType(new BodyType(BodyType::TEXT));
            $body->setContent($email->getTextBody());
        }
        $message->setBody($body);

        // Set recipients
        if ($email->getTo()) {
            $toRecipients = [];
            foreach ($email->getTo() as $toAddress) {
                $recipient = new Recipient();
                $emailAddr = new EmailAddress();
                $emailAddr->setAddress($toAddress->getAddress());
                if ($toAddress->getName()) {
                    $emailAddr->setName($toAddress->getName());
                }
                $recipient->setEmailAddress($emailAddr);
                $toRecipients[] = $recipient;
            }
            $message->setToRecipients($toRecipients);
        }

        // Set CC recipients
        if ($email->getCc()) {
            $ccRecipients = [];
            foreach ($email->getCc() as $ccAddress) {
                $recipient = new Recipient();
                $emailAddr = new EmailAddress();
                $emailAddr->setAddress($ccAddress->getAddress());
                if ($ccAddress->getName()) {
                    $emailAddr->setName($ccAddress->getName());
                }
                $recipient->setEmailAddress($emailAddr);
                $ccRecipients[] = $recipient;
            }
            $message->setCcRecipients($ccRecipients);
        }

        // Set BCC recipients
        if ($email->getBcc()) {
            $bccRecipients = [];
            foreach ($email->getBcc() as $bccAddress) {
                $recipient = new Recipient();
                $emailAddr = new EmailAddress();
                $emailAddr->setAddress($bccAddress->getAddress());
                if ($bccAddress->getName()) {
                    $emailAddr->setName($bccAddress->getName());
                }
                $recipient->setEmailAddress($emailAddr);
                $bccRecipients[] = $recipient;
            }
            $message->setBccRecipients($bccRecipients);
        }

        // Set From
        if ($email->getFrom()) {
            $fromArray = $email->getFrom();
            if (!empty($fromArray)) {
                $fromAddress = reset($fromArray);
                $from = new Recipient();
                $emailAddr = new EmailAddress();
                $emailAddr->setAddress($fromAddress->getAddress());
                if ($fromAddress->getName()) {
                    $emailAddr->setName($fromAddress->getName());
                }
                $from->setEmailAddress($emailAddr);
                $message->setFrom($from);
            }
        }

        // Handle attachments
        if ($email->getAttachments()) {
            $attachments = [];
            foreach ($email->getAttachments() as $attachment) {
                $fileAttachment = new FileAttachment();
                $fileAttachment->setName($attachment->getFilename() ?? 'attachment');
                $fileAttachment->setContentType($attachment->getContentType());
                $fileAttachment->setContentBytes(base64_encode($attachment->getBody()));
                $attachments[] = $fileAttachment;
            }
            $message->setAttachments($attachments);
        }

        return $message;
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
            $this->graphClient = new GraphServiceClient($tokenRequestContext, [], $this->baseUrl);
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
}
