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
use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Exception\TransportException;
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
        $provider = $_ENV['MAIL_OAUTH2_PROVIDER'] ?? throw new InvalidArgumentException('MAIL_OAUTH2_PROVIDER must be configured for Graph API transport');

        $this->tenantId = $_ENV['MAIL_OAUTH2_TENANT_ID'] ?? throw new InvalidArgumentException('MAIL_OAUTH2_TENANT_ID must be configured');
        $this->clientId = $_ENV['MAIL_OAUTH2_CLIENT_ID'] ?? throw new InvalidArgumentException('MAIL_OAUTH2_CLIENT_ID must be configured');
        $this->clientSecret = $_ENV['MAIL_OAUTH2_CLIENT_SECRET'] ?? throw new InvalidArgumentException('MAIL_OAUTH2_CLIENT_SECRET must be configured');
        $this->userId = $_ENV['MAIL_OAUTH2_GRAPH_USER_ID'] ?? $_ENV['MAIL_FROM_EMAIL'] ?? throw new InvalidArgumentException('MAIL_OAUTH2_GRAPH_USER_ID or MAIL_FROM_EMAIL must be configured');
        $this->mailbox = $_ENV['MAIL_OAUTH2_GRAPH_MAILBOX'] ?? $this->userId;

        // Graph API base URL must be explicitly configured
        $this->baseUrl = $_ENV['MAIL_OAUTH2_GRAPH_BASE_URL'] ?? throw new InvalidArgumentException("MAIL_OAUTH2_GRAPH_BASE_URL must be configured for provider: {$provider}");
        $this->mockEnabled = ($_ENV['MAIL_GRAPH_API_MOCK_ENABLED'] ?? false) === true;

        if ($this->mockEnabled) {
            $this->baseUrl = $_ENV['MAIL_GRAPH_API_MOCK_URL'] ?? 'http://mock-graph-api:8080';
            Logger::debug('Graph API Mock mode enabled', ['base_url' => $this->baseUrl]);
        }

        Logger::debug('Graph API Transport initialized', [
            'provider' => $provider,
            'base_url' => $this->baseUrl,
            'mock_enabled' => $this->mockEnabled,
        ]);
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

                // Check permissions before attempting to send
                $this->validateGraphAPIPermissions($graphClient);

                $graphMessage = $this->convertEmailToGraphMessage($message);

                // Create the request body object
                $requestBody = new SendMailPostRequestBody();
                $requestBody->setMessage($graphMessage);

                // If userId == mailbox, it's likely an application mailbox without standard folders
                $isApplicationMailbox = ($this->userId === $this->mailbox);
                $requestBody->setSaveToSentItems(!$isApplicationMailbox);

                // Send email using Graph API with proper request body object
                if ($this->mailbox !== $this->userId) {
                    // Send as shared mailbox
                    Logger::debug('Sending as shared mailbox', [
                        'user_id' => $this->userId,
                        'mailbox' => $this->mailbox,
                    ]);

                    $graphClient->users()->byUserId($this->mailbox)->sendMail()->post($requestBody);
                } else {
                    // Send as user/application account
                    Logger::debug('Sending as user/application account', [
                        'user_id' => $this->userId,
                    ]);

                    $graphClient->users()->byUserId($this->userId)->sendMail()->post($requestBody);
                }

                // Only validate sent items for shared mailbox scenarios (when userId != mailbox)
                if ($this->userId !== $this->mailbox) {
                    $this->validateEmailSent($graphClient);
                }
            }

            Logger::debug('Email sent successfully via Graph API Transport', [
                'to' => $this->getRecipientsString($message->getTo()),
                'subject' => $message->getSubject(),
                'user_id' => $this->userId,
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
                'user_id' => $this->userId,
                'mailbox' => $this->mailbox,
                'mock_mode' => $this->mockEnabled,
            ]);

            // Use TransportException so Symfony's FailoverTransport recognizes this as a transport failure
            throw new TransportException(
                'Graph API transport failed: ' . $e->getMessage(),
                0,
                $e
            );
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
     * Validate Graph API permissions - adapted for application vs shared mailbox scenarios
     */
    private function validateGraphAPIPermissions(GraphServiceClient $graphClient): void
    {
        try {
            // Always verify the authenticated user (userId) has basic Graph API access
            $user = $graphClient->users()->byUserId($this->userId)->get();

            // Check if the response is valid (not a rejected promise or null)
            if (!$user || !is_object($user) || $user instanceof RejectedPromise) {
                throw new RuntimeException('Graph API: Authentication failed or insufficient permissions for user: ' . $this->userId);
            }

            // Additional check for valid user object methods
            if (!method_exists($user, 'getDisplayName')) {
                throw new RuntimeException('Graph API: Invalid user object returned - authentication may have failed');
            }

            if ($this->mailbox === $this->userId) {
                // Application mailbox scenario: same account for auth and sending
                // Just verify basic access, don't test mail folders as they might not exist
                Logger::debug('Graph API application mailbox validated', [
                    'user_id' => $this->userId,
                    'display_name' => $user->getDisplayName(),
                ]);
            } else {
                // Shared mailbox scenario: different accounts for auth and sending
                // Verify full mail access permissions
                if (!$user->getMail()) {
                    throw new RuntimeException('Graph API: Authenticated user has no mail - insufficient permissions');
                }

                // Test access to the shared mailbox
                $mailboxUser = $graphClient->users()->byUserId($this->mailbox)->get();

                // Check mailbox user response validity
                if (!$mailboxUser || !is_object($mailboxUser) || $mailboxUser instanceof RejectedPromise) {
                    throw new RuntimeException('Graph API: Unable to access shared mailbox - insufficient permissions for ' . $this->mailbox);
                }

                if (!method_exists($mailboxUser, 'getDisplayName')) {
                    throw new RuntimeException('Graph API: Invalid mailbox user object returned - access may have failed');
                }

                // Test mail folder access for the shared mailbox
                $sharedMailFolders = $graphClient->users()->byUserId($this->mailbox)->mailFolders()->get();

                if (!$sharedMailFolders || $sharedMailFolders instanceof RejectedPromise) {
                    throw new RuntimeException('Graph API: Unable to access shared mailbox folders - insufficient Send As permissions for ' . $this->mailbox);
                }

                Logger::debug('Graph API shared mailbox validated', [
                    'user_id' => $this->userId,
                    'mailbox' => $this->mailbox,
                    'auth_user_mail' => $user->getMail(),
                    'mailbox_display_name' => $mailboxUser->getDisplayName(),
                ]);
            }

        } catch (Exception $e) {
            throw new RuntimeException('Graph API: Permission validation failed - ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Additional validation after sending - only for shared mailbox scenarios
     */
    private function validateEmailSent(GraphServiceClient $graphClient): void
    {
        try {
            // This method is only called when userId !== mailbox (shared mailbox scenario)
            // We check the mailbox where the email was actually sent from
            $checkMailbox = $this->mailbox;

            // Try to access sent items to verify the system is working
            $sentItems = $graphClient->users()->byUserId($checkMailbox)->mailFolders()->byMailFolderId('sentitems')->get();

            if (!$sentItems) {
                throw new RuntimeException('Graph API: Unable to access sent items folder - email delivery uncertain');
            }

            Logger::debug('Graph API post-send validation passed', [
                'checked_mailbox' => $checkMailbox,
                'scenario' => 'shared_mailbox',
            ]);

        } catch (Exception $e) {
            // Throw exception to trigger failover - email might not have been sent
            throw new RuntimeException('Graph API: Post-send validation failed - ' . $e->getMessage(), 0, $e);
        }
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
