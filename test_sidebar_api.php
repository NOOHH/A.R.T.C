<?php

require_once 'vendor/autoload.php';

// Test the sidebar API endpoint
$url = 'http://127.0.0.1:8000/smartprep/admin/settings/sidebar';

// Get CSRF token first by loading the settings page
$settingsUrl = 'http://127.0.0.1:8000/smartprep/admin/settings';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $settingsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);

// Extract CSRF token from the response
preg_match('/name="csrf-token" content="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? '';

echo "CSRF Token: " . $csrfToken . "\n";

// Test data
$testData = [
    'role' => 'student',
    'colors' => [
        'primary_color' => '#001122',
        'secondary_color' => '#334455',
        'accent_color' => '#667788',
        'text_color' => '#ffffff',
        'hover_color' => '#445566'
    ]
];

// Make the API request
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: ' . $csrfToken,
    'X-Requested-With: XMLHttpRequest'
]);

$apiResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $apiResponse . "\n";

// Test if colors were saved
echo "\n=== Testing Color Retrieval ===\n";

if ($httpCode === 200) {
    $responseData = json_decode($apiResponse, true);
    if ($responseData && $responseData['success']) {
        echo "✅ Colors saved successfully!\n";
        echo "Role: " . $responseData['role'] . "\n";
        echo "Colors saved: " . json_encode($responseData['colors'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ API returned success=false\n";
    }
} else {
    echo "❌ HTTP Error: " . $httpCode . "\n";
    echo "Response: " . $apiResponse . "\n";
}

?>
