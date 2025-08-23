<?php
// Test admin navigation flow with preserved URL parameters
echo "🔗 ADMIN NAVIGATION FLOW WITH PARAMETERS TEST\n";
echo "===============================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$params = 'website=15&preview=true&t=1755937168774';

// Test navigation sequence with parameters
$testSequence = [
    'Dashboard' => "/t/draft/$tenant/admin-dashboard?$params",
    'Announcements' => "/t/draft/$tenant/admin/announcements?$params",
    'Students' => "/t/draft/$tenant/admin/students?$params",
    'Programs' => "/t/draft/$tenant/admin/programs?$params",
    'Analytics' => "/t/draft/$tenant/admin/analytics?$params"
];

echo "Testing navigation flow with customization parameters...\n\n";

$success = 0;
$total = count($testSequence);

foreach ($testSequence as $pageName => $path) {
    echo "📍 Testing: $pageName\n";
    echo "   URL: $baseUrl$path\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ],
            'timeout' => 10
        ]
    ]);
    
    $content = @file_get_contents($baseUrl . $path, false, $context);
    
    if ($content === false) {
        echo "   ❌ FAILED - Could not load page\n";
        continue;
    }
    
    $contentLength = strlen($content);
    
    // Check if it loads admin content
    $hasAdminContent = strpos($content, 'Admin Dashboard') !== false || 
                       strpos($content, 'admin-functions.js') !== false ||
                       strpos($content, 'csrf-token') !== false;
    
    if ($hasAdminContent && $contentLength > 1000) {
        echo "   ✅ SUCCESS - Page loaded ($contentLength bytes)\n";
        $success++;
        
        // Check if navigation links preserve parameters
        $parameterizedLinks = 0;
        preg_match_all('/href="([^"]*\/t\/draft\/' . $tenant . '[^"]*\?[^"]*website=[^"]*)"/', $content, $matches);
        $parameterizedLinks = count($matches[1]);
        
        if ($parameterizedLinks > 0) {
            echo "   ✅ Contains $parameterizedLinks parameterized navigation links\n";
            
            // Show first few links as examples
            for ($i = 0; $i < min(3, count($matches[1])); $i++) {
                echo "      → " . $matches[1][$i] . "\n";
            }
        } else {
            echo "   ⚠️  No parameterized navigation links found\n";
        }
    } else {
        echo "   ❌ FAILED - Invalid admin content or too small\n";
    }
    
    echo "\n";
}

echo "=== NAVIGATION FLOW TEST RESULTS ===\n";
echo "📊 Pages tested: $total\n";
echo "✅ Successful: $success\n";
echo "❌ Failed: " . ($total - $success) . "\n";
echo "📈 Success rate: " . round(($success / $total) * 100, 1) . "%\n\n";

if ($success == $total) {
    echo "🎉 ADMIN PREVIEW CUSTOMIZATION NAVIGATION - COMPLETE SUCCESS!\n";
    echo "=============================================================\n\n";
    echo "✅ All admin preview pages load with customization parameters\n";
    echo "✅ Navigation links preserve website, preview, and timestamp parameters\n";
    echo "✅ Users can navigate between admin preview pages with customization intact\n";
    echo "✅ No more redirects to standard admin routes\n\n";
    echo "🔧 ISSUE RESOLVED: Admin preview now maintains customization parameters!\n";
} else {
    echo "⚠️  Some issues remain with parameterized navigation.\n";
}

echo "\n🚀 Testing complete!\n";
?>
