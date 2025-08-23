<?php
echo "=== DEBUGGING WEB INTERFACE RESPONSE ===\n\n";

// Test the customize-website endpoint and see what's being returned

foreach ([15 => 'test1', 16 => 'test2'] as $websiteId => $slug) {
    echo "Testing website $websiteId ($slug)...\n";
    
    $url = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=$websiteId";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "URL: $url\n";
    echo "HTTP Code: $httpCode\n";
    
    if ($error) {
        echo "cURL Error: $error\n";
        continue;
    }
    
    if ($httpCode !== 200) {
        echo "Non-200 response. First 500 chars:\n";
        echo substr($response, 0, 500) . "\n";
        echo "...\n\n";
        continue;
    }
    
    // Look for brand_name input field in various ways
    echo "Looking for brand_name field...\n";
    
    if (preg_match('/name=["\']brand_name["\'][^>]*value=["\']([^"\']*)["\']/', $response, $matches)) {
        echo "Found brand_name: '" . $matches[1] . "'\n";
    } elseif (preg_match('/id=["\']brand_name["\'][^>]*value=["\']([^"\']*)["\']/', $response, $matches)) {
        echo "Found brand_name by id: '" . $matches[1] . "'\n";
    } else {
        echo "No brand_name field found. Searching for any input with 'brand'...\n";
        
        if (preg_match_all('/<input[^>]*brand[^>]*>/i', $response, $matches)) {
            foreach ($matches[0] as $match) {
                echo "Found: $match\n";
            }
        }
        
        echo "\nSearching for any 'brand' text in response...\n";
        if (preg_match_all('/brand[^<>]*["\'][^"\']*["\']/i', $response, $matches)) {
            foreach ($matches[0] as $match) {
                echo "Found brand text: $match\n";
            }
        }
        
        echo "\nFirst 1000 characters of response:\n";
        echo substr($response, 0, 1000) . "\n";
        echo "...\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "=== DEBUGGING COMPLETE ===\n";
?>
