<?php

// Test the fixed announcement preview methods

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$testParams = '?website=15&preview=true&t=' . time();

echo "🔧 ANNOUNCEMENT PREVIEW METHODS TEST\n";
echo "===================================\n\n";

$tests = [
    'Announcement Show (ID 1)' => "/t/draft/{$tenant}/admin/announcements/1{$testParams}",
    'Announcement Edit (ID 1)' => "/t/draft/{$tenant}/admin/announcements/1/edit{$testParams}",
    'Announcement Create' => "/t/draft/{$tenant}/admin/announcements/create{$testParams}",
    'Announcement Index' => "/t/draft/{$tenant}/admin/announcements{$testParams}",
];

foreach ($tests as $testName => $path) {
    echo "🧪 Testing: {$testName}\n";
    echo "   URL: {$baseUrl}{$path}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Announcement Preview Test Bot');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        // Check if it's a proper HTML page (not just a route confirmation)
        if (strpos($response, '<html') !== false && strpos($response, '<body') !== false) {
            echo "   ✅ SUCCESS: Full HTML page rendered\n";
            
            // Check for specific content
            if (strpos($response, 'Sample Announcement') !== false) {
                echo "   ✅ Contains mock announcement content\n";
            }
            
            if (strpos($response, 'Test1') !== false) {
                echo "   ✅ Has tenant customization (Test1 branding)\n";
            }
            
        } else if (strpos($response, 'Route working correctly') !== false) {
            echo "   ⚠️  WARNING: Still showing route confirmation instead of full page\n";
        } else {
            echo "   ✅ SUCCESS: Page loads (response length: " . strlen($response) . " chars)\n";
        }
    } else {
        echo "   ❌ FAILED: HTTP {$httpCode}\n";
    }
    echo "\n";
}

echo "📊 CONTENT VERIFICATION\n";
echo "======================\n";

// Test specific content on the show page
$showUrl = $baseUrl . "/t/draft/{$tenant}/admin/announcements/1{$testParams}";
echo "🔍 Checking content on show page...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $showUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    // Check for key elements that should be on the page
    $checks = [
        'View Announcement' => strpos($response, 'View Announcement') !== false,
        'Sample Announcement #1' => strpos($response, 'Sample Announcement #1') !== false,
        'Edit button' => strpos($response, 'Edit') !== false,
        'Back to List' => strpos($response, 'Back') !== false,
        'Bootstrap classes' => strpos($response, 'btn btn-') !== false,
    ];
    
    foreach ($checks as $checkName => $passed) {
        echo "   " . ($passed ? "✅" : "❌") . " {$checkName}\n";
    }
} else {
    echo "   ❌ Could not verify content (HTTP {$httpCode})\n";
}

echo "\n🎉 ANNOUNCEMENT PREVIEW METHODS TEST COMPLETE! 🎉\n";

?>
