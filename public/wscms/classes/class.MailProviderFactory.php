<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.MailProviderFactory.php v.1.0.0. 24/09/2025
 */

class MailProviderFactory
{
    private static array $providers = [];
    private static ?MailProviderInterface $activeProvider = null;
    
    /**
     * Get available mail providers
     */
    public static function getAvailableProviders(): array
    {
        if (empty(self::$providers)) {
            self::$providers = [
                'smtp-oauth2' => new OAuth2SMTPProvider(),
                'graph-api' => new GraphAPIProvider(),
            ];
        }
        
        return array_filter(self::$providers, fn($provider) => $provider->isAvailable());
    }
    
    /**
     * Get the best available provider
     */
    public static function getBestProvider(): ?MailProviderInterface
    {
        // Check if we have a manually configured provider
        $configuredProvider = $_ENV['MAIL_PROVIDER'] ?? '';
        
        if (!empty($configuredProvider)) {
            $provider = self::getProvider($configuredProvider);
            if ($provider && $provider->isAvailable()) {
                Logger::debug('Using configured mail provider', [
                    'provider' => $configuredProvider
                ]);
                return $provider;
            } else {
                Logger::warning('Configured mail provider not available', [
                    'provider' => $configuredProvider
                ]);
            }
        }
        
        // Fallback to first available provider
        $availableProviders = self::getAvailableProviders();
        
        if (empty($availableProviders)) {
            Logger::error('No mail providers available');
            return null;
        }
        
        $provider = reset($availableProviders);
        Logger::debug('Using fallback mail provider', [
            'provider' => $provider->getName()
        ]);
        
        return $provider;
    }
    
    /**
     * Get specific provider by name
     */
    public static function getProvider(string $name): ?MailProviderInterface
    {
        $providers = self::getProviders();
        return $providers[$name] ?? null;
    }
    
    /**
     * Get all providers (available and unavailable)
     */
    public static function getProviders(): array
    {
        if (empty(self::$providers)) {
            self::$providers = [
                'smtp-oauth2' => new OAuth2SMTPProvider(),
                'graph-api' => new GraphAPIProvider(),
            ];
        }
        
        return self::$providers;
    }
    
    /**
     * Send email using the best available provider
     */
    public static function sendEmail(string $to, string $subject, string $htmlContent, string $textContent, array $options = []): bool
    {
        $provider = self::getBestProvider();
        
        if (!$provider) {
            Logger::error('No mail provider available for sending email');
            return false;
        }
        
        Logger::debug('Sending email via provider', [
            'provider' => $provider->getName(),
            'to' => $to,
            'subject' => $subject
        ]);
        
        $success = $provider->sendEmail($to, $subject, $htmlContent, $textContent, $options);
        
        if (!$success) {
            // Try fallback to other providers
            $allProviders = self::getAvailableProviders();
            $currentProviderName = $provider->getName();
            
            foreach ($allProviders as $fallbackProvider) {
                if ($fallbackProvider->getName() === $currentProviderName) {
                    continue; // Skip the one we just tried
                }
                
                Logger::warning('Attempting fallback mail provider', [
                    'fallback_provider' => $fallbackProvider->getName(),
                    'failed_provider' => $currentProviderName
                ]);
                
                $success = $fallbackProvider->sendEmail($to, $subject, $htmlContent, $textContent, $options);
                
                if ($success) {
                    Logger::info('Fallback mail provider succeeded', [
                        'fallback_provider' => $fallbackProvider->getName(),
                        'failed_provider' => $currentProviderName
                    ]);
                    break;
                }
            }
        }
        
        return $success;
    }
    
    /**
     * Test all available providers
     */
    public static function testAllProviders(): array
    {
        $results = [];
        $providers = self::getProviders();
        
        foreach ($providers as $name => $provider) {
            $results[$name] = [
                'available' => $provider->isAvailable(),
                'info' => $provider->getInfo(),
                'connection_test' => $provider->isAvailable() ? $provider->testConnection() : [
                    'status' => 'skipped',
                    'message' => 'Provider not available'
                ]
            ];
        }
        
        return $results;
    }
    
    /**
     * Get provider statistics
     */
    public static function getProviderStats(): array
    {
        $providers = self::getProviders();
        $available = self::getAvailableProviders();
        $best = self::getBestProvider();
        
        return [
            'total_providers' => count($providers),
            'available_providers' => count($available),
            'provider_names' => array_keys($providers),
            'available_names' => array_map(fn($p) => $p->getName(), $available),
            'best_provider' => $best ? $best->getName() : null,
            'configured_provider' => $_ENV['MAIL_PROVIDER'] ?? 'auto'
        ];
    }
}