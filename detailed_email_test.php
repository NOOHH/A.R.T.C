<?php

echo "Detailed Email Test\n";
echo "==================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

// Show mail configuration
echo "Current Mail Configuration:\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Test connection
echo "Testing SMTP connection...\n";

try {
    // Set timeout for testing
    ini_set('default_socket_timeout', 10);
    
    Mail::raw('This is a test email to verify your Laravel email configuration is working properly.', function($message) {
        $message->to('vince03handsome11@gmail.com')
               ->subject('Laravel Email Configuration Test');
    });
    
    echo "✓ Email sent successfully!\n";
    echo "Please check your email inbox (and spam folder) for the test message.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Error details:\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

?>
