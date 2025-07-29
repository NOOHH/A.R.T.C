<?php

// Test session endpoint using cURL
$url = 'http://127.0.0.1:8000/professor/professor/test-session';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

// Decode JSON response
$data = json_decode($response, true);
if ($data) {
    echo "\nSession Data:\n";
    echo "session_logged_in: " . ($data['session_logged_in'] ? 'true' : 'false') . "\n";
    echo "session_professor_id: " . $data['session_professor_id'] . "\n";
    echo "session_user_role: " . $data['session_user_role'] . "\n";
    echo "session_user_type: " . $data['session_user_type'] . "\n";
    echo "session_user_id: " . $data['session_user_id'] . "\n";
    echo "auth_check: " . ($data['auth_check'] ? 'true' : 'false') . "\n";
    echo "auth_user: " . ($data['auth_user'] ? 'not null' : 'null') . "\n";
} else {
    echo "Failed to decode JSON response\n";
}
?> 