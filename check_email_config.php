<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Email Configuration Check ===\n\n";

// Check if .env file exists
if (file_exists('.env')) {
    echo "✅ .env file exists\n";
} else {
    echo "❌ .env file does not exist\n";
    echo "   You need to create a .env file with email configuration\n\n";
}

// Check email configuration
echo "Current Email Configuration:\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'not set') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'not set') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'not set') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME', 'not set') . "\n";
echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? 'set' : 'not set') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION', 'not set') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'not set') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME', 'not set') . "\n\n";

// Check if email exists in database
echo "Checking if bmjustimbaste2003@gmail.com exists in database...\n";
$email = 'bmjustimbaste2003@gmail.com';

try {
    $db = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . 
        ';dbname=' . env('DB_DATABASE', 'artc') . 
        ';port=' . env('DB_PORT', '3306'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    
    // Check all tables
    $tables = [
        'students' => 'email',
        'admins' => 'email', 
        'professors' => 'professor_email',
        'directors' => 'directors_email'
    ];
    
    $found = false;
    foreach ($tables as $table => $column) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table WHERE $column = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "$table table: " . $count . " records found\n";
        if ($count > 0) {
            $found = true;
        }
    }
    
    if ($found) {
        echo "✅ Email found in database\n";
    } else {
        echo "❌ Email not found in any table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== SOLUTION ===\n";
echo "To fix the password reset email issue:\n\n";
echo "1. Create a .env file in your project root with the following email configuration:\n\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=your-gmail@gmail.com\n";
echo "MAIL_PASSWORD=your-app-password\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=your-gmail@gmail.com\n";
echo "MAIL_FROM_NAME=\"A.R.T.C\"\n\n";
echo "2. For Gmail, you need to:\n";
echo "   - Enable 2-factor authentication\n";
echo "   - Generate an App Password\n";
echo "   - Use the App Password as MAIL_PASSWORD\n\n";
echo "3. Clear Laravel cache:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n\n";
echo "4. Test the email functionality\n\n"; 