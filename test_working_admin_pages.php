<?php
echo "🧪 TESTING WORKING ADMIN PAGES FIRST\n";
echo "=====================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$params = '?website=15&preview=true&t=' . time();

// Known working pages to verify tenant system
$workingPages = [
    'Dashboard' => '/t/draft/' . $tenant . '/admin-dashboard',
    'Students' => '/t/draft/' . $tenant . '/admin/students',
    'Announcements' => '/t/draft/' . $tenant . '/admin/announcements'
];

echo "Testing known working pages first...\n\n";

foreach ($workingPages as $pageName => $path) {
    $url = $baseUrl . $path . $params;
    echo "🧪 Testing: $pageName\n";
    echo "   URL: $url\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        
        if ($content === false) {
            echo "   ❌ FAILED - Could not load page\n";
        } else {
            // Check for custom branding "Test1"
            $hasTest1Branding = strpos($content, 'Test1') !== false;
            $hasDefaultBranding = strpos($content, 'Ascendo Review and Training Center') !== false || 
                                 strpos($content, 'Ascendo Review &amp; Training Center') !== false;
            
            if ($hasTest1Branding && !$hasDefaultBranding) {
                echo "   ✅ WORKING - Shows 'Test1' branding\n";
            } elseif ($hasTest1Branding && $hasDefaultBranding) {
                echo "   ⚠️  MIXED - Shows both 'Test1' and default branding\n";
            } elseif ($hasDefaultBranding) {
                echo "   ❌ PROBLEM - Shows default branding instead of 'Test1'\n";
            } else {
                echo "   ❓ UNKNOWN - Cannot detect branding\n";
            }
            
            // Check content length
            echo "   📄 Content length: " . strlen($content) . " bytes\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
