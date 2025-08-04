<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Password Reset Email Test ===\n\n";

// Test email configuration
echo "1. Checking email configuration...\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'not set') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'not set') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'not set') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME', 'not set') . "\n";
echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? 'set' : 'not set') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION', 'not set') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'not set') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME', 'not set') . "\n\n";

// Test if the email exists in the database
echo "2. Checking if email exists in database...\n";
$email = 'bmjustimbaste2003@gmail.com';

try {
    $db = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . 
        ';dbname=' . env('DB_DATABASE', 'artc') . 
        ';port=' . env('DB_PORT', '3306'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    
    // Check students table
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $studentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Students table: " . $studentCount . " records found\n";
    
    // Check admins table
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Admins table: " . $adminCount . " records found\n";
    
    // Check professors table
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM professors WHERE professor_email = ?");
    $stmt->execute([$email]);
    $professorCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Professors table: " . $professorCount . " records found\n";
    
    // Check directors table
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM directors WHERE directors_email = ?");
    $stmt->execute([$email]);
    $directorCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Directors table: " . $directorCount . " records found\n\n";
    
    if ($studentCount + $adminCount + $professorCount + $directorCount == 0) {
        echo "❌ Email not found in any table!\n";
        exit;
    }
    
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
    exit;
}

// Test email sending
echo "3. Testing email sending...\n";

try {
    Mail::raw("Test email from A.R.T.C system", function ($message) use ($email) {
        $message->to($email)
                ->subject('A.R.T.C - Test Email');
    });
    
    echo "✅ Test email sent successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Email sending failed: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
