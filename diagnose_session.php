<?php

// Load Laravel environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SESSION DIAGNOSTIC REPORT ===\n\n";

// A) Show key session/env bits
echo "A) Environment Variables:\n";
$envVars = ['APP_URL', 'APP_KEY', 'SESSION_DRIVER', 'SESSION_SAME_SITE', 'SESSION_SECURE_COOKIE', 'SESSION_DOMAIN'];
foreach ($envVars as $var) {
    $value = env($var);
    if ($var === 'APP_KEY') {
        $value = $value ? 'SET (base64:...)' : 'NOT SET';
    }
    echo "$var = $value\n";
}

echo "\nB) Storage Permissions:\n";
$sessionsPath = storage_path('framework/sessions');
echo "Sessions directory: $sessionsPath\n";
echo "Sessions writable: " . (is_writable($sessionsPath) ? 'YES' : 'NO') . "\n";
echo "Storage writable: " . (is_writable(storage_path()) ? 'YES' : 'NO') . "\n";
echo "Bootstrap/cache writable: " . (is_writable(base_path('bootstrap/cache')) ? 'YES' : 'NO') . "\n";

echo "\nC) Current Session Files:\n";
if (is_dir($sessionsPath)) {
    $files = glob($sessionsPath . '/*');
    echo "Session files count: " . count($files) . "\n";
    if (count($files) > 0) {
        echo "Recent session files:\n";
        $recentFiles = array_slice($files, -5);
        foreach ($recentFiles as $file) {
            echo "  - " . basename($file) . " (" . date('Y-m-d H:i:s', filemtime($file)) . ")\n";
        }
    }
} else {
    echo "Sessions directory does not exist!\n";
}

echo "\nD) Session Configuration:\n";
$config = config('session');
echo "Driver: " . $config['driver'] . "\n";
echo "Lifetime: " . $config['lifetime'] . "\n";
echo "Domain: " . ($config['domain'] ?? 'null') . "\n";
echo "Secure: " . ($config['secure'] ? 'true' : 'false') . "\n";
echo "Same Site: " . $config['same_site'] . "\n";
echo "Cookie Name: " . $config['cookie'] . "\n";

echo "\nE) Web Middleware Check:\n";
$kernel = app('Illuminate\Contracts\Http\Kernel');
$middlewareGroups = $kernel->getMiddlewareGroups();
if (isset($middlewareGroups['web'])) {
    echo "Web middleware group found with " . count($middlewareGroups['web']) . " middleware\n";
    $required = [
        'App\Http\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'Illuminate\Routing\Middleware\SubstituteBindings'
    ];
    
    foreach ($required as $middleware) {
        $found = false;
        foreach ($middlewareGroups['web'] as $webMiddleware) {
            if (strpos($webMiddleware, $middleware) !== false) {
                $found = true;
                break;
            }
        }
        echo "  $middleware: " . ($found ? '✅ FOUND' : '❌ MISSING') . "\n";
    }
} else {
    echo "❌ Web middleware group not found!\n";
}

echo "\n=== END DIAGNOSTIC ===\n";
