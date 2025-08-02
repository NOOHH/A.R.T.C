<?php
// Direct API test to see what's being returned
echo "🔍 DIRECT API INVESTIGATION\n";
echo "===========================\n\n";

$baseUrl = 'http://127.0.0.1:8000';

function makeSimpleRequest($url) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['response' => $response, 'code' => $httpCode];
}

// Test the course API directly
echo "1. TESTING /admin/courses ENDPOINT:\n";
$result = makeSimpleRequest($baseUrl . '/admin/courses');
echo "HTTP Code: " . $result['code'] . "\n";
echo "Response Length: " . strlen($result['response']) . " characters\n";
echo "Response Content:\n";
echo "---\n";
echo $result['response'];
echo "\n---\n\n";

// Test a specific course ID
echo "2. TESTING /admin/courses/1 ENDPOINT:\n";
$result = makeSimpleRequest($baseUrl . '/admin/courses/1');
echo "HTTP Code: " . $result['code'] . "\n";
echo "Response Length: " . strlen($result['response']) . " characters\n";
echo "Response Content:\n";
echo "---\n";
echo $result['response'];
echo "\n---\n\n";

// Test another course ID
echo "3. TESTING /admin/courses/2 ENDPOINT:\n";
$result = makeSimpleRequest($baseUrl . '/admin/courses/2');
echo "HTTP Code: " . $result['code'] . "\n";
echo "Response Content:\n";
echo "---\n";
echo $result['response'];
echo "\n---\n\n";

echo "🏁 INVESTIGATION COMPLETE\n";
