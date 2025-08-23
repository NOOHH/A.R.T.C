<?php

// Debug what the admin dashboard route is actually returning
echo "🔍 Debugging Admin Dashboard Response\n";
echo "=====================================\n\n";

$url = 'http://localhost:8000/t/draft/smartprep/admin-dashboard?website=17';
echo "📡 Testing URL: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "📊 HTTP Status: $httpCode\n";
echo "📄 Response Headers:\n";
echo $headers . "\n";

echo "📝 Response Body (first 1000 chars):\n";
echo substr($body, 0, 1000) . "\n\n";

// Check for specific content
echo "🔍 Content Analysis:\n";
echo "- Contains 'SmartPrep': " . (strpos($body, 'SmartPrep') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'dashboard': " . (strpos($body, 'dashboard') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'Dashboard': " . (strpos($body, 'Dashboard') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'total_students': " . (strpos($body, 'total_students') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'analytics': " . (strpos($body, 'analytics') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'Preview': " . (strpos($body, 'Preview') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'Ascendo': " . (strpos($body, 'Ascendo') !== false ? '✅ YES' : '❌ NO') . "\n";

// Look for any errors
if (strpos($body, 'Error') !== false || strpos($body, 'Exception') !== false) {
    echo "\n⚠️  Potential errors found in response:\n";
    preg_match_all('/<.*?>(.*?Error.*?)<\/.*?>/i', $body, $matches);
    foreach ($matches[1] as $error) {
        echo "   - " . trim(strip_tags($error)) . "\n";
    }
}

echo "\n✅ Response analysis complete!\n";
