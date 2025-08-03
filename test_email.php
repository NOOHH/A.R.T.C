<?php

echo "Testing Email Configuration...\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('This is a test email from your Laravel application. If you receive this, your email configuration is working correctly!', function($message) {
        $message->to('vince03handsome11@gmail.com')
               ->subject('Laravel Email Test - Success!');
    });
    echo "✓ Email sent successfully!\n";
    echo "Check your email inbox for the test message.\n";
} catch (Exception $e) {
    echo "✗ Error sending email: " . $e->getMessage() . "\n";
}

?>
