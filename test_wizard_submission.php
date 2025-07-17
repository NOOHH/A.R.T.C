<?php

// Test the modular enrollment wizard submission
$data = [
    'package_id' => 12, // From our API test
    'module_ids' => [40, 41], // From our module query
    'learning_mode' => 'Online',
    'account_data' => [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'testuser@example.com',
        'password' => 'password123'
    ],
    'profile_data' => [
        'first_name' => 'Test',
        'last_name' => 'User',
        'test' => 'Test value'
    ]
];

$url = 'http://127.0.0.1:8000/enrollment/modular/store';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
