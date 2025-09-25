#!/usr/bin/env php
<?php
/**
 * Simple CLI script to send test email using Mails::sendEmail()
 * Usage: php test_mail.php email@example.com
 */

define('PATH', '../');

include_once(PATH."wscms/include/configuration.inc.php");

Config::setGlobalSettings($globalSettings);
Config::init();

if (!isset($argv[1]) || empty($argv[1])) {
    echo "Usage: php send_test_email.php email@example.com\n";
    exit(1);
}

$toEmail = $argv[1];

if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    echo "Error: Invalid email format: {$toEmail}\n";
    exit(1);
}

echo "Sending test email to: {$toEmail}\n";
echo "Using system configuration...\n\n";

$subject = 'Test Email - ' . date('Y-m-d H:i:s');
$htmlContent = '
<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h1>Test Email</h1>
    <p>This is a test email sent using the transport-based mail system.</p>
    <p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>
    <p><strong>Environment:</strong> ' . getEnvironment() . '</p>
</body>
</html>';

$textContent = "Test Email\n\nThis is a test email sent using the transport-based mail system.\n\nSent at: " . date('Y-m-d H:i:s') . "\Environment: " . getEnvironment();

$options = [
    'fromEmail' => $_ENV['MAIL_FROM_EMAIL'] ?? 'noreply@example.com',
    'fromLabel' => $_ENV['MAIL_FROM_NAME'] ?? 'Mail System Test',
];

try {
    Mails::sendEmail($toEmail, $subject, $htmlContent, $textContent, $options);
    
    if (Core::$resultOp->error === 0) {
        echo "✅ Email sent successfully!\n";
        echo "   To: {$toEmail}\n";
        echo "   Subject: {$subject}\n";
        echo "   From: {$options['fromEmail']} ({$options['fromLabel']})\n";
    } else {
        echo "❌ Email sending failed!\n";
        echo "   Check logs for details\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
}
