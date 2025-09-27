<?php

/**
 * Custom File Transport for saving emails to files
 *
 * @see https://github.com/symfony/symfony/issues/33563
 */

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class FileTransport extends AbstractTransport
{
    private readonly string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = rtrim($filePath, '/');

        // Ensure directory exists
        if (!is_dir($this->filePath)) {
            if (!mkdir($this->filePath, 0755, true) && !is_dir($this->filePath)) {
                throw new RuntimeException("Cannot create directory: {$this->filePath}");
            }
        }

        parent::__construct();
    }

    public function __toString(): string
    {
        return sprintf('file://%s', $this->filePath);
    }

    protected function doSend(SentMessage $message): void
    {
        $envelope = $message->getEnvelope();
        $rawMessage = $message->getOriginalMessage();

        // Generate filename with timestamp and random component
        $filename = sprintf(
            '%s_%s_%s.eml',
            date('Y-m-d_H-i-s'),
            uniqid(),
            md5($envelope->getSender()->getAddress())
        );

        $filepath = $this->filePath . '/' . $filename;

        // Save email as EML format
        $content = $rawMessage->toString();

        if (file_put_contents($filepath, $content) === false) {
            throw new RuntimeException("Failed to write email to file: {$filepath}");
        }

        Logger::debug('Email saved to file', [
            'file' => $filepath,
            'to' => implode(', ', array_map(fn ($addr) => $addr->getAddress(), $envelope->getRecipients())),
            'subject' => $rawMessage instanceof Email ? $rawMessage->getSubject() : 'N/A',
        ]);
    }
}
