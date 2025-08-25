<?php
echo "🔍 TESTING SPECIFIC BUTTON REDIRECTIONS\n";
echo "======================================\n\n";

// Test the tenant enrollment page and check for account selection buttons
$tenantUrl = 'http://127.0.0.1:8000/t/draft/test/enrollment/modular';
echo "📋 Testing: $tenantUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenantUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n\n";

if ($httpCode == 200 && $response) {
    echo "🔍 Checking for account selection buttons and their JavaScript:\n";
    echo "==============================================================\n\n";
    
    // Look for the selectAccountOption function
    if (preg_match('/function selectAccountOption\(hasAccount\)[^}]+window\.location\.href = "([^"]+)";/', $response, $matches)) {
        $loginUrl = $matches[1];
        echo "✅ Found selectAccountOption function\n";
        echo "📌 Login URL it redirects to: $loginUrl\n";
        
        if (strpos($loginUrl, '/t/draft/test/login') !== false) {
            echo "🎉 SUCCESS! Account button redirects to tenant-aware login URL\n";
        } else {
            echo "❌ ISSUE: Account button does not redirect to tenant-aware URL\n";
        }
    } else {
        echo "⚠️  Could not find selectAccountOption function\n";
    }
    
    // Also check for any hardcoded login links
    echo "\n🔍 Checking for login links in the page:\n";
    echo "========================================\n";
    
    preg_match_all('/href="([^"]*login[^"]*)"/', $response, $loginMatches);
    
    if (!empty($loginMatches[1])) {
        foreach ($loginMatches[1] as $loginLink) {
            echo "📌 Found login link: $loginLink\n";
            
            if (strpos($loginLink, '/t/draft/test/login') !== false) {
                echo "   ✅ Tenant-aware login URL\n";
            } else {
                echo "   ❌ Not tenant-aware\n";
            }
        }
    } else {
        echo "ℹ️  No login links found in page\n";
    }
    
    // Check form actions
    echo "\n🔍 Checking form actions:\n";
    echo "=========================\n";
    
    preg_match_all('/action="([^"]*)"/', $response, $formMatches);
    
    if (!empty($formMatches[1])) {
        foreach ($formMatches[1] as $formAction) {
            echo "📌 Found form action: $formAction\n";
            
            if (strpos($formAction, '/t/draft/test/') !== false) {
                echo "   ✅ Tenant-aware form action\n";
            } else {
                echo "   ⚠️  Non-tenant form action (might be normal)\n";
            }
        }
    }
    
} else {
    echo "❌ Failed to fetch tenant modular page: HTTP $httpCode\n";
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "If you see 'SUCCESS! Account button redirects to tenant-aware login URL' above,\n";
echo "then the 'Yes, I have an account' button issue has been fixed!\n";

echo "\n=== SPECIFIC BUTTON TEST COMPLETE ===\n";
?>
