<?php
/**
 * Simulate Browser API Test - First visit a page to set up session, then call API
 */

echo "🌐 SIMULATE BROWSER API TEST\n";
echo "============================\n";

$tenant = 'test11';
$base_url = "http://127.0.0.1:8000/t/draft/{$tenant}";

// Step 1: Visit the course content upload page to set up session
$page_url = "{$base_url}/admin/courses/upload?website=15&preview=true";
echo "Step 1: Visiting course content upload page to establish session...\n";
echo "URL: {$page_url}\n";

// Use cURL to properly handle cookies/session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $page_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt'); // Use cookies
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$page_response = curl_exec($ch);
$page_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit;
}

echo "Page HTTP Code: {$page_http_code}\n";

if ($page_http_code === 200 && $page_response && strpos($page_response, 'TEST11') !== false) {
    echo "✅ Page loaded successfully with TEST11 branding\n";
} else {
    echo "❌ Page failed to load properly\n";
    echo "Response length: " . strlen($page_response) . " bytes\n";
}

// Step 2: Now call the API using the same session/cookies
echo "\nStep 2: Calling API with established session...\n";
$api_url = "{$base_url}/admin/modules/by-program?program_id=1";
echo "API URL: {$api_url}\n";

curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$api_response = curl_exec($ch);
$api_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "API HTTP Code: {$api_http_code}\n";

if (curl_error($ch)) {
    echo "❌ API cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "✅ Got API response. Length: " . strlen($api_response) . " bytes\n";
    
    if ($api_http_code === 200) {
        echo "Raw API response:\n---\n";
        echo substr($api_response, 0, 500) . (strlen($api_response) > 500 ? '...' : '') . "\n---\n";
        
        $json_data = json_decode($api_response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ Valid JSON response!\n";
            echo "Success: " . ($json_data['success'] ? 'true' : 'false') . "\n";
            echo "Message: " . ($json_data['message'] ?? 'N/A') . "\n";
            echo "Modules count: " . (isset($json_data['modules']) ? count($json_data['modules']) : 'N/A') . "\n";
        } else {
            echo "❌ Invalid JSON: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "❌ API returned HTTP {$api_http_code}\n";
    }
}

curl_close($ch);

// Clean up
if (file_exists('/tmp/cookies.txt')) {
    unlink('/tmp/cookies.txt');
}

echo "\n🏁 Browser simulation completed!\n";
?>
