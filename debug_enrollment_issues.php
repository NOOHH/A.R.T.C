<?php
echo "🔧 FIXING IDENTIFIED ISSUES FROM TESTING\n";
echo "========================================\n\n";

// Issue 1: Helper functions not loading
echo "1️⃣ FIXING HELPER FUNCTION LOADING:\n";
echo "----------------------------------\n";

// Check if the helper is properly autoloaded
$composerFile = 'composer.json';
if (file_exists($composerFile)) {
    $composer = json_decode(file_get_contents($composerFile), true);
    echo "✅ Composer.json exists\n";
    
    if (isset($composer['autoload']['files'])) {
        echo "✅ Autoload files section exists\n";
        foreach ($composer['autoload']['files'] as $file) {
            echo "   - $file\n";
            if (strpos($file, 'TenantEnrollmentHelper') !== false) {
                echo "     ✅ Helper file found in autoload\n";
            }
        }
    } else {
        echo "❌ No autoload files section\n";
    }
}

// Try to manually require the helper for testing
$helperFile = 'app/Helpers/TenantEnrollmentHelper.php';
if (file_exists($helperFile)) {
    require_once $helperFile;
    echo "✅ Helper file manually loaded\n";
    
    // Test functions
    if (function_exists('current_tenant_slug')) {
        echo "✅ current_tenant_slug() now available\n";
    }
    if (function_exists('tenant_enrollment_url')) {
        echo "✅ tenant_enrollment_url() now available\n";
    }
} else {
    echo "❌ Helper file not found\n";
}

// Issue 2: Debug the HTTP 500 error on tenant modular route
echo "\n2️⃣ DEBUGGING TENANT MODULAR ROUTE ERROR:\n";
echo "----------------------------------------\n";

$problemUrl = 'http://127.0.0.1:8000/t/draft/artc/enrollment/modular';
echo "🔍 Investigating: $problemUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $problemUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 500) {
    echo "❌ Server error detected\n";
    
    // Look for error information in response
    if (strpos($body, 'error') !== false || strpos($body, 'Exception') !== false) {
        echo "📄 Error details found in response:\n";
        // Extract first 500 chars to avoid too much output
        $errorSnippet = substr(strip_tags($body), 0, 500);
        echo "   " . trim($errorSnippet) . "...\n";
    }
    
    // Check Laravel logs
    $logFile = 'storage/logs/laravel.log';
    if (file_exists($logFile)) {
        echo "\n📋 Checking recent Laravel logs:\n";
        $logs = file_get_contents($logFile);
        
        // Get last few lines
        $logLines = explode("\n", $logs);
        $recentLogs = array_slice($logLines, -10);
        
        foreach ($recentLogs as $line) {
            if (!empty(trim($line)) && (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false)) {
                echo "   🔍 " . trim($line) . "\n";
            }
        }
    } else {
        echo "❌ Laravel log file not found\n";
    }
}

// Issue 3: Verify route registration
echo "\n3️⃣ CHECKING ROUTE REGISTRATION:\n";
echo "-------------------------------\n";

// Test if routes are properly registered by checking route list
echo "🔍 Checking Laravel routes...\n";
$routeOutput = shell_exec('php artisan route:list --name=enrollment 2>&1');

if ($routeOutput) {
    echo "✅ Route list output:\n";
    $lines = explode("\n", $routeOutput);
    foreach ($lines as $line) {
        if (strpos($line, 'enrollment') !== false) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "❌ Could not get route list\n";
}

// Issue 4: Test tenant middleware
echo "\n4️⃣ TESTING TENANT MIDDLEWARE:\n";
echo "-----------------------------\n";

$middlewareFile = 'app/Http/Middleware/TenantMiddleware.php';
if (file_exists($middlewareFile)) {
    echo "✅ TenantMiddleware exists\n";
    
    $content = file_get_contents($middlewareFile);
    
    // Check for key methods
    if (strpos($content, 'handle') !== false) {
        echo "✅ Has handle method\n";
    }
    
    if (strpos($content, 'tenant') !== false) {
        echo "✅ Contains tenant logic\n";
    }
    
} else {
    echo "❌ TenantMiddleware not found\n";
}

// Issue 5: Check tenant service
echo "\n5️⃣ CHECKING TENANT SERVICE:\n";
echo "---------------------------\n";

$tenantService = 'app/Services/TenantService.php';
if (file_exists($tenantService)) {
    echo "✅ TenantService exists\n";
    
    $content = file_get_contents($tenantService);
    
    // Check for key methods
    $methods = ['switchToTenant', 'getCurrentTenant', 'switchToMain'];
    foreach ($methods as $method) {
        if (strpos($content, $method) !== false) {
            echo "✅ Has $method method\n";
        } else {
            echo "❌ Missing $method method\n";
        }
    }
    
} else {
    echo "❌ TenantService not found\n";
}

echo "\n=== ISSUE INVESTIGATION COMPLETE ===\n";
echo "Next: Implementing specific fixes...\n";
?>
