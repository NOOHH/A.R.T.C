<?php
/**
 * Simple 419 Fix Tool - Works without Laravel context
 */

echo "<h1>🚨 419 Error Fix Tool</h1>";
echo "<pre>";

echo "🔍 RUNNING 419 DIAGNOSTICS...\n\n";

// Basic environment check
echo "=== BASIC CHECKS ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? '✅ YES' : '❌ NO') . "\n\n";

// Session directory check
$possibleSessionPaths = [
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/../storage/framework/sessions',
    getcwd() . '/storage/framework/sessions'
];

$sessionPath = null;
foreach ($possibleSessionPaths as $path) {
    if (is_dir($path)) {
        $sessionPath = $path;
        break;
    }
}

echo "=== SESSION STORAGE ===\n";
if ($sessionPath) {
    echo "Session Path Found: $sessionPath\n";
    echo "Directory Writable: " . (is_writable($sessionPath) ? '✅ YES' : '❌ NO') . "\n";
    
    if (!is_writable($sessionPath)) {
        echo "🔧 Attempting to fix permissions...\n";
        chmod($sessionPath, 0755);
        echo "Permissions updated: " . (is_writable($sessionPath) ? '✅ SUCCESS' : '❌ STILL FAILED') . "\n";
    }
    
    // Test session file creation
    $testFile = $sessionPath . '/test_' . time() . '.txt';
    if (file_put_contents($testFile, 'test')) {
        echo "File Write Test: ✅ SUCCESS\n";
        unlink($testFile);
    } else {
        echo "File Write Test: ❌ FAILED\n";
    }
} else {
    echo "❌ Session directory not found!\n";
    echo "🔧 Attempting to create session directory...\n";
    
    $newSessionPath = __DIR__ . '/storage/framework/sessions';
    if (!is_dir(dirname($newSessionPath))) {
        mkdir(dirname($newSessionPath), 0755, true);
    }
    mkdir($newSessionPath, 0755, true);
    
    if (is_dir($newSessionPath)) {
        echo "✅ Session directory created: $newSessionPath\n";
        $sessionPath = $newSessionPath;
    }
}
echo "\n";

// Cache directory check
echo "=== CACHE CLEANUP ===\n";
$cacheDirectories = [
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/views'
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        echo "Clearing cache directory: " . basename($dir) . "\n";
        
        // Clear cache files
        $files = glob($dir . '/*');
        $cleared = 0;
        foreach ($files as $file) {
            if (is_file($file) && strpos($file, '.gitignore') === false) {
                unlink($file);
                $cleared++;
            }
        }
        echo "  Cleared $cleared files\n";
    }
}
echo "\n";

// Environment file check
echo "=== ENVIRONMENT CONFIG ===\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ .env file exists\n";
    
    $envContent = file_get_contents($envFile);
    $requiredVars = [
        'SESSION_DOMAIN=' => 'Should be empty for production',
        'SESSION_SECURE_COOKIE=true' => 'Required for HTTPS',
        'SESSION_SAME_SITE=lax' => 'Required setting'
    ];
    
    foreach ($requiredVars as $var => $description) {
        if (strpos($envContent, $var) !== false) {
            echo "✅ $var found - $description\n";
        } else {
            echo "❌ $var missing - $description\n";
        }
    }
} else {
    echo "❌ .env file not found\n";
}
echo "\n";

// Manual Artisan Commands
echo "=== CLEARING LARAVEL CACHES ===\n";
$artisanPath = __DIR__ . '/artisan';
if (file_exists($artisanPath)) {
    $commands = ['config:clear', 'cache:clear', 'view:clear', 'route:clear'];
    
    foreach ($commands as $cmd) {
        echo "Running: php artisan $cmd\n";
        $output = [];
        $return_var = 0;
        exec("php $artisanPath $cmd 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "  ✅ Success\n";
        } else {
            echo "  ❌ Failed: " . implode(' ', $output) . "\n";
        }
    }
} else {
    echo "❌ Artisan not found at $artisanPath\n";
}
echo "\n";

// Quick session test
echo "=== SESSION TEST ===\n";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '✅ ACTIVE' : '❌ INACTIVE') . "\n";
echo "Session ID: " . session_id() . "\n";

// Test CSRF token generation
$_SESSION['_token'] = bin2hex(random_bytes(40));
echo "Test Token Generated: " . substr($_SESSION['_token'], 0, 20) . "...\n";
echo "\n";

echo "=== IMMEDIATE ACTION ITEMS ===\n";
echo "1. 🔄 Try login again immediately\n";
echo "2. 🌐 Visit: <a href='https://laravel-zfurp.sevalla.app/login'>https://laravel-zfurp.sevalla.app/login</a>\n";
echo "3. 📱 Clear browser cache/cookies if still failing\n";
echo "4. 🔍 Check browser developer tools for errors\n";
echo "\n";

echo "🎯 Fix attempts completed! Try logging in now.\n";
echo "</pre>";

// Simple login form for testing
echo "<h2>🧪 Test Login Form</h2>";
echo "<p>If the regular login still fails, try this test form:</p>";
echo "<form method='POST' action='/login' style='border: 1px solid #ccc; padding: 20px; max-width: 400px;'>";
echo "    <div><label>Email: <input type='email' name='email' required style='width: 100%; margin: 5px 0;'></label></div>";
echo "    <div><label>Password: <input type='password' name='password' required style='width: 100%; margin: 5px 0;'></label></div>";
echo "    <div><label><input type='checkbox' name='remember'> Remember Me</label></div>";
echo "    <input type='hidden' name='_token' value='" . ($_SESSION['_token'] ?? '') . "'>";
echo "    <button type='submit' style='background: #007cba; color: white; padding: 10px 20px; border: none; margin-top: 10px;'>Test Login</button>";
echo "</form>";
?>
