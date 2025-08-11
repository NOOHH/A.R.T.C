<?php
/**
 * Debug session configuration in production
 * This will help identify the exact issue
 */

// Check if we're in CLI mode or web
$isCLI = php_sapi_name() === 'cli';

if (!$isCLI) {
    echo "<h1>Session Configuration Debug</h1><pre>";
}

echo "🔍 Debugging Session Configuration...\n\n";

// Display current environment
echo "Environment: " . (app()->environment() ?? 'unknown') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n\n";

// Session configuration
echo "📄 Session Configuration:\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_LIFETIME: " . config('session.lifetime') . "\n";
echo "SESSION_DOMAIN: '" . config('session.domain') . "'\n";
echo "SESSION_SECURE_COOKIE: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "SESSION_SAME_SITE: " . config('session.same_site') . "\n";
echo "SESSION_COOKIE_NAME: " . config('session.cookie') . "\n\n";

// Current domain info
echo "🌐 Current Request Info:\n";
if (isset($_SERVER)) {
    echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
    echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'not set') . "\n";
    echo "HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'yes' : 'no') . "\n";
    echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n\n";
}

// Check if sessions directory is writable
$sessionPath = config('session.files');
echo "📁 Session Storage:\n";
echo "Path: $sessionPath\n";
echo "Exists: " . (is_dir($sessionPath) ? 'yes' : 'no') . "\n";
echo "Writable: " . (is_writable($sessionPath) ? 'yes' : 'no') . "\n\n";

// Environment variables check
echo "🔧 Environment Variables:\n";
$envVars = ['SESSION_DOMAIN', 'SESSION_SECURE_COOKIE', 'SESSION_SAME_SITE', 'APP_URL'];
foreach ($envVars as $var) {
    $value = env($var);
    echo "$var: '" . ($value ?? 'not set') . "'\n";
}

echo "\n✅ Debug complete!\n";

if (!$isCLI) {
    echo "</pre>";
}
?>
