<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.FileTransport.php v.1.0.0. 27/09/2025
 */

use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * Factory for creating FileTransport instances
 */
class FileTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        // Validate scheme
        if (!in_array($dsn->getScheme(), $this->getSupportedSchemes(), true)) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" scheme is not supported; supported schemes for mailer "%s" are: "%s".',
                $dsn->getScheme(),
                static::class,
                implode('", "', $this->getSupportedSchemes())
            ));
        }

        // Extract path from DSN
        $path = $dsn->getHost();
        if ($dsn->getPath()) {
            $path .= $dsn->getPath();
        }

        // Handle default host
        if ($path === 'default') {
            $path = $_ENV['MAIL_FILE_PATH'] ?? '/tmp/emails';
        }

        // Validate path
        if (empty($path)) {
            throw new InvalidArgumentException('File transport requires a valid path in the DSN host or path component.');
        }

        return new FileTransport($path);
    }

    /**
     * Static factory method for easy instantiation
     */
    public static function createTransport(string $filePath): FileTransport
    {
        return new FileTransport($filePath);
    }

    protected function getSupportedSchemes(): array
    {
        return ['file'];
    }
}
