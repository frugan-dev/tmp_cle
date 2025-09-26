<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.Office365TokenProvider.php v.1.0.0. 24/09/2025
 */

class Office365TokenProvider
{
    private const string OAUTH_URL = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token';
    private const string SCOPE = 'https://outlook.office365.com/.default';
    private const string GRANT_TYPE = 'client_credentials';

    private readonly bool $mockEnabled;

    public function __construct(
        private readonly string $tenantId,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $scope = self::SCOPE
    ) {
        $this->mockEnabled = ($_ENV['MAIL_OAUTH2_MOCK_ENABLED'] ?? false) === true;
    }

    /**
     * Get OAuth2 access token
     */
    public function getAccessToken(): array
    {
        // Try to get from cache first
        $cachedToken = OAuth2Cache::getToken('microsoft-office365');

        if ($cachedToken && $this->isTokenValid($cachedToken)) {
            Logger::debug('Using cached OAuth2 token for Microsoft Office365');
            return $cachedToken;
        }

        // Fetch new token
        $tokenData = $this->fetchNewToken();

        // Cache the token (expires_in - 60 seconds buffer)
        $ttl = isset($tokenData['expires_in']) ? (int)$tokenData['expires_in'] - 60 : 3600;
        OAuth2Cache::storeToken('microsoft-office365', $tokenData, $ttl);

        return $tokenData;
    }

    /**
     * Fetch new token from Microsoft or Mock service
     */
    private function fetchNewToken(): array
    {
        if ($this->mockEnabled) {
            $mockUrl = $_ENV['MAIL_OAUTH2_MOCK_URL'] ?? 'http://mock-oauth2:8080';
            $tokenUrl = rtrim((string) $mockUrl, '/') . '/oauth2/token';

            Logger::debug('Using mock OAuth2 service', ['url' => $tokenUrl]);

            $postData = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
                'grant_type' => self::GRANT_TYPE,
            ];
        } else {
            $tokenUrl = sprintf(self::OAUTH_URL, $this->tenantId);

            $postData = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
                'grant_type' => self::GRANT_TYPE,
            ];
        }

        $response = $this->makeHttpRequest($tokenUrl, $postData);

        if (!isset($response['access_token'])) {
            throw new Exception('Failed to get OAuth2 token: ' . json_encode($response));
        }

        // Add timestamp for expiry checking
        $response['fetched_at'] = time();

        return $response;
    }

    /**
     * Check if token is still valid
     */
    private function isTokenValid(array $tokenData): bool
    {
        if (!isset($tokenData['expires_in']) || !isset($tokenData['fetched_at'])) {
            return false;
        }

        // Consider token invalid if it expires in less than 5 minutes
        $expiryTime = $tokenData['fetched_at'] + $tokenData['expires_in'] - 60; // 60s buffer
        return time() < $expiryTime;
    }

    /**
     * Make HTTP request to OAuth2 endpoint
     */
    private function makeHttpRequest(string $url, array $postData): array
    {
        $ch = curl_init();

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ];

        // For mock services, disable SSL verification
        if ($this->mockEnabled) {
            $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOptions[CURLOPT_SSL_VERIFYHOST] = false;

            Logger::debug('OAuth2 Mock mode: SSL verification disabled');
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception("cURL error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP error {$httpCode}: {$response}");
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Get provider information
     */
    public function getInfo(): array
    {
        $info = [
            'provider' => 'microsoft-office365',
            'tenant' => $this->tenantId,
            'scope' => $this->scope,
            'grant_type' => self::GRANT_TYPE,
            'mock_enabled' => $this->mockEnabled,
        ];

        if ($this->mockEnabled) {
            $info['mock_url'] = $_ENV['MAIL_OAUTH2_MOCK_URL'] ?? 'http://mock-oauth2:8080';
            $info['token_endpoint'] = rtrim((string) $info['mock_url'], '/') . '/oauth2/token';
        } else {
            $info['token_endpoint'] = sprintf(self::OAUTH_URL, $this->tenantId);
        }

        return $info;
    }

    /**
     * Create instance from environment variables
     */
    public static function createFromEnv(): self
    {
        $tenantId = $_ENV['MAIL_OAUTH2_TENANT_ID'] ?? '';
        $clientId = $_ENV['MAIL_OAUTH2_CLIENT_ID'] ?? '';
        $clientSecret = $_ENV['MAIL_OAUTH2_CLIENT_SECRET'] ?? '';
        $scope = $_ENV['MAIL_OAUTH2_SCOPE'] ?? self::SCOPE;

        if (empty($tenantId) || empty($clientId) || empty($clientSecret)) {
            throw new InvalidArgumentException('Missing OAuth2 credentials in environment variables');
        }

        return new self($tenantId, $clientId, $clientSecret, $scope);
    }
}
