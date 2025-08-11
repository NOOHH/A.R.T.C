<?php

// Comprehensive 419 Error Troubleshooting
echo "🔍 419 ERROR TROUBLESHOOTING\n";
echo "============================\n\n";

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. ENVIRONMENT VARIABLES CHECK:\n";
echo "   ===========================\n";
$envVars = [
    'APP_URL' => env('APP_URL'),
    'APP_KEY' => env('APP_KEY') ? 'SET' : 'NOT SET',
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'SESSION_DOMAIN' => env('SESSION_DOMAIN'),
    'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE'),
    'SESSION_SAME_SITE' => env('SESSION_SAME_SITE'),
    'APP_ENV' => env('APP_ENV'),
    'APP_DEBUG' => env('APP_DEBUG')
];

foreach ($envVars as $var => $value) {
    $status = $value ? '✅' : '❌';
    echo "   $var = $value $status\n";
}

echo "\n2. SESSION CONFIGURATION:\n";
echo "   ======================\n";
$config = config('session');
echo "   Driver: " . $config['driver'] . "\n";
echo "   Domain: " . ($config['domain'] ?? 'null') . "\n";
echo "   Secure: " . ($config['secure'] ? 'true' : 'false') . "\n";
echo "   Same Site: " . $config['same_site'] . "\n";
echo "   Cookie Name: " . $config['cookie'] . "\n";
echo "   Lifetime: " . $config['lifetime'] . "\n";
echo "   Path: " . $config['path'] . "\n";

echo "\n3. WEB MIDDLEWARE CHECK:\n";
echo "   ====================\n";
$kernel = app('Illuminate\Contracts\Http\Kernel');
$middlewareGroups = $kernel->getMiddlewareGroups();
if (isset($middlewareGroups['web'])) {
    echo "   Web middleware count: " . count($middlewareGroups['web']) . "\n";
    foreach ($middlewareGroups['web'] as $middleware) {
        echo "   - $middleware\n";
    }
} else {
    echo "   ❌ Web middleware group not found!\n";
}

echo "\n4. STORAGE PERMISSIONS:\n";
echo "   ===================\n";
$sessionsPath = storage_path('framework/sessions');
echo "   Sessions directory: " . (is_dir($sessionsPath) ? 'EXISTS' : 'MISSING') . "\n";
echo "   Sessions writable: " . (is_writable($sessionsPath) ? 'YES' : 'NO') . "\n";
echo "   Storage writable: " . (is_writable(storage_path()) ? 'YES' : 'NO') . "\n";

echo "\n5. SESSION TEST:\n";
echo "   ============\n";
try {
    $session = app('session');
    $session->put('test_key', 'test_value_' . time());
    $testValue = $session->get('test_key');
    echo "   Session write/read: SUCCESS ($testValue)\n";
    echo "   Session ID: " . $session->getId() . "\n";
} catch (Exception $e) {
    echo "   Session test: FAILED - " . $e->getMessage() . "\n";
}

echo "\n6. CSRF TOKEN TEST:\n";
echo "   ===============\n";
try {
    $token = csrf_token();
    echo "   CSRF Token: " . substr($token, 0, 20) . "...\n";
    echo "   Token length: " . strlen($token) . " characters\n";
} catch (Exception $e) {
    echo "   CSRF Token: FAILED - " . $e->getMessage() . "\n";
}

echo "\n7. FORCE HTTPS MIDDLEWARE CHECK:\n";
echo "   ============================\n";
$globalMiddleware = $kernel->middleware;
$forceHttpsFound = false;
foreach ($globalMiddleware as $middleware) {
    if (strpos($middleware, 'ForceHttps') !== false) {
        $forceHttpsFound = true;
        echo "   ForceHttps middleware: FOUND\n";
        break;
    }
}
if (!$forceHttpsFound) {
    echo "   ForceHttps middleware: NOT FOUND\n";
}

echo "\n8. COOKIE CONFIGURATION:\n";
echo "   ====================\n";
echo "   Cookie encryption: " . (config('session.encrypt') ? 'ENABLED' : 'DISABLED') . "\n";
echo "   HTTP only: " . (config('session.http_only') ? 'YES' : 'NO') . "\n";

echo "\n=== TROUBLESHOOTING RECOMMENDATIONS ===\n";

// Check for common issues
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

if (empty($issues)) {
    echo "✅ All environment variables appear correct\n";
    echo "🔍 The issue might be:\n";
    echo "   1. Changes not deployed yet\n";
    echo "   2. CDN/Proxy caching\n";
    echo "   3. Browser cache\n";
    echo "   4. Server configuration\n";
} else {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\n=== IMMEDIATE ACTIONS ===\n";
echo "1. Clear browser cache and cookies\n";
echo "2. Try incognito/private browsing mode\n";
echo "3. Check if changes are deployed in Sevalla\n";
echo "4. Verify APP_URL is correct in production\n";
echo "5. Check if there's a CDN or proxy in front\n";

echo "\n=== END TROUBLESHOOTING ===\n";
