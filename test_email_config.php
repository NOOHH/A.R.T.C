<?php
// Test email configuration and sending
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
}

echo "=== Email Configuration Test ===\n";

// Read .env file directly
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    echo "Email settings from .env:\n";
    
    // Extract email settings
    preg_match('/MAIL_MAILER=(.*)/', $envContent, $mailer);
    preg_match('/MAIL_HOST=(.*)/', $envContent, $host);
    preg_match('/MAIL_PORT=(.*)/', $envContent, $port);
    preg_match('/MAIL_USERNAME=(.*)/', $envContent, $username);
    preg_match('/MAIL_PASSWORD=(.*)/', $envContent, $password);
    preg_match('/MAIL_ENCRYPTION=(.*)/', $envContent, $encryption);
    preg_match('/MAIL_FROM_ADDRESS=(.*)/', $envContent, $from);
    
    echo "MAIL_MAILER: " . ($mailer[1] ?? 'not set') . "\n";
    echo "MAIL_HOST: " . ($host[1] ?? 'not set') . "\n";
    echo "MAIL_PORT: " . ($port[1] ?? 'not set') . "\n";
    echo "MAIL_USERNAME: " . ($username[1] ?? 'not set') . "\n";
    echo "MAIL_PASSWORD: " . (isset($password[1]) ? '***' . substr($password[1], -4) : 'not set') . "\n";
    echo "MAIL_ENCRYPTION: " . ($encryption[1] ?? 'not set') . "\n";
    echo "MAIL_FROM_ADDRESS: " . ($from[1] ?? 'not set') . "\n";
    
    echo "\n=== Testing SMTP Connection ===\n";
    
    // Test SMTP connection
    $host = $host[1] ?? '';
    $port = $port[1] ?? '';
    $username = $username[1] ?? '';
    $password = $password[1] ?? '';
    
    if ($host && $port && $username && $password) {
        $socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if ($socket) {
            echo "✅ SMTP connection to {$host}:{$port} successful\n";
            fclose($socket);
        } else {
            echo "❌ SMTP connection failed: {$errstr} ({$errno})\n";
        }
    } else {
        echo "❌ Missing SMTP configuration\n";
    }
} else {
    echo "❌ .env file not found\n";
}

echo "\n=== Manual Email Test ===\n";
$testEmail = 'bmjustimbaste2003@gmail.com';

try {
    // Use PHP's mail() function as backup test
    $to = $testEmail;
    $subject = 'A.R.T.C Password Reset Test';
    $message = "This is a test email to verify email functionality.\n\nIf you receive this, the basic email system is working.";
    $headers = "From: bravo.teamproj@gmail.com\r\n";
    $headers .= "Reply-To: bravo.teamproj@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    echo "Attempting to send test email to {$testEmail}...\n";
    
    // This won't work with Gmail SMTP but will test basic PHP mail configuration
    if (function_exists('mail')) {
        echo "PHP mail() function is available\n";
    } else {
        echo "❌ PHP mail() function is not available\n";
    }
    
} catch (Exception $e) {
    echo "❌ Email test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Recommendations ===\n";
echo "1. Make sure Gmail 2-factor authentication is enabled\n";
echo "2. Use App Password instead of regular Gmail password\n";
echo "3. Check if 'Less secure app access' is disabled (which is correct)\n";
echo "4. Verify the App Password is correct: rwlgspjnqxkhfpst\n";
echo "5. Test with Laravel's mail system using php artisan tinker\n";
?>
