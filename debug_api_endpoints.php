<?php
/**
 * Debug API Endpoints - Check what's happening with our new API routes
 */

echo "🔍 DEBUG API ENDPOINTS\n";
echo "======================\n";

$tenant = 'test11';
$base_url = "http://127.0.0.1:8000/t/draft/{$tenant}";

// Test the modules by program API
$api_url = "{$base_url}/admin/modules/by-program?program_id=1";
echo "Testing API URL: {$api_url}\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true,
        'header' => 'Accept: application/json'
    ]
]);

echo "\nSending request...\n";
$response = @file_get_contents($api_url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch response\n";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
} else {
    echo "✅ Got response. Length: " . strlen($response) . " bytes\n";
    echo "Raw response:\n";
    echo "---\n";
    echo $response;
    echo "\n---\n";
    
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON response\n";
        echo "Parsed data: " . print_r($json_data, true) . "\n";
    } else {
        echo "❌ Invalid JSON. Error: " . json_last_error_msg() . "\n";
    }
}

// Also test the course content upload page to see the JavaScript
echo "\n📄 CHECKING COURSE CONTENT UPLOAD PAGE\n";
echo "=======================================\n";

$page_url = "{$base_url}/admin/courses/upload?website=15&preview=true";
echo "Testing page URL: {$page_url}\n";

$response = @file_get_contents($page_url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch page\n";
} else {
    echo "✅ Got page response\n";
    
    // Check for our JavaScript functions
    if (strpos($response, 'getTenantFromPath()') !== false) {
        echo "✅ Found getTenantFromPath() function\n";
    } else {
        echo "❌ getTenantFromPath() function not found\n";
    }
    
    if (strpos($response, 'getApiUrl(') !== false) {
        echo "✅ Found getApiUrl() function\n";
    } else {
        echo "❌ getApiUrl() function not found\n";
    }
    
    // Extract a sample of the JavaScript to see what we have
    $start = strpos($response, 'function getTenantFromPath()');
    if ($start !== false) {
        $js_sample = substr($response, $start, 500);
        echo "\nJavaScript sample:\n---\n{$js_sample}...\n---\n";
    }
}

echo "\n🏁 Debug completed!\n";
?>
