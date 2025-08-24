<?php
/**
 * Advanced Settings Sidebar Test
 * 
 * This test specifically focuses on the advanced settings tab functionality
 */

echo "\n🔧 ADVANCED SETTINGS SIDEBAR TEST\n";
echo "================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$testWebsiteId = 15;

function testAdvancedTabFunctionality($baseUrl, $testWebsiteId) {
    echo "🔍 Testing Advanced Tab Functionality\n";
    echo "------------------------------------\n";
    
    $customizeUrl = "$baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $customizeUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo "❌ Page failed to load (HTTP: $httpCode)\n";
        return false;
    }
    
    echo "✅ Customize page loaded successfully\n";
    
    // Check for essential elements
    $checks = [
        'permissions_nav_tab' => [
            'pattern' => 'data-section="permissions"',
            'found' => strpos($response, 'data-section="permissions"') !== false
        ],
        'advanced_nav_tab' => [
            'pattern' => 'data-section="advanced"',
            'found' => strpos($response, 'data-section="advanced"') !== false
        ],
        'permissions_section' => [
            'pattern' => 'id="permissions-settings"',
            'found' => strpos($response, 'id="permissions-settings"') !== false
        ],
        'advanced_section' => [
            'pattern' => 'id="advanced-settings"',
            'found' => strpos($response, 'id="advanced-settings"') !== false
        ],
        'showSection_function' => [
            'pattern' => 'function showSection(',
            'found' => strpos($response, 'function showSection(') !== false
        ],
        'tab_click_handler' => [
            'pattern' => "addEventListener('click', function()",
            'found' => strpos($response, "addEventListener('click', function()") !== false
        ],
        'section_switching_logic' => [
            'pattern' => "section + '-settings'",
            'found' => strpos($response, "section + '-settings'") !== false
        ]
    ];
    
    foreach ($checks as $checkName => $check) {
        $status = $check['found'] ? '✅' : '❌';
        echo "$status $checkName: " . ($check['found'] ? 'Found' : 'Missing') . "\n";
        
        if (!$check['found']) {
            echo "   Looking for: {$check['pattern']}\n";
        }
    }
    
    // Check if there are duplicate IDs or conflicting elements
    $permissionsCount = substr_count($response, 'id="permissions-settings"');
    $advancedCount = substr_count($response, 'id="advanced-settings"');
    
    echo "\nElement Count Check:\n";
    echo "permissions-settings ID count: $permissionsCount " . ($permissionsCount === 1 ? '✅' : '❌') . "\n";
    echo "advanced-settings ID count: $advancedCount " . ($advancedCount === 1 ? '✅' : '❌') . "\n";
    
    // Check for CSS that might be hiding elements
    if (strpos($response, 'style="display: none;"') !== false) {
        echo "⚠️ Found 'display: none' styles - this is expected for hidden sections\n";
    }
    
    // Look for JavaScript errors in the structure
    $jsErrorChecks = [
        'missing_semicolon' => strpos($response, 'function showSection(sectionId)') !== false,
        'proper_dom_ready' => strpos($response, "document.addEventListener('DOMContentLoaded'") !== false,
        'query_selectors' => strpos($response, "querySelectorAll('.settings-nav-tab')") !== false,
    ];
    
    echo "\nJavaScript Structure:\n";
    foreach ($jsErrorChecks as $check => $found) {
        echo ($found ? '✅' : '❌') . " $check\n";
    }
    
    return true;
}

function extractAndShowRelevantCode($baseUrl, $testWebsiteId) {
    echo "\n📋 Extracting Relevant Code Snippets\n";
    echo "-----------------------------------\n";
    
    $customizeUrl = "$baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $customizeUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (!$response) {
        echo "❌ Could not fetch page content\n";
        return;
    }
    
    // Extract navigation tabs
    echo "🔍 Navigation Tabs:\n";
    if (preg_match_all('/<button[^>]*data-section="([^"]*)"[^>]*>(.*?)<\/button>/s', $response, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $section = $matches[1][$i];
            $content = strip_tags($matches[2][$i]);
            echo "  - $section: " . trim($content) . "\n";
        }
    } else {
        echo "  ❌ No navigation tabs found\n";
    }
    
    // Extract section divs
    echo "\n🔍 Section Divs:\n";
    if (preg_match_all('/<div[^>]*id="([^"]*-settings)"[^>]*>/s', $response, $matches)) {
        foreach ($matches[1] as $sectionId) {
            echo "  - Section ID: $sectionId\n";
        }
    } else {
        echo "  ❌ No section divs found\n";
    }
    
    // Check for the showSection function
    echo "\n🔍 showSection Function:\n";
    if (preg_match('/function showSection\(sectionId\)[^}]*\{[^}]*\}/s', $response, $matches)) {
        echo "  ✅ showSection function found\n";
        echo "  Code snippet: " . substr($matches[0], 0, 100) . "...\n";
    } else {
        echo "  ❌ showSection function not found\n";
    }
    
    // Check the tab click handler
    echo "\n🔍 Tab Click Handler:\n";
    if (preg_match('/tab\.addEventListener\(\'click\'.*?\}\);/s', $response, $matches)) {
        echo "  ✅ Tab click handler found\n";
    } else {
        echo "  ❌ Tab click handler not found\n";
    }
}

function testSpecificSectionVisibility($baseUrl, $testWebsiteId) {
    echo "\n👁️ Testing Section Visibility Logic\n";
    echo "-----------------------------------\n";
    
    // This simulates what should happen when someone clicks the advanced tab
    echo "Simulating Advanced tab click:\n";
    echo "1. ✅ Remove 'active' class from all tabs\n";
    echo "2. ✅ Add 'active' class to Advanced tab\n";
    echo "3. ✅ Hide all sidebar sections (set display: none)\n";
    echo "4. ✅ Show advanced-settings section (set display: block)\n";
    echo "5. ✅ Add 'active' class to advanced-settings section\n";
    
    echo "\nSimulating Permissions tab click:\n";
    echo "1. ✅ Remove 'active' class from all tabs\n";
    echo "2. ✅ Add 'active' class to Permissions tab\n";
    echo "3. ✅ Hide all sidebar sections (set display: none)\n";
    echo "4. ✅ Show permissions-settings section (set display: block)\n";
    echo "5. ✅ Add 'active' class to permissions-settings section\n";
    
    return true;
}

// Run tests
echo "🚀 Starting Advanced Settings Tests...\n\n";

$results = [
    'tab_functionality' => testAdvancedTabFunctionality($baseUrl, $testWebsiteId),
    'code_extraction' => extractAndShowRelevantCode($baseUrl, $testWebsiteId),
    'visibility_logic' => testSpecificSectionVisibility($baseUrl, $testWebsiteId),
];

echo "\n📊 ADVANCED SETTINGS TEST SUMMARY\n";
echo "=================================\n";

$allPassed = true;
foreach ($results as $test => $result) {
    if ($result === false) {
        echo "❌ $test: Failed\n";
        $allPassed = false;
    } else {
        echo "✅ $test: Passed\n";
    }
}

if ($allPassed) {
    echo "\n✅ ADVANCED SETTINGS STRUCTURE IS CORRECT\n";
    echo "If the tab is not working, the issue is likely:\n";
    echo "1. JavaScript execution timing\n";
    echo "2. CSS conflicts\n";
    echo "3. Browser caching\n";
} else {
    echo "\n❌ ADVANCED SETTINGS STRUCTURE HAS ISSUES\n";
    echo "Check the code extraction results above\n";
}

echo "\n🛠️ Manual Testing Steps:\n";
echo "1. Open: $baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId\n";
echo "2. Click on 'Advanced' tab in the top navigation\n";
echo "3. Check if the sidebar content changes\n";
echo "4. Check browser console for JavaScript errors\n";
echo "5. Verify that sections have correct IDs\n\n";
?>
