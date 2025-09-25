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
        $this->mockEnabled = ($_ENV['MAIL_GRAPH_API_MOCK_ENABLED'] ?? 'false') === 'true';
        
        if ($this->mockEnabled) {
            $this->baseUrl = $_ENV['MAIL_GRAPH_API_MOCK_URL'] ?? 'http://mock-graph-api:8080';
        }
    }
    
    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if (!$message instanceof Email) {
            throw new \InvalidArgumentException('GraphAPITransport only supports Email messages');
        }
        
        try {
            $graphClient = $this->getGraphClient();
            $graphMessage = $this->convertEmailToGraphMessage($message);
            
            // Send email using Graph API
            if ($this->mailbox !== $this->userId) {
                // Send as shared mailbox
                $graphClient->users()->byUserId($this->mailbox)->sendMail()->post([
                    'message' => $graphMessage,
                    'saveToSentItems' => true
                ]);
            } else {
                // Send as user
                $graphClient->users()->byUserId($this->userId)->sendMail()->post([
                    'message' => $graphMessage,
                    'saveToSentItems' => true
                ]);
            }
            
            Logger::info('Email sent successfully via Graph API Transport', [
                'to' => $this->getRecipientsString($message->getTo()),
                'subject' => $message->getSubject(),
                'mailbox' => $this->mailbox
            ]);
            
            return new SentMessage($message, 'graph-api');
            
        } catch (Exception $e) {
            Logger::error('Failed to send email via Graph API Transport', [
                'error' => $e->getMessage(),
                'to' => $this->getRecipientsString($message->getTo()),
                'subject' => $message->getSubject()
            ]);
            
            throw $e;
        }
    }
    
    public function __toString(): string
    {
        return sprintf('graph-api://graph.microsoft.com');
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
            $body->setContentType(BodyType::Html);
            $body->setContent($email->getHtmlBody());
        } else {
            $body->setContentType(BodyType::Text);
            $body->setContent($email->getTextBody());
        }
        $message->setBody($body);
        
        // Set recipients
        if ($email->getTo()) {
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
        }
        
        // Set CC recipients
        if ($email->getCc()) {
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
        }
        
        // Set BCC recipients
        if ($email->getBcc()) {
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
        }
        
        // Set from address  
        if ($email->getFrom() && count($email->getFrom()) > 0) {
            $fromAddress = $email->getFrom()[0];
            
            $fromEmailAddress = new EmailAddress();
            $fromEmailAddress->setAddress($fromAddress->getAddress());
            if ($fromAddress->getName()) {
                $fromEmailAddress->setName($fromAddress->getName());
            }
            
            $fromRecipient = new Recipient();
            $fromRecipient->setEmailAddress($fromEmailAddress);
            $message->setFrom($fromRecipient);
        }
        
        // Set reply-to addresses
        if ($email->getReplyTo()) {
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
        }
        
        // Handle attachments
        if ($email->getAttachments()) {
            $attachments = [];
            foreach ($email->getAttachments() as $attachment) {
                $fileAttachment = new FileAttachment();
                $fileAttachment->setName($attachment->getFilename() ?: 'attachment');
                $fileAttachment->setContentType($attachment->getContentType() ?: 'application/octet-stream');
                $fileAttachment->setContentBytes(base64_encode($attachment->getBody()));
                
                $attachments[] = $fileAttachment;
            }
            $message->setAttachments($attachments);
        }
        
        return $message;
    }
    
    /**
     * Get Graph Service Client instance
     */
    private function getGraphClient(): GraphServiceClient
    {
        if ($this->graphClient === null) {
            if ($this->mockEnabled) {
                // For mock mode, we'll create a simplified client
                // In reality, this would need custom HTTP client configuration
                Logger::debug('Graph API mock mode enabled', [
                    'mock_url' => $this->baseUrl
                ]);
                
                // For now, throw exception as mock implementation is complex
                throw new Exception('Mock Graph API client not yet implemented - use real credentials or disable mock');
            } else {
                // For real Graph API, we can use the token provider approach
                // But Microsoft Graph SDK expects ClientCredentialContext, not our token provider
                // So we'll use the SDK's built-in authentication
                $tokenRequestContext = new ClientCredentialContext(
                    $this->tenantId,
                    $this->clientId,
                    $this->clientSecret
                );
                
                $this->graphClient = new GraphServiceClient($tokenRequestContext);
                
                Logger::debug('Graph API client initialized', [
                    'tenant_id' => $this->tenantId,
                    'base_url' => $this->baseUrl
                ]);
            }
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
        
        return implode(', ', array_map(fn($addr) => $addr->getAddress(), $recipients));
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
}