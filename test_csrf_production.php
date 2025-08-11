<?php

// Simple CSRF Test for Production
echo "🔍 CSRF TOKEN TEST\n";
echo "==================\n\n";

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. BASIC CSRF TEST:\n";
echo "   ================\n";

try {
    $token = csrf_token();
    echo "   CSRF Token: " . $token . "\n";
    echo "   Token Length: " . strlen($token) . " characters\n";
    echo "   Token Valid: " . (strlen($token) > 0 ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "   CSRF Token Error: " . $e->getMessage() . "\n";
}

echo "\n2. SESSION TEST:\n";
echo "   =============\n";

try {
    $session = app('session');
    $sessionId = $session->getId();
    echo "   Session ID: " . $sessionId . "\n";
    echo "   Session Started: " . ($session->isStarted() ? 'YES' : 'NO') . "\n";
    
    // Test session write/read
    $testKey = 'csrf_test_' . time();
    $testValue = 'test_value_' . rand(1000, 9999);
    $session->put($testKey, $testValue);
    $readValue = $session->get($testKey);
    echo "   Session Write/Read: " . ($readValue === $testValue ? 'SUCCESS' : 'FAILED') . "\n";
    
} catch (Exception $e) {
    echo "   Session Error: " . $e->getMessage() . "\n";
}

echo "\n3. ENVIRONMENT CHECK:\n";
echo "   ==================\n";

$envVars = [
    'APP_URL' => env('APP_URL'),
    'APP_ENV' => env('APP_ENV'),
    'APP_DEBUG' => env('APP_DEBUG'),
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
    'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
    'SESSION_SAME_SITE' => env('SESSION_SAME_SITE')
];

foreach ($envVars as $var => $value) {
    echo "   $var = $value\n";
}

echo "\n4. CONFIGURATION CHECK:\n";
echo "   ====================\n";

$config = config('session');
echo "   Driver: " . $config['driver'] . "\n";
echo "   Domain: " . ($config['domain'] ?? 'null') . "\n";
echo "   Secure: " . ($config['secure'] ? 'true' : 'false') . "\n";
echo "   Same Site: " . $config['same_site'] . "\n";
echo "   Cookie Name: " . $config['cookie'] . "\n";

echo "\n=== RECOMMENDATIONS ===\n";

if (strlen($token) === 0) {
    echo "❌ CSRF Token is empty - this is the cause of 419 error\n";
    echo "🔧 SOLUTIONS:\n";
    echo "   1. Clear all caches in production\n";
    echo "   2. Verify environment variables are deployed\n";
    echo "   3. Check if session is starting properly\n";
} else {
    echo "✅ CSRF Token is working locally\n";
    echo "🔍 The issue is likely in production deployment\n";
    echo "📋 CHECK:\n";
    echo "   1. Are environment variables deployed?\n";
    echo "   2. Is the application redeployed?\n";
    echo "   3. Is there a CDN/proxy caching?\n";
}

echo "\n=== END TEST ===\n";

