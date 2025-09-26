<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.OAuth2Authenticator.php v.1.0.0. 24/09/2025
 */

use Symfony\Component\Mailer\Transport\Smtp\Auth\AuthenticatorInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class OAuth2Authenticator implements AuthenticatorInterface
{
    public function __construct(private readonly Office365TokenProvider $tokenProvider)
    {
    }

    /**
     * Get authentication mode
     */
    public function getAuthKeyword(): string
    {
        return 'XOAUTH2';
    }

    /**
     * Perform OAuth2 authentication
     */
    public function authenticate(EsmtpTransport $client): void
    {
        try {
            $tokenData = $this->tokenProvider->getAccessToken();
            $accessToken = $tokenData['access_token'];

            $username = $client->getUsername();

            // Build XOAUTH2 string
            // Format: user={email}\x01auth=Bearer {token}\x01\x01
            $authString = sprintf(
                "user=%s\x01auth=Bearer %s\x01\x01",
                $username,
                $accessToken
            );

            $authString = base64_encode($authString);

            // Send AUTH XOAUTH2 command
            $client->executeCommand("AUTH XOAUTH2 {$authString}\r\n", [235]);

            Logger::debug('OAuth2 authentication successful', [
                'provider' => 'microsoft-office365',
                'username' => $username,
            ]);

        } catch (Exception $exception) {
            Logger::error($exception->getMessage(), [
                'exception' => $exception,
                'provider' => 'microsoft-office365',
            ]);

            throw $e;
        }
    }
}
