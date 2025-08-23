<?php
// Test the originally failing routes without preview parameter

$originallyFailingUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin/archived',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/certificates',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload'
];

echo "=== TESTING ORIGINALLY FAILING ADMIN ROUTES ===\n\n";

foreach ($originallyFailingUrls as $url) {
    echo "Testing: $url\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_error($curl)) {
        echo "❌ CURL Error: " . curl_error($curl) . "\n";
    } else {
        echo "HTTP Status: $httpCode\n";
        
        if ($httpCode == 200) {
            echo "✅ SUCCESS - Route is now working!\n";
            
            // Extract page title from response
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
                echo "Page Title: " . trim($matches[1]) . "\n";
            }
        } else {
            echo "❌ STILL FAILING\n";
            
            // Check for redirects
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            if (preg_match('/Location: (.+)/i', $headers, $matches)) {
                echo "Redirected to: " . trim($matches[1]) . "\n";
            }
        }
    }
    
    curl_close($curl);
    echo "---\n\n";
}
?>
