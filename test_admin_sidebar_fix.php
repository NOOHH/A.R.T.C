<?php
// Test admin preview URL parameter preservation after sidebar fix
echo "🔧 ADMIN SIDEBAR URL PARAMETER PRESERVATION TEST\n";
echo "=================================================\n\n";

$testUrl = 'http://127.0.0.1:8000/t/draft/test1/admin-dashboard?website=15&preview=true&t=1755937168774';

echo "Testing URL: $testUrl\n\n";

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

$content = @file_get_contents($testUrl, false, $context);

if ($content) {
    echo "✅ Page loaded successfully (" . strlen($content) . " bytes)\n\n";
    
    // Check for navigation links with parameters
    $pattern = '/href="([^"]*\/t\/draft\/[^"]*)"[^>]*>/i';
    preg_match_all($pattern, $content, $matches);
    
    if (!empty($matches[1])) {
        echo "📍 Tenant-aware navigation links found:\n";
        foreach ($matches[1] as $link) {
            echo "   - $link\n";
            
            // Check if link preserves all parameters
            $hasWebsite = strpos($link, 'website=') !== false;
            $hasPreview = strpos($link, 'preview=') !== false;
            $hasTimestamp = strpos($link, 't=') !== false;
            
            if ($hasWebsite && $hasPreview && $hasTimestamp) {
                echo "     ✅ All parameters preserved\n";
            } else {
                echo "     ⚠️  Missing parameters: ";
                $missing = [];
                if (!$hasWebsite) $missing[] = 'website';
                if (!$hasPreview) $missing[] = 'preview';
                if (!$hasTimestamp) $missing[] = 't';
                echo implode(', ', $missing) . "\n";
            }
        }
    } else {
        echo "❌ No tenant-aware navigation links found\n";
    }
    
    echo "\n📊 Expected vs Actual:\n";
    echo "Expected: /t/draft/test1/admin/announcements?website=15&preview=true&t=1755937168774\n";
    
    // Find announcements link
    $announcementsMatch = [];
    preg_match('/href="([^"]*announcements[^"]*)"/', $content, $announcementsMatch);
    if (!empty($announcementsMatch[1])) {
        echo "Actual:   {$announcementsMatch[1]}\n";
        
        if (strpos($announcementsMatch[1], 'website=15') !== false && 
            strpos($announcementsMatch[1], 'preview=true') !== false && 
            strpos($announcementsMatch[1], 't=') !== false) {
            echo "🎉 SUCCESS: URL parameters properly preserved!\n";
        } else {
            echo "❌ FAILED: URL parameters not preserved\n";
        }
    } else {
        echo "❌ Announcements link not found\n";
    }
    
} else {
    echo "❌ Failed to load page\n";
}

echo "\n🔄 Testing navigation to preserve parameters...\n";
?>
