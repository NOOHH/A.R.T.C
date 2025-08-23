<?php
// Test if adding preview parameter fixes the redirect issue

$testUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin/archived',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/archived?preview=true',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/certificates',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/certificates?preview=true'
];

foreach ($testUrls as $url) {
    echo "\n=== Testing URL: $url ===\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_error($curl)) {
        echo "CURL Error: " . curl_error($curl) . "\n";
    } else {
        echo "HTTP Status: $httpCode\n";
        
        // Extract headers
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        // Check for location header
        if (preg_match('/Location: (.+)/i', $headers, $matches)) {
            echo "Redirect to: " . trim($matches[1]) . "\n";
        }
        
        // Show first 200 characters of body
        echo "Body preview: " . substr(strip_tags($body), 0, 200) . "\n";
    }
    
    curl_close($curl);
    echo "\n";
}
?>
