<?php
// Direct endpoint test for modular enrollment
header('Content-Type: text/plain');

$url = 'http://127.0.0.1:8000/enrollment/modular/submit';

$postData = [
    'enrollment_type' => 'Modular',
    'program_id' => '33',
    'package_id' => '18',
    'learning_mode' => 'synchronous',
    'education_level' => 'Graduate',
    'Start_Date' => '2025-07-20',
    'selected_modules' => '[{"id":46,"name":"Test Module","selected_courses":["11","14"]}]',
    'user_firstname' => 'Debug',
    'user_lastname' => 'Test',
    'email' => 'debug.fix@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'plan_id' => '2'
];

// Get CSRF token first
$csrfUrl = 'http://127.0.0.1:8000/csrf-token';
$csrfResponse = file_get_contents($csrfUrl);
$csrfData = json_decode($csrfResponse, true);

if (isset($csrfData['csrf_token'])) {
    $postData['_token'] = $csrfData['csrf_token'];
    echo "✅ CSRF token obtained: " . substr($csrfData['csrf_token'], 0, 10) . "...\n\n";
} else {
    echo "⚠️ Could not get CSRF token, proceeding without it\n\n";
}

echo "Testing modular enrollment with Graduate level...\n";
echo "POST URL: $url\n";
echo "Data being sent:\n";
print_r($postData);
echo "\n" . str_repeat("-", 50) . "\n\n";

// Use cURL for the POST request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response:\n";
    
    // Try to decode JSON
    $jsonData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "📋 JSON Response:\n";
        print_r($jsonData);
        
        if ($httpCode === 200 || $httpCode === 201) {
            echo "\n✅ SUCCESS! Registration completed without 500 error.\n";
        } elseif ($httpCode === 422) {
            echo "\n⚠️ Validation errors (this is expected for testing):\n";
            if (isset($jsonData['errors'])) {
                print_r($jsonData['errors']);
            }
        } else {
            echo "\n❌ Unexpected response code: $httpCode\n";
        }
    } else {
        echo "📄 Raw Response (not JSON):\n";
        echo $response;
        
        if ($httpCode === 500) {
            echo "\n❌ 500 INTERNAL SERVER ERROR - Bug may not be fully fixed\n";
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed. Check if 500 error is resolved.\n";
?>
