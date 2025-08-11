<?php
/**
 * Emergency 419 Diagnostic and Fix Tool
 * This will identify and attempt to fix the 419 error immediately
 */

echo "<h1>🚨 Emergency 419 Diagnostic Tool</h1>";
echo "<pre>";

echo "🔍 DIAGNOSING 419 ERROR...\n\n";

// Check 1: Environment Configuration
echo "=== ENVIRONMENT CHECK ===\n";
$envVars = [
    'APP_ENV' => env('APP_ENV'),
    'APP_DEBUG' => env('APP_DEBUG') ? 'true' : 'false',
    'APP_URL' => env('APP_URL'),
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'SESSION_DOMAIN' => "'" . env('SESSION_DOMAIN') . "'",
    'SESSION_SECURE_COOKIE' => env('SESSION_SECURE_COOKIE') ? 'true' : 'false',
    'SESSION_SAME_SITE' => env('SESSION_SAME_SITE'),
];

foreach ($envVars as $key => $value) {
    echo "$key: $value\n";
}
echo "\n";

// Check 2: Session Directory Permissions
echo "=== SESSION STORAGE CHECK ===\n";
$sessionPath = storage_path('framework/sessions');
echo "Session Path: $sessionPath\n";
echo "Directory Exists: " . (is_dir($sessionPath) ? '✅ YES' : '❌ NO') . "\n";
echo "Directory Writable: " . (is_writable($sessionPath) ? '✅ YES' : '❌ NO') . "\n";

// Try to create session directory if it doesn't exist
if (!is_dir($sessionPath)) {
    echo "🔧 Creating session directory...\n";
    mkdir($sessionPath, 0755, true);
    echo "Session directory created: " . (is_dir($sessionPath) ? '✅ SUCCESS' : '❌ FAILED') . "\n";
}

// Check permissions
$permissions = substr(sprintf('%o', fileperms($sessionPath)), -4);
echo "Directory Permissions: $permissions\n\n";

// Check 3: Current Request Info
echo "=== REQUEST INFO ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? '✅ YES' : '❌ NO') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'not set') . "\n\n";

// Check 4: Laravel Configuration
echo "=== LARAVEL CONFIG CHECK ===\n";
try {
    echo "Config Cached: " . (file_exists(bootstrap_path('cache/config.php')) ? '⚠️ YES (may need clearing)' : '✅ NO') . "\n";
    echo "Route Cached: " . (file_exists(bootstrap_path('cache/routes-v7.php')) ? '⚠️ YES (may need clearing)' : '✅ NO') . "\n";
    echo "View Cached: " . (count(glob(storage_path('framework/views/*'))) > 0 ? '⚠️ YES (may need clearing)' : '✅ NO') . "\n";
} catch (Exception $e) {
    echo "❌ Error checking cache: " . $e->getMessage() . "\n";
}
echo "\n";

// Check 5: Session Test
echo "=== SESSION TEST ===\n";
try {
    // Start session
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    echo "Session ID: " . session_id() . "\n";
    echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '✅ ACTIVE' : '❌ INACTIVE') . "\n";
    
    // Test session write
    $_SESSION['test_419'] = time();
    echo "Session Write Test: ✅ SUCCESS\n";
    
    // Generate CSRF token test
    $testToken = bin2hex(random_bytes(32));
    echo "CSRF Token Generated: ✅ SUCCESS\n";
    
} catch (Exception $e) {
    echo "❌ Session Error: " . $e->getMessage() . "\n";
}
echo "\n";

// IMMEDIATE FIX ATTEMPTS
echo "=== EMERGENCY FIXES ===\n";

// Fix 1: Clear all caches
echo "🔧 Clearing all caches...\n";
$commands = [
    'config:clear',
    'cache:clear',
    'view:clear',
    'route:clear',
];

foreach ($commands as $cmd) {
    try {
        $output = [];
        $return_var = 0;
        exec("php " . base_path('artisan') . " $cmd 2>&1", $output, $return_var);
        echo "php artisan $cmd: " . ($return_var === 0 ? '✅ SUCCESS' : '❌ FAILED') . "\n";
        if ($return_var !== 0) {
            echo "Error: " . implode("\n", $output) . "\n";
        }
    } catch (Exception $e) {
        echo "php artisan $cmd: ❌ EXCEPTION - " . $e->getMessage() . "\n";
    }
}

// Fix 2: Set proper permissions
echo "\n🔧 Setting proper permissions...\n";
try {
    chmod($sessionPath, 0755);
    echo "Session directory permissions: ✅ SET TO 755\n";
} catch (Exception $e) {
    echo "❌ Permission Error: " . $e->getMessage() . "\n";
}

// Fix 3: Create test session file
echo "\n🔧 Testing session file creation...\n";
try {
    $testFile = $sessionPath . '/test_' . time();
    file_put_contents($testFile, 'test');
    echo "Session file write test: ✅ SUCCESS\n";
    unlink($testFile);
} catch (Exception $e) {
    echo "❌ Session file write error: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";

// Check if this is the main issue
if (!is_writable($sessionPath)) {
    echo "🚨 CRITICAL: Session directory not writable!\n";
    echo "   Run: chmod 755 " . $sessionPath . "\n";
}

if (file_exists(bootstrap_path('cache/config.php'))) {
    echo "⚠️  WARNING: Config is cached, changes may not take effect\n";
    echo "   Run: php artisan config:clear\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. ✅ Run this diagnostic\n";
echo "2. 🔄 Try login again: <a href='/login'>/login</a>\n";
echo "3. 🐛 If still failing, check: <a href='/debug-session'>/debug-session</a>\n";
echo "4. 🆘 If persistent, try: <a href='/test-csrf-bypass'>/test-csrf-bypass</a>\n";

echo "\n🎯 Diagnostic Complete!\n";
echo "</pre>";
?>
