<?php
// Test current URL parameter handling in admin preview
echo "🔍 ADMIN PREVIEW URL PARAMETERS TEST\n";
echo "=====================================\n\n";

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
    
    // Check for existing navigation links
    $pattern = '/href="([^"]*admin[^"]*)"[^>]*>/i';
    preg_match_all($pattern, $content, $matches);
    
    if (!empty($matches[1])) {
        echo "📍 Current navigation links found:\n";
        foreach ($matches[1] as $link) {
            echo "   - $link\n";
            
            // Check if link preserves parameters
            if (strpos($link, 'website=') !== false && strpos($link, 'preview=') !== false) {
                echo "     ✅ Preserves parameters\n";
            } else {
                echo "     ❌ Missing parameters\n";
            }
        }
    } else {
        echo "❌ No admin navigation links found\n";
    }
    
    echo "\n📋 URL Parameter Analysis:\n";
    echo "Expected parameters to preserve:\n";
    echo "   - website=15\n";
    echo "   - preview=true\n";
    echo "   - t=1755937168774\n\n";
    
} else {
    echo "❌ Failed to load page\n";
}

echo "🎯 Next step: Update admin sidebar to preserve URL parameters\n";
?>
