<?php
echo "🔍 DETAILED SMARTPREP CUSTOMIZATION ANALYSIS\n";
echo "============================================\n\n";

// Get the actual HTML content to analyze
$testUrl = 'http://127.0.0.1:8000/smartprep/dashboard/customize-website';
echo "Fetching: $testUrl\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Page loaded successfully\n\n";
    
    // Save the response to analyze
    file_put_contents('smartprep_customize_page.html', $response);
    echo "📄 Page content saved to: smartprep_customize_page.html\n\n";
    
    // Look for navigation tabs
    echo "🔍 NAVIGATION TABS FOUND:\n";
    echo "-------------------------\n";
    if (preg_match_all('/data-section="([^"]+)"/', $response, $matches)) {
        foreach ($matches[1] as $section) {
            echo "✅ Tab: $section\n";
        }
    } else {
        echo "❌ No data-section tabs found\n";
    }
    
    // Check for specific content patterns
    echo "\n🔍 CONTENT ANALYSIS:\n";
    echo "--------------------\n";
    
    $checks = [
        'SmartPrep' => 'SmartPrep branding',
        'settings-nav-tab' => 'Settings navigation structure',
        'auth' => 'Auth section references', 
        'permissions' => 'Permissions section references',
        'login_review_text' => 'Review text field',
        'login_bg_top_color' => 'Background top color',
        'login_bg_bottom_color' => 'Background bottom color',
        'form-control-color' => 'Color picker inputs',
        'Left Panel Customization' => 'Left panel section',
        'Review Smarter' => 'Default review text'
    ];
    
    foreach ($checks as $pattern => $description) {
        $count = substr_count($response, $pattern);
        if ($count > 0) {
            echo "✅ $description: Found $count occurrence(s)\n";
        } else {
            echo "❌ $description: Not found\n";
        }
    }
    
    // Check for possible errors or redirects
    echo "\n🔍 ERROR CHECKING:\n";
    echo "------------------\n";
    
    if (strpos($response, 'error') !== false || strpos($response, 'Error') !== false) {
        echo "⚠️  Error messages found in response\n";
    } else {
        echo "✅ No obvious errors in response\n";
    }
    
    if (strpos($response, 'login') !== false && strpos($response, 'Sign In') !== false) {
        echo "⚠️  May be showing login form instead of customize interface\n";
    } else {
        echo "✅ Not a login redirect\n";
    }
    
    // Look for JavaScript that might hide/show sections
    echo "\n🔍 JAVASCRIPT ANALYSIS:\n";
    echo "-----------------------\n";
    
    if (strpos($response, 'showSection') !== false) {
        echo "✅ Section switching JavaScript found\n";
    } else {
        echo "❌ Section switching JavaScript not found\n";
    }
    
    if (strpos($response, 'settings-nav-tab') !== false) {
        echo "✅ Navigation tab JavaScript classes found\n";
    } else {
        echo "❌ Navigation tab classes not found\n";
    }
    
    echo "\n📄 First 500 characters of response:\n";
    echo "-------------------------------------\n";
    echo substr($response, 0, 500) . "...\n";
    
} else {
    echo "❌ Failed to load page - HTTP Status: $httpCode\n";
}

echo "\n=== Analysis Complete ===\n";
?>
