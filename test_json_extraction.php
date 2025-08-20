<?php

echo "Testing API JSON extraction...\n";

$jsonResponse = file_get_contents('api_response.html');
echo "Raw JSON length: " . strlen($jsonResponse) . "\n";

$data = json_decode($jsonResponse, true);

if ($data === null) {
    echo "JSON decode failed. Error: " . json_last_error_msg() . "\n";
} else {
    echo "JSON decode successful!\n";
    echo "Success field: " . ($data['success'] ? 'true' : 'false') . "\n";
    
    if (isset($data['data']['navbar']['brand_name'])) {
        echo "PERFECT! Brand name from tenant API: '" . $data['data']['navbar']['brand_name'] . "'\n";
    } else {
        echo "Brand name field not found\n";
    }
}

echo "Done.\n";
