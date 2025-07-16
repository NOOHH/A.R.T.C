<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Session Configuration Debug ===\n";
echo "Session driver: " . config('session.driver') . "\n";
echo "Session lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session path: " . config('session.path') . "\n";
echo "Session domain: " . config('session.domain') . "\n";
echo "Session secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "Session same_site: " . config('session.same_site') . "\n";
echo "Session cookie: " . config('session.cookie') . "\n";

echo "\n=== Authentication Check ===\n";
// Check what might be causing the session loss
$sessionPath = storage_path('framework/sessions');
echo "Session storage path: $sessionPath\n";
echo "Session path exists: " . (file_exists($sessionPath) ? 'YES' : 'NO') . "\n";
echo "Session path writable: " . (is_writable($sessionPath) ? 'YES' : 'NO') . "\n";

echo "\n=== Environment Check ===\n";
echo "APP_ENV: " . env('APP_ENV', 'not set') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG', 'not set') . "\n";
echo "SESSION_DRIVER: " . env('SESSION_DRIVER', 'not set') . "\n";
