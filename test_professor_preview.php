<?php

echo "=== PROFESSOR PREVIEW TESTING ===\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenantSlug = 'test1';
$websiteId = 15;

// Test different professor routes
$professorRoutes = [
    'Dashboard (Preview Param)' => "/professor/dashboard?website={$websiteId}&preview=true",
    'Dashboard (Tenant Route)' => "/t/{$tenantSlug}/professor/dashboard?website={$websiteId}&preview=true",
    'Dashboard (Draft Route)' => "/t/draft/{$tenantSlug}/professor/dashboard?website={$websiteId}&preview=true",
];

echo "Testing professor routes...\n\n";

foreach ($professorRoutes as $name => $route) {
    $url = $baseUrl . $route;
    
    echo "🔍 Testing {$name}...\n";
    echo "   URL: {$url}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Professor Preview Test');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentLength = strlen($response);
    
    if (curl_error($ch)) {
        echo "   ❌ cURL Error: " . curl_error($ch) . "\n";
    } else {
        echo "   📊 HTTP Status: {$httpCode}\n";
        echo "   📄 Content Length: {$contentLength} bytes\n";
        
        // Check for database errors
        $databaseErrors = [
            'Table \'smartprep.professors\' doesn\'t exist',
            'SQLSTATE[42S02]',
            'Base table or view not found',
            'professors\' doesn\'t exist'
        ];
        
        foreach ($databaseErrors as $error) {
            if (stripos($response, $error) !== false) {
                echo "   ❌ DATABASE ERROR DETECTED: {$error}\n";
                
                // Extract more context around the error
                $lines = explode("\n", $response);
                foreach ($lines as $line) {
                    if (stripos($line, $error) !== false) {
                        echo "   🔍 Error Context: " . trim(strip_tags($line)) . "\n";
                        break;
                    }
                }
            }
        }
        
        // Check for other errors
        $otherErrors = ['exception', 'fatal', 'undefined', 'error'];
        foreach ($otherErrors as $error) {
            if (stripos($response, $error) !== false) {
                echo "   ⚠️  Possible error: {$error} detected\n";
            }
        }
        
        // Extract title if successful
        if ($httpCode === 200) {
            if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $response, $matches)) {
                $title = html_entity_decode(strip_tags(trim($matches[1])));
                echo "   📝 Title: {$title}\n";
            }
        }
    }
    
    curl_close($ch);
    echo "\n";
}

echo "=== PROFESSOR PREVIEW TEST COMPLETE ===\n";
