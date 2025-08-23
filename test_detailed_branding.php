<?php
echo "🔍 DETAILED BRANDING SEARCH TEST\n";
echo "=================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$params = '?website=15&preview=true&t=' . time();

// Test the dashboard first
$url = $baseUrl . '/t/draft/' . $tenant . '/admin-dashboard' . $params;
echo "🧪 Testing: Admin Dashboard\n";
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
        echo "   📄 Content loaded successfully (" . strlen($content) . " bytes)\n";
        
        // Search for various brand name patterns
        $searchTerms = [
            'Test1',
            'test1',
            'TEST1', 
            'Ascendo Review and Training Center',
            'Ascendo Review &amp; Training Center',
            'Ascendo Review & Training Center',
            'ARTC',
            'brand_name',
            'navbar_brand_name'
        ];
        
        echo "\n   🔍 Searching for branding terms:\n";
        foreach ($searchTerms as $term) {
            $count = substr_count(strtolower($content), strtolower($term));
            if ($count > 0) {
                echo "   - '$term': Found $count time(s)\n";
                
                // Find context around the term
                $pos = stripos($content, $term);
                if ($pos !== false) {
                    $start = max(0, $pos - 50);
                    $end = min(strlen($content), $pos + strlen($term) + 50);
                    $context = substr($content, $start, $end - $start);
                    echo "     Context: " . htmlspecialchars(trim($context)) . "\n";
                }
            }
        }
        
        // Check if the admin customization is being loaded
        if (strpos($content, 'loadAdminPreviewCustomization') !== false) {
            echo "\n   ✅ Admin preview customization function detected!\n";
        }
        
        // Check if settings are being shared with views
        if (strpos($content, 'var settings') !== false || strpos($content, '"settings"') !== false) {
            echo "   ✅ Settings variable detected in JavaScript!\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR - " . $e->getMessage() . "\n";
}

echo "\nNow testing new routes...\n\n";

// Test new routes
$newPages = [
    'FAQ Management' => '/t/draft/' . $tenant . '/admin/faq',
    'Assignment Submissions' => '/t/draft/' . $tenant . '/admin/submissions'
];

foreach ($newPages as $pageName => $path) {
    $url = $baseUrl . $path . $params;
    echo "🧪 Testing: $pageName\n";
    echo "   URL: $url\n";
    
    try {
        $newContext = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $content = @file_get_contents($url, false, $newContext);
        
        if ($content === false) {
            echo "   ❌ FAILED - Could not load page\n";
        } else {
            echo "   ✅ Page loaded successfully (" . strlen($content) . " bytes)\n";
            
            // Quick brand check
            if (stripos($content, 'test1') !== false) {
                echo "   ✅ Contains 'Test1' branding\n";
            } else {
                echo "   ⚠️  May not have custom branding\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
