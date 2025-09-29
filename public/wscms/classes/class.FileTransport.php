<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.FileTransport.php v.1.0.0. 27/09/2025
 *
 * @see https://github.com/symfony/symfony/issues/33563
 */

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;

/**
 * File Transport for saving emails to files
 */
class FileTransport extends AbstractTransport
{
    private readonly string $filePath;

    public function __construct(string $filePath, private readonly bool $continueOnSuccess = false)
    {
        $this->filePath = rtrim($filePath, '/');

        // Ensure directory exists
        if (!is_dir($this->filePath)) {
            if (!mkdir($this->filePath, 0755, true) && !is_dir($this->filePath)) {
                throw new RuntimeException("Cannot create directory: {$this->filePath}");
            }
        }

        if (!is_writable($this->filePath)) {
            throw new RuntimeException("Directory is not writable: {$this->filePath}");
        }

        parent::__construct();
    }

    public function __toString(): string
    {
        $suffix = $this->continueOnSuccess ? '?continue=1' : '';
        return sprintf('file://%s%s', $this->filePath, $suffix);
    }

    /**
     * Get the file path where emails are saved
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    protected function doSend(SentMessage $message): void
    {
        $envelope = $message->getEnvelope();
        $rawMessage = $message->getOriginalMessage();

        try {
            // Generate filename with timestamp and random component
            $filename = sprintf(
                '%s_%s_%s.eml',
                date('Y-m-d_H-i-s'),
                uniqid(),
                substr(md5($envelope->getSender()->getAddress()), 0, 8)
            );

            $filepath = $this->filePath . '/' . $filename;

            // Save email as EML format
            $content = $rawMessage->toString();

            if (file_put_contents($filepath, $content) === false) {
                throw new RuntimeException("Failed to write email to file: {$filepath}");
            }

            Logger::debug('Email saved to file', [
                'transport' => 'file',
                'file' => $filepath,
                'to' => implode(', ', array_map(fn ($addr) => $addr->getAddress(), $envelope->getRecipients())),
                'subject' => $rawMessage instanceof Email ? $rawMessage->getSubject() : 'N/A',
                'size' => strlen($content),
                'continue_on_success' => $this->continueOnSuccess,
            ]);

            // If continue is enabled, "fail" to pass to next transport
            if ($this->continueOnSuccess) {
                throw new TransportException('File saved successfully, continuing to next transport');
            }

        } catch (Exception $e) {
            // If it's already a TransportException, re-throw as-is
            if ($e instanceof TransportException) {
                throw $e;
            }

            // Wrap other exceptions in TransportException for proper failover behavior
            throw new TransportException('File transport failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
