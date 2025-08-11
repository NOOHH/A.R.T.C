<?php

// Comprehensive 419 Error Fix Script
echo "🔧 COMPREHENSIVE 419 ERROR FIX\n";
echo "==============================\n\n";

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. CLEARING ALL CACHES...\n";
echo "   =====================\n";
system('php artisan config:clear');
system('php artisan cache:clear');
system('php artisan route:clear');
system('php artisan view:clear');
system('php artisan clear-compiled');

echo "\n2. CHECKING TRUST PROXIES...\n";
echo "   ========================\n";
$trustProxiesPath = 'app/Http/Middleware/TrustProxies.php';
if (file_exists($trustProxiesPath)) {
    $content = file_get_contents($trustProxiesPath);
    if (strpos($content, 'HEADER_X_FORWARDED_FOR') !== false && 
        strpos($content, 'HEADER_X_FORWARDED_HOST') !== false &&
        strpos($content, 'HEADER_X_FORWARDED_PORT') !== false &&
        strpos($content, 'HEADER_X_FORWARDED_PROTO') !== false) {
        echo "   ✅ TrustProxies middleware is correctly configured\n";
    } else {
        echo "   ❌ TrustProxies middleware needs updating\n";
    }
} else {
    echo "   ❌ TrustProxies middleware file not found\n";
}

echo "\n3. CHECKING SESSION CONFIGURATION...\n";
echo "   ===============================\n";
$config = config('session');
echo "   Driver: " . $config['driver'] . "\n";
echo "   Domain: " . ($config['domain'] ?? 'null') . "\n";
echo "   Secure: " . ($config['secure'] ? 'true' : 'false') . "\n";
echo "   Same Site: " . $config['same_site'] . "\n";
echo "   Cookie Name: " . $config['cookie'] . "\n";

echo "\n4. CHECKING STORAGE PERMISSIONS...\n";
echo "   =============================\n";
$sessionsPath = storage_path('framework/sessions');
echo "   Sessions directory: " . (is_dir($sessionsPath) ? 'EXISTS' : 'MISSING') . "\n";
echo "   Sessions writable: " . (is_writable($sessionsPath) ? 'YES' : 'NO') . "\n";
echo "   Storage writable: " . (is_writable(storage_path()) ? 'YES' : 'NO') . "\n";

echo "\n5. CHECKING WEB MIDDLEWARE...\n";
echo "   =========================\n";
$kernel = app('Illuminate\Contracts\Http\Kernel');
$middlewareGroups = $kernel->getMiddlewareGroups();
if (isset($middlewareGroups['web'])) {
    echo "   Web middleware count: " . count($middlewareGroups['web']) . "\n";
    $required = [
        'EncryptCookies',
        'AddQueuedCookiesToResponse', 
        'StartSession',
        'ShareErrorsFromSession',
        'VerifyCsrfToken',
        'SubstituteBindings'
    ];
    
    foreach ($required as $middleware) {
        $found = false;
        foreach ($middlewareGroups['web'] as $webMiddleware) {
            if (strpos($webMiddleware, $middleware) !== false) {
                $found = true;
                break;
            }
        }
        echo "   $middleware: " . ($found ? '✅ FOUND' : '❌ MISSING') . "\n";
    }
} else {
    echo "   ❌ Web middleware group not found!\n";
}

echo "\n6. TESTING SESSION FUNCTIONALITY...\n";
echo "   ===============================\n";
try {
    $session = app('session');
    
    // Force session start
    if (!$session->isStarted()) {
        $session->start();
    }
    
    // Test session write/read
    $testKey = 'fix_test_' . time();
    $testValue = 'test_value_' . rand(1000, 9999);
    $session->put($testKey, $testValue);
    $readValue = $session->get($testKey);
    
    echo "   Session ID: " . $session->getId() . "\n";
    echo "   Session Started: " . ($session->isStarted() ? 'YES' : 'NO') . "\n";
    echo "   Session Write/Read: " . ($readValue === $testValue ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Test CSRF token
    $token = csrf_token();
    echo "   CSRF Token Length: " . strlen($token) . " characters\n";
    echo "   CSRF Token Valid: " . (strlen($token) > 0 ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "   Session Error: " . $e->getMessage() . "\n";
}

echo "\n7. ENVIRONMENT VARIABLES CHECK...\n";
echo "   =============================\n";
$envVars = [
    'APP_URL' => env('APP_URL'),
    'APP_ENV' => env('APP_ENV'),
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
    'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
    'SESSION_SAME_SITE' => env('SESSION_SAME_SITE')
];

foreach ($envVars as $var => $value) {
    $status = $value ? '✅' : '❌';
    echo "   $var = $value $status\n";
}

echo "\n=== DIAGNOSTIC SUMMARY ===\n";

$issues = [];

if (env('APP_URL') !== 'https://laravel-zfurp.sevalla.app') {
    $issues[] = "APP_URL should be https://laravel-zfurp.sevalla.app";
}

if (env('SESSION_DOMAIN') !== null && env('SESSION_DOMAIN') !== '') {
    $issues[] = "SESSION_DOMAIN should be blank";
}

if (env('SESSION_SECURE_COOKIE') !== 'true') {
    $issues[] = "SESSION_SECURE_COOKIE should be true";
}

if (env('SESSION_SAME_SITE') !== 'lax') {
    $issues[] = "SESSION_SAME_SITE should be lax";
}

if (!is_writable($sessionsPath)) {
    $issues[] = "Sessions directory is not writable";
}

if (empty($issues)) {
    echo "✅ All configurations appear correct\n";
    echo "🎯 NEXT STEPS:\n";
    echo "   1. Deploy these changes to production\n";
    echo "   2. Clear caches in production\n";
    echo "   3. Test /cookie-test endpoint\n";
    echo "   4. Check if laravel_session cookie appears\n";
} else {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Deploy to production\n";
echo "2. Visit: https://laravel-zfurp.sevalla.app/cookie-test\n";
echo "3. Check DevTools → Application → Cookies\n";
echo "4. You should see laravel_session cookie\n";
echo "5. If cookie appears, try login again\n";

echo "\n=== END FIX ===\n";

