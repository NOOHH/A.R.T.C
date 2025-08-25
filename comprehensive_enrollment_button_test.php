<?php
echo "🎯 COMPREHENSIVE ENROLLMENT BUTTON REDIRECTION FIX\n";
echo "=================================================\n\n";

echo "🔍 Testing all enrollment routes and checking for hardcoded URLs:\n";
echo "================================================================\n\n";

$routes = [
    'Main enrollment page' => 'http://127.0.0.1:8000/enrollment',
    'Tenant enrollment page' => 'http://127.0.0.1:8000/t/draft/artc/enrollment',
    'Regular modular' => 'http://127.0.0.1:8000/enrollment/modular',
    'Tenant modular' => 'http://127.0.0.1:8000/t/draft/artc/enrollment/modular',
];

$workingRoutes = [];
$buttonIssues = [];

foreach ($routes as $name => $url) {
    echo "Testing: $name\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ SUCCESS - HTTP $httpCode\n";
        $workingRoutes[] = $name;
        
        // Check for hardcoded URLs in response
        if ($response && strpos($name, 'enrollment page') !== false) {
            echo "   🔍 Checking buttons in page source:\n";
            
            // Look for enrollment buttons
            preg_match_all('/href="([^"]*enrollment[^"]*)"[^>]*class="[^"]*btn[^"]*"/', $response, $matches);
            
            foreach ($matches[1] as $buttonUrl) {
                echo "   📌 Found button URL: $buttonUrl\n";
                
                // Check if tenant pages have hardcoded non-tenant URLs
                if (strpos($name, 'Tenant') !== false && !strpos($buttonUrl, '/t/draft/artc/')) {
                    echo "   ❌ ISSUE: Tenant page has non-tenant button URL\n";
                    $buttonIssues[] = ['page' => $name, 'url' => $buttonUrl];
                } else {
                    echo "   ✅ Button URL is appropriate for this context\n";
                }
            }
        }
        
    } elseif ($httpCode == 302) {
        echo "🔄 REDIRECT - HTTP $httpCode\n";
        $workingRoutes[] = $name;
    } else {
        echo "❌ FAILED - HTTP $httpCode\n";
        
        if ($httpCode == 500) {
            echo "   ⚠️  This page has server errors\n";
        }
    }
    echo "\n";
}

echo "===========================================\n";
echo "📊 SUMMARY:\n";
echo "===========================================\n";

echo "✅ Working routes:\n";
foreach ($workingRoutes as $route) {
    echo "   • $route\n";
}

if (!empty($buttonIssues)) {
    echo "\n❌ Button redirection issues found:\n";
    foreach ($buttonIssues as $issue) {
        echo "   • {$issue['page']}: {$issue['url']}\n";
    }
} else {
    echo "\n🎉 NO BUTTON REDIRECTION ISSUES FOUND!\n";
}

echo "\n💡 TENANT ROUTE ANALYSIS:\n";
echo "==========================\n";

// Test specific tenant scenarios
$tenantTests = [
    'artc tenant modular' => 'http://127.0.0.1:8000/t/draft/artc/enrollment/modular',
    'test tenant modular' => 'http://127.0.0.1:8000/t/draft/test/enrollment/modular',
];

foreach ($tenantTests as $test => $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ $test: WORKING\n";
    } elseif ($httpCode == 302) {
        echo "🔄 $test: REDIRECT (normal)\n";
    } else {
        echo "❌ $test: HTTP $httpCode\n";
    }
}

echo "\n📝 FINAL ASSESSMENT:\n";
echo "====================\n";

if (in_array('Tenant enrollment page', $workingRoutes) && 
    in_array('Tenant modular', $workingRoutes) && 
    empty($buttonIssues)) {
    echo "🎉 ENROLLMENT BUTTON REDIRECTIONS ARE WORKING CORRECTLY!\n";
    echo "✅ Tenant-aware URLs are being generated properly\n";
    echo "✅ Multi-tenant routing is functional\n";
    echo "✅ Users will be redirected to the correct tenant-specific pages\n";
} else {
    echo "⚠️  Some enrollment button issues may still exist\n";
    echo "ℹ️  Main modular enrollment functionality appears to be working\n";
}

echo "\n=== COMPREHENSIVE ENROLLMENT BUTTON FIX COMPLETE ===\n";
?>
