<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * classes/class.OAuth2Cache.php v.1.0.0. 26/09/2025
 */

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class OAuth2Cache
{
    private static ?CacheInterface $cache = null;

    /**
     * Store OAuth2 token in cache
     */
    public static function storeToken(string $provider, array $tokenData, int $ttl): void
    {
        try {
            $cache = self::getCache();
            $cacheKey = self::getCacheKey($provider);

            // Add timestamp for validation
            $tokenData['cached_at'] = time();

            $cache->delete($cacheKey); // Clear existing

            $item = $cache->getItem($cacheKey);
            $item->set($tokenData);
            $item->expiresAfter($ttl);

            $cache->save($item);

            Logger::debug('OAuth2 token cached with Symfony Cache', [
                'provider' => $provider,
                'cache_key' => $cacheKey,
                'expires_in' => $ttl,
                'cached_at' => date('Y-m-d H:i:s'),
            ]);

        } catch (Exception $e) {
            Logger::error('Failed to cache OAuth2 token', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get OAuth2 token from cache
     */
    public static function getToken(string $provider): ?array
    {
        try {
            $cache = self::getCache();
            $cacheKey = self::getCacheKey($provider);

            $item = $cache->getItem($cacheKey);

            if (!$item->isHit()) {
                Logger::debug('OAuth2 token cache miss', [
                    'provider' => $provider,
                    'cache_key' => $cacheKey,
                ]);
                return null;
            }

            $tokenData = $item->get();

            Logger::debug('OAuth2 token cache hit', [
                'provider' => $provider,
                'cache_key' => $cacheKey,
                'cached_at' => isset($tokenData['cached_at']) ? date('Y-m-d H:i:s', $tokenData['cached_at']) : 'unknown',
            ]);

            return $tokenData;

        } catch (Exception $e) {
            Logger::error('Failed to retrieve OAuth2 token from cache', [
                'exception' => $e,
                'provider' => $provider,

            ]);
            return null;
        }
    }

    /**
     * Clear token from cache
     */
    public static function clearToken(string $provider): void
    {
        try {
            $cache = self::getCache();
            $cacheKey = self::getCacheKey($provider);

            $cache->delete($cacheKey);

            Logger::debug('OAuth2 token cleared from cache', [
                'provider' => $provider,
                'cache_key' => $cacheKey,
            ]);

        } catch (Exception $e) {
            Logger::error('Failed to clear OAuth2 token from cache', [
                'exception' => $e,
                'provider' => $provider,
            ]);
        }
    }

    /**
     * Clear all tokens from cache
     */
    public static function clearAll(): void
    {
        try {
            $cache = self::getCache();
            $cache->clear();

            Logger::debug('All OAuth2 tokens cleared from cache');

        } catch (Exception $e) {
            Logger::error('Failed to clear all OAuth2 tokens from cache', [
                'exception' => $e,
            ]);
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        try {
            $cache = self::getCache();

            // Basic stats - FilesystemAdapter doesn't provide detailed stats
            return [
                'adapter' => get_class($cache),
                'namespace' => 'oauth2_tokens',
                'directory' => (defined('PATH_CACHE_DIR') ? PATH_CACHE_DIR : sys_get_temp_dir()) . '/oauth2',
            ];

        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get cache instance (lazy initialization)
     */
    private static function getCache(): CacheInterface
    {
        if (self::$cache === null) {
            // Create filesystem cache in project's cache directory
            $cacheDir = defined('PATH_CACHE_DIR') ? PATH_CACHE_DIR : sys_get_temp_dir();
            self::$cache = new FilesystemAdapter(
                namespace: 'oauth2_tokens',
                defaultLifetime: 3600, // 1 hour default
                directory: $cacheDir . '/oauth2'
            );
        }

        return self::$cache;
    }

    /**
     * Get cache key for provider
     */
    private static function getCacheKey(string $provider): string
    {
        return 'token_' . str_replace(['-', ' ', '.'], '_', strtolower($provider));
    }
}
