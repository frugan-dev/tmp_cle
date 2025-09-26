<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.OAuth2Authenticator.php v.1.0.0. 24/09/2025
 */

use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator as BaseXOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\Auth\AuthenticatorInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class OAuth2Authenticator implements AuthenticatorInterface
{
    private BaseXOAuth2Authenticator $authenticator;

    public function __construct(private readonly Office365TokenProvider $tokenProvider)
    {
        Logger::debug('OAuth2Authenticator::getAuthKeyword called');
        
        $this->authenticator = new BaseXOAuth2Authenticator();
    }

    /**
     * Get authentication mode
     */
    public function getAuthKeyword(): string
    {
        return $this->authenticator->getAuthKeyword();
    }

    /**
     * Perform OAuth2 authentication
     */
    public function authenticate(EsmtpTransport $client): void
    {
        Logger::debug('OAuth2Authenticator starting authentication', [
            'username' => $client->getUsername(),
        ]);

        // Get fresh token
        $tokenData = $this->tokenProvider->getAccessToken();
        $accessToken = $tokenData['access_token'];

        // Set token as password temporarily
        $originalPassword = $client->getPassword();
        $client->setPassword($accessToken);

        try {
            // Use Symfony's tested authenticator
            $this->authenticator->authenticate($client);

            Logger::debug('OAuth2 authentication successful');
        } finally {
            // Restore original password
            $client->setPassword($originalPassword);
        }
    }
}
