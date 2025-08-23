<?php

echo "=== FINAL BRAND VERIFICATION WITH REGEX ===\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'header' => 'User-Agent: Final Brand Test'
    ]
]);

$response = @file_get_contents('http://127.0.0.1:8000/t/draft/artc/admin-dashboard?website=1', false, $context);

if ($response) {
    echo "✅ Admin dashboard response received\n\n";
    
    // Look for brand-text span
    if (preg_match('/<span[^>]*class="[^"]*brand-text[^"]*"[^>]*>(.*?)<\/span>/s', $response, $brandMatches)) {
        $brandText = trim(strip_tags($brandMatches[1]));
        echo "📝 Brand text found: '$brandText'\n";
        
        if ($brandText === 'SmartPrep Learning Center') {
            echo "🎉 SUCCESS! Brand name is customized\n";
        } else {
            echo "❌ Brand name not customized properly\n";
        }
    } else {
        echo "❌ Brand text span not found\n";
    }
    
    // Look for brand-subtext span  
    if (preg_match('/<span[^>]*class="[^"]*brand-subtext[^"]*"[^>]*>(.*?)<\/span>/s', $response, $subtextMatches)) {
        $subtextText = trim(strip_tags($subtextMatches[1]));
        echo "📝 Subtext found: '$subtextText'\n";
        
        if ($subtextText === 'Learning Portal') {
            echo "🎉 SUCCESS! Subtext is customized\n";
        } else {
            echo "❌ Subtext not customized (still shows: '$subtextText')\n";
        }
    } else {
        echo "❌ Brand subtext span not found\n";
    }
    
    // Count legacy references
    $legacyChecks = [
        'ARTC' => substr_count($response, 'ARTC'),
        'Admin Portal' => substr_count($response, 'Admin Portal'),
        'SmartPrep Learning Center' => substr_count($response, 'SmartPrep Learning Center'),
        'Learning Portal' => substr_count($response, 'Learning Portal')
    ];
    
    echo "\n📊 Text occurrence count:\n";
    foreach ($legacyChecks as $text => $count) {
        echo "   - '$text': $count occurrences\n";
    }
    
} else {
    echo "❌ Could not fetch admin dashboard\n";
}

echo "\n=== Complete ===\n";
