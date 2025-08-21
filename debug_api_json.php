<?php

echo "Testing API with full debug output...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/api/ui-settings?website=10');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response length: " . strlen($response) . "\n";
echo "First 200 chars: " . substr($response, 0, 200) . "\n";

if ($response) {
    // Try to clean the response (remove BOM if present)
    $response = trim($response, "\xEF\xBB\xBF");
    
    $data = json_decode($response, true);
    if ($data) {
        echo "JSON parsed successfully!\n";
        if (isset($data['data']['navbar']['brand_name'])) {
            echo "Brand name from API: " . $data['data']['navbar']['brand_name'] . "\n";
        } else {
            echo "Brand name not found. Available navbar keys:\n";
            if (isset($data['data']['navbar'])) {
                foreach (array_keys($data['data']['navbar']) as $key) {
                    echo "  - $key\n";
                }
            } else {
                echo "No navbar data found\n";
            }
        }
    } else {
        echo "JSON parsing failed. Error: " . json_last_error_msg() . "\n";
        echo "Raw response: " . $response . "\n";
    }
} else {
    echo "No response\n";
}

echo "Done.\n";
