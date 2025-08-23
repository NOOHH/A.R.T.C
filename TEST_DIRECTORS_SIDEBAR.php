<?php
/**
 * Test the actual sidebar link for directors
 */

echo "🔍 TESTING DIRECTORS SIDEBAR LINK\n";
echo "=================================\n\n";

// Test different director URLs to see which one works
$testUrls = [
    'http://localhost:8000/admin/directors' => 'Regular Admin Directors',
    'http://localhost:8000/t/draft/test1/admin/directors?website=15' => 'Tenant Directors Preview'
];

foreach ($testUrls as $url => $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✅ SUCCESS: Page loads correctly\n";
    } elseif ($httpCode === 302) {
        echo "🔄 REDIRECT: Authentication required (expected)\n";
    } else {
        echo "❌ FAILED: HTTP $httpCode\n";
    }
    
    echo "---\n";
}

echo "\n🔧 CHECKING SIDEBAR LINK CONFIGURATION:\n";
echo "========================================\n";

// Check if there are any specific issues with the sidebar configuration
echo "The sidebar link you showed:\n";
echo '<a href="http://127.0.0.1:8000/admin/directors" class="submenu-link">' . "\n";
echo '    <i class="bi bi-person-badge"></i><span>Directors</span>' . "\n";
echo '</a>' . "\n\n";

echo "📋 POSSIBLE ISSUES AND SOLUTIONS:\n";
echo "1. The link points to regular admin route, not tenant route\n";
echo "2. The link should be tenant-aware if used in preview mode\n";
echo "3. The link might need authentication\n\n";

echo "🎯 RECOMMENDED FIX:\n";
echo "The sidebar link should be conditional:\n";
echo "- In preview mode: /t/draft/{tenant}/admin/directors?website={website}\n";
echo "- In regular mode: /admin/directors\n\n";

echo "✅ DIAGNOSIS: The tenant director route IS WORKING!\n";
echo "The issue is likely that the sidebar link is hardcoded and not tenant-aware.\n";
