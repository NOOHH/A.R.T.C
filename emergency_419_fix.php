<?php

// Emergency 419 Error Fix Script
echo "🚨 EMERGENCY 419 ERROR FIX\n";
echo "==========================\n\n";

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. CLEARING ALL CACHES...\n";
echo "   - Config cache...\n";
system('php artisan config:clear');
echo "   - Application cache...\n";
system('php artisan cache:clear');
echo "   - Route cache...\n";
system('php artisan route:clear');
echo "   - View cache...\n";
system('php artisan view:clear');
echo "   - Compiled classes...\n";
system('php artisan clear-compiled');

echo "\n2. CHECKING SESSION CONFIGURATION...\n";
$config = config('session');
echo "   Driver: " . $config['driver'] . "\n";
echo "   Domain: " . ($config['domain'] ?? 'null') . "\n";
echo "   Secure: " . ($config['secure'] ? 'true' : 'false') . "\n";
echo "   Same Site: " . $config['same_site'] . "\n";
echo "   Cookie Name: " . $config['cookie'] . "\n";

echo "\n3. CHECKING ENVIRONMENT VARIABLES...\n";
$envVars = [
    'APP_URL' => env('APP_URL'),
    'APP_KEY' => env('APP_KEY') ? 'SET' : 'NOT SET',
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
    'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
    'SESSION_SAME_SITE' => env('SESSION_SAME_SITE')
];

foreach ($envVars as $var => $value) {
    $status = $value ? '✅' : '❌';
    echo "   $var = $value $status\n";
}

echo "\n4. CHECKING STORAGE PERMISSIONS...\n";
$sessionsPath = storage_path('framework/sessions');
echo "   Sessions directory: " . (is_dir($sessionsPath) ? 'EXISTS' : 'MISSING') . "\n";
echo "   Sessions writable: " . (is_writable($sessionsPath) ? 'YES' : 'NO') . "\n";

echo "\n5. CREATING TEST SESSION...\n";
try {
    $session = app('session');
    $session->put('test_key', 'test_value');
    echo "   Session test: SUCCESS\n";
} catch (Exception $e) {
    echo "   Session test: FAILED - " . $e->getMessage() . "\n";
}

echo "\n6. GENERATING CSRF TOKEN...\n";
try {
    $token = csrf_token();
    echo "   CSRF Token: " . substr($token, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "   CSRF Token: FAILED - " . $e->getMessage() . "\n";
}

echo "\n=== CRITICAL FIX REQUIRED ===\n";
echo "❌ SESSION_DOMAIN is set to a database hostname!\n";
echo "✅ SOLUTION: Set SESSION_DOMAIN= (blank) in Sevalla\n";
echo "\n=== AFTER FIXING SESSION_DOMAIN ===\n";
echo "1. Redeploy your application\n";
echo "2. Test login again\n";
echo "3. Check DevTools → Application → Cookies\n";
echo "4. You should see laravel_session cookie\n";
echo "\n=== END EMERGENCY FIX ===\n";

