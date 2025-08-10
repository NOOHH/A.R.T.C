<?php

// Load environment variables
$envFile = '.env';
if (!file_exists($envFile)) {
    echo "No .env file found. Please check your environment variables manually.\n";
    echo "CRITICAL: Set SESSION_DOMAIN= (leave blank) in your Sevalla environment.\n";
    exit(1);
}

// Read current .env file
$envContent = file_get_contents($envFile);
$lines = explode("\n", $envContent);

$sessionDomainFound = false;
$sessionDomainFixed = false;

foreach ($lines as $index => $line) {
    if (strpos($line, 'SESSION_DOMAIN=') === 0) {
        $sessionDomainFound = true;
        $currentValue = trim(substr($line, strlen('SESSION_DOMAIN=')));
        
        echo "Current SESSION_DOMAIN value: '$currentValue'\n";
        
        if ($currentValue !== '' && $currentValue !== 'null') {
            echo "❌ PROBLEM: SESSION_DOMAIN is set to '$currentValue'\n";
            echo "✅ SOLUTION: Set SESSION_DOMAIN= (blank) in your Sevalla environment\n";
            
            // Update the line
            $lines[$index] = 'SESSION_DOMAIN=';
            $sessionDomainFixed = true;
        } else {
            echo "✅ SESSION_DOMAIN is correctly set to blank\n";
        }
        break;
    }
}

if (!$sessionDomainFound) {
    echo "SESSION_DOMAIN not found in .env file. Adding it as blank...\n";
    $lines[] = 'SESSION_DOMAIN=';
    $sessionDomainFixed = true;
}

if ($sessionDomainFixed) {
    // Write back to .env file
    file_put_contents($envFile, implode("\n", $lines));
    echo "✅ Updated .env file with SESSION_DOMAIN=\n";
}

echo "\n=== ENVIRONMENT CHECK ===\n";
echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "\n";
echo "SESSION_DRIVER: " . ($_ENV['SESSION_DRIVER'] ?? 'NOT SET') . "\n";
echo "SESSION_LIFETIME: " . ($_ENV['SESSION_LIFETIME'] ?? 'NOT SET') . "\n";
echo "SESSION_SECURE_COOKIE: " . ($_ENV['SESSION_SECURE_COOKIE'] ?? 'NOT SET') . "\n";
echo "SESSION_SAME_SITE: " . ($_ENV['SESSION_SAME_SITE'] ?? 'NOT SET') . "\n";
echo "APP_KEY: " . (isset($_ENV['APP_KEY']) ? 'SET' : 'NOT SET') . "\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. In Sevalla, set SESSION_DOMAIN= (leave blank)\n";
echo "2. Clear caches: php artisan config:clear && php artisan cache:clear\n";
echo "3. Test login again\n";
echo "4. Check DevTools → Application → Cookies for laravel_session\n";
