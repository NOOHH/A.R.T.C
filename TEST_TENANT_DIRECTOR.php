<?php
/**
 * Test the tenant director route specifically
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 TESTING TENANT DIRECTOR ROUTE\n";
echo "=================================\n\n";

// Test the specific route that's failing
$testUrl = "http://localhost:8000/t/draft/test1/admin/directors?website=15";
echo "Testing URL: $testUrl\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/temp_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/temp_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "CURL Error: $error\n";
}

if ($httpCode === 200) {
    echo "✅ SUCCESS: Page loads!\n\n";
    
    // Check for common error patterns
    if (strpos($response, 'Error') !== false) {
        echo "⚠️  Page contains errors:\n";
        
        // Extract error messages
        preg_match_all('/Error.*?<\/.*?>/', $response, $matches);
        foreach ($matches[0] as $error) {
            echo "   - " . strip_tags($error) . "\n";
        }
    }
    
    if (strpos($response, 'Exception') !== false) {
        echo "⚠️  Page contains exceptions:\n";
        
        // Extract exception messages
        preg_match_all('/Exception.*?<\/.*?>/', $response, $matches);
        foreach ($matches[0] as $exception) {
            echo "   - " . strip_tags($exception) . "\n";
        }
    }
    
    if (strpos($response, 'Undefined') !== false) {
        echo "⚠️  Page has undefined variables/properties:\n";
        
        // Extract undefined messages
        preg_match_all('/Undefined.*?<\/.*?>/', $response, $matches);
        foreach ($matches[0] as $undefined) {
            echo "   - " . strip_tags($undefined) . "\n";
        }
    }
    
    // Check for successful content
    if (strpos($response, 'Directors') !== false) {
        echo "✅ Contains 'Directors' content\n";
    }
    
    if (strpos($response, 'Admin Directors Preview') !== false) {
        echo "✅ Shows preview mode\n";
    }
    
    // Show response length
    echo "Response length: " . strlen($response) . " characters\n";
    
    // Show first 500 characters for debugging
    echo "\nFirst 500 characters of response:\n";
    echo "==================================\n";
    echo substr($response, 0, 500) . "...\n";
    
} else {
    echo "❌ FAILED: HTTP $httpCode\n";
    if ($response) {
        echo "Response: " . substr($response, 0, 500) . "\n";
    }
}

echo "\n🔧 ROUTE DEBUGGING INFO:\n";
echo "========================\n";
echo "Expected route: t/draft/{tenant}/admin/directors\n";
echo "Controller: AdminDirectorController@previewIndex\n";
echo "Method: Should return directors preview page\n";
