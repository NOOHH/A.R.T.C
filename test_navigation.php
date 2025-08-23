<?php

// Test internal navigation within preview mode
echo "=== TENANT PREVIEW NAVIGATION TEST ===\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenantSlug = 'test1';
$websiteId = 15;

// Test routes that might be linked from within the preview pages
$testRoutes = [
    'Dashboard' => "/t/draft/{$tenantSlug}/student/dashboard?website={$websiteId}&preview=true",
    'Calendar' => "/t/draft/{$tenantSlug}/student/calendar?website={$websiteId}&preview=true",
    'Courses' => "/t/draft/{$tenantSlug}/student/enrolled-courses?website={$websiteId}&preview=true",
    'Meetings' => "/t/draft/{$tenantSlug}/student/meetings?website={$websiteId}&preview=true",
    'Settings' => "/t/draft/{$tenantSlug}/student/settings?website={$websiteId}&preview=true",
    'Profile' => "/t/draft/{$tenantSlug}/student/profile?website={$websiteId}&preview=true",
    'Announcements' => "/t/draft/{$tenantSlug}/student/announcements?website={$websiteId}&preview=true",
    'Support' => "/t/draft/{$tenantSlug}/student/support?website={$websiteId}&preview=true",
];

foreach ($testRoutes as $name => $route) {
    $url = $baseUrl . $route;
    
    echo "Testing {$name}: {$url}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Preview Test Bot');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentLength = strlen($response);
    
    if (curl_error($ch)) {
        echo "  ❌ cURL Error: " . curl_error($ch) . "\n";
    } else {
        echo "  📊 HTTP Status: {$httpCode}\n";
        echo "  📄 Content Length: {$contentLength} bytes\n";
        
        // Check for errors in content
        $hasError = stripos($response, 'error') !== false || 
                   stripos($response, 'exception') !== false || 
                   stripos($response, 'undefined') !== false ||
                   stripos($response, 'fatal') !== false;
        
        if ($hasError) {
            echo "  ⚠️ Possible error detected in content\n";
        } else {
            echo "  ✅ No obvious errors detected\n";
        }
        
        // Extract title
        if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $response, $matches)) {
            $title = html_entity_decode(strip_tags(trim($matches[1])));
            echo "  Title: {$title}\n";
        }
        
        // Check if it looks like a student page
        if (stripos($response, 'student') !== false) {
            echo "  📱 Appears to be a student page\n";
        }
    }
    
    curl_close($ch);
    echo "\n";
}

echo "=== NAVIGATION TEST COMPLETE ===\n";
