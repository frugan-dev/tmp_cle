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
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;

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
        Logger::debug('OAuth2TransportFactoryDecorator::create called', [
            'scheme' => $dsn->getScheme(),
            'host' => $dsn->getHost(),
            'port' => $dsn->getPort(),
            'oauth2_provider' => $dsn->getOption('oauth2_provider'),
        ]);

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
        Logger::debug('OAuth2TransportFactoryDecorator::supports called', [
            'scheme' => $dsn->getScheme(),
            'oauth2_provider' => $dsn->getOption('oauth2_provider'),
        ]);

        // Support oauth2:// scheme or smtp:// with oauth2_provider option
        if ('oauth2' === $dsn->getScheme()) {
            Logger::debug('OAuth2TransportFactoryDecorator supports oauth2:// scheme');
            return true;
        }

        if ($dsn->getOption('oauth2_provider')) {
            Logger::debug('OAuth2TransportFactoryDecorator supports smtp:// with oauth2_provider');
            return true;
        }

        // Delegate to inner factory for regular SMTP
        $supports = $this->innerFactory->supports($dsn);
        Logger::debug('OAuth2TransportFactoryDecorator delegating to inner factory', [
            'supports' => $supports,
        ]);

        return $supports;
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
                'username' => $dsn->getUser(),
                'host' => $dsn->getHost(),
                'port' => $dsn->getPort(),
                'scheme' => $dsn->getScheme(),
            ]);

            // Get OAuth2 token
            $tokenProvider = Office365TokenProvider::createFromEnv();
            $tokenData = $tokenProvider->getAccessToken();
            $accessToken = $tokenData['access_token'];

            // Configure transport for built-in XOAuth2Authenticator
            $transport->setUsername($dsn->getUser()); // Real email
            $transport->setPassword($accessToken); // Token OAuth2

            if (isDevelop()) {
                // Remove all authenticators except XOAUTH2
                $reflection = new ReflectionClass($transport);
                $property = $reflection->getProperty('authenticators');
                $property->setAccessible(true);

                $authenticators = $property->getValue($transport);
                $xoauth2Only = array_filter($authenticators, function ($auth) {
                    return $auth instanceof XOAuth2Authenticator;
                });

                $property->setValue($transport, array_values($xoauth2Only));

                Logger::debug('OAuth2 transport configured with built-in authenticator', [
                    'provider' => $provider,
                    'username' => $dsn->getUser(),
                    'authenticator_count' => count($xoauth2Only),
                ]);
            }

            Logger::debug('OAuth2 token obtained', [
                'token_length' => strlen($accessToken),
                'token_preview' => substr($accessToken, 0, 20) . '...',
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

        Logger::debug('Converting oauth2:// DSN to smtp://', [
            'original_scheme' => $dsn->getScheme(),
            'host' => $dsn->getHost(),
            'port' => $dsn->getPort(),
        ]);

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
