<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.OAuth2TransportFactoryDecorator.php v.1.0.0. 26/09/2025
 */

use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OAuth2TransportFactoryDecorator implements TransportFactoryInterface
{
    private EsmtpTransportFactory $innerFactory;

    public function __construct(
        EventDispatcherInterface $dispatcher = null,
        HttpClientInterface $httpClient = null,
        LoggerInterface $logger = null
    ) {
        $this->innerFactory = new EsmtpTransportFactory($dispatcher, $httpClient, $logger);
    }

    /**
     * Create transport from DSN with OAuth2 support
     */
    public function create(Dsn $dsn): TransportInterface
    {
        // Convert oauth2:// scheme to smtp:// for inner factory
        $adjustedDsn = $this->convertOAuth2DsnToSmtp($dsn);

        // Create transport using inner factory
        $transport = $this->innerFactory->create($adjustedDsn);

        // Configure OAuth2 if provider is specified or scheme was oauth2
        if (($dsn->getOption('oauth2_provider') || 'oauth2' === $dsn->getScheme()) && $transport instanceof EsmtpTransport) {
            $this->configureOAuth2Transport($transport, $dsn);
        }

        return $transport;
    }

    /**
     * Check if this factory supports the given DSN
     */
    public function supports(Dsn $dsn): bool
    {
        // Support oauth2:// scheme or smtp:// with oauth2_provider option
        if ('oauth2' === $dsn->getScheme()) {
            return true;
        }

        if ($dsn->getOption('oauth2_provider')) {
            return true;
        }

        return $this->innerFactory->supports($dsn);
    }

    /**
     * Configure OAuth2 authentication on transport
     */
    private function configureOAuth2Transport(EsmtpTransport $transport, Dsn $dsn): void
    {
        try {
            $provider = $dsn->getOption('oauth2_provider', 'microsoft');

            Logger::debug('Configuring OAuth2 transport', [
                'provider' => $provider,
                'host' => $dsn->getHost(),
                'port' => $dsn->getPort(),
                'scheme' => $dsn->getScheme(),
            ]);

            // Create token provider and authenticator
            $tokenProvider = Office365TokenProvider::createFromEnv();
            $authenticator = new OAuth2Authenticator($tokenProvider);

            // Add OAuth2 authenticator to transport
            $transport->addAuthenticator($authenticator);

            // Set placeholder password - will be replaced by authenticator
            $transport->setPassword('oauth2_token_placeholder');

            Logger::debug('OAuth2 transport configured successfully', [
                'provider' => $provider,
                'username' => $dsn->getUser(),
            ]);

        } catch (Exception $e) {
            Logger::error('Failed to configure OAuth2 transport', [
                'error' => $e->getMessage(),
                'provider' => $dsn->getOption('oauth2_provider'),
                'host' => $dsn->getHost(),
            ]);
            throw $e;
        }
    }

    /**
     * Convert oauth2:// DSN to smtp:// for inner factory
     */
    private function convertOAuth2DsnToSmtp(Dsn $dsn): Dsn
    {
        if ('oauth2' !== $dsn->getScheme()) {
            return $dsn;
        }

        return new Dsn(
            'smtp',
            $dsn->getHost(),
            $dsn->getUser(),
            '', // Empty password for OAuth2
            $dsn->getPort(),
            $dsn->getOptions()
        );
    }
}
