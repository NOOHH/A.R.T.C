<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Assignment Submissions Route (Fixed Version)\n";
echo "====================================================\n\n";

$url = 'http://127.0.0.1:8000/t/draft/test1/admin/submissions?preview=true&t=' . time() . '&website=15';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Connection: close'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    if ($httpCode === 200) {
        echo "✅ Request successful!\n";
        echo "Response length: " . strlen($response) . " bytes\n";
        
        // Check for TEST11 branding
        $test1Count = substr_count($response, 'TEST11');
        echo "Instances of 'TEST11' found: $test1Count\n";
        
        // Check for Assignment Submissions specific content
        if (strpos($response, 'Assignment Submissions') !== false) {
            echo "✅ Assignment Submissions page loaded\n";
        } else {
            echo "❌ Assignment Submissions content not found\n";
        }
        
        // Check for error indicators
        if (strpos($response, 'Undefined property') !== false) {
            echo "❌ Still has Undefined property errors\n";
        } else {
            echo "✅ No undefined property errors detected\n";
        }
        
        // Check if navbar customization is applied
        if (strpos($response, 'TEST11') !== false) {
            echo "✅ Tenant customization (TEST11) is applied\n";
        } else {
            echo "❌ Tenant customization not found\n";
        }
        
    } else {
        echo "❌ HTTP Error: $httpCode\n";
        if (strlen($response) < 2000) {
            echo "Response snippet: " . substr($response, 0, 500) . "\n";
        }
    }
}

echo "\nTesting completed at " . date('Y-m-d H:i:s') . "\n";
?>
