<?php

echo "Testing API with simple curl request...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/api/ui-settings?website=10');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($response) {
    echo "Raw response length: " . strlen($response) . "\n";
    $data = json_decode($response, true);
    echo "JSON decode success: " . ($data !== null ? 'true' : 'false') . "\n";
    
    if ($data) {
        echo "Response success field: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "Has data field: " . (isset($data['data']) ? 'true' : 'false') . "\n";
        echo "Has navbar field: " . (isset($data['data']['navbar']) ? 'true' : 'false') . "\n";
        echo "Has brand_name field: " . (isset($data['data']['navbar']['brand_name']) ? 'true' : 'false') . "\n";
        
        if (isset($data['data']['navbar']['brand_name'])) {
            echo "SUCCESS! Brand name from API: '" . $data['data']['navbar']['brand_name'] . "'\n";
        } else {
            echo "Brand name not found at expected path\n";
        }
    } else {
        echo "JSON decode failed\n";
    }
} else {
    echo "No response\n";
}

echo "Done.\n";
