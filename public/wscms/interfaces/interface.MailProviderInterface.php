<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 8.4
 * @copyright 2025 Websync
 * interfaces/interface.MailProviderInterface.php v.1.0.0. 24/09/2025
 */

interface MailProviderInterface 
{
    /**
     * Get provider name
     */
    public function getName(): string;
    
    /**
     * Check if provider is available/configured
     */
    public function isAvailable(): bool;
    
    /**
     * Send email using this provider
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject  
     * @param string $htmlContent HTML content
     * @param string $textContent Text content
     * @param array $options Additional options (from, attachments, etc.)
     * @return bool Success status
     */
    public function sendEmail(string $to, string $subject, string $htmlContent, string $textContent, array $options = []): bool;
    
    /**
     * Get provider configuration info (for debugging)
     */
    public function getInfo(): array;
    
    /**
     * Test provider connection/authentication
     */
    public function testConnection(): array;
}