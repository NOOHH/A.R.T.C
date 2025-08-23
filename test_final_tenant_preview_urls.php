<?php
// Test the CORRECT tenant preview URLs for the reported issues

$correctTenantUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator', // Should work 
    'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload', // Should work after fix
    'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived' // New route
];

echo "=== TESTING CORRECT TENANT PREVIEW URLS ===\n\n";

$successCount = 0;
$totalCount = count($correctTenantUrls);

foreach ($correctTenantUrls as $url) {
    echo "🔍 Testing: $url\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_error($curl)) {
        echo "❌ CURL Error: " . curl_error($curl) . "\n";
    } else {
        echo "HTTP Status: $httpCode\n";
        
        if ($httpCode == 200) {
            echo "✅ SUCCESS - Page loads correctly\n";
            $successCount++;
            
            // Extract page content to analyze tenant branding
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            
            // Check page title
            if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
                echo "Page Title: " . trim($matches[1]) . "\n";
            }
            
            // Check for tenant branding
            if (str_contains($body, 'TEST11') || str_contains($body, 'smartprep')) {
                echo "✅ Tenant branding detected\n";
            } else {
                echo "⚠️  No tenant branding detected\n";
            }
            
            // Check for navbar
            if (str_contains($body, 'navbar') || str_contains($body, 'nav-item')) {
                echo "✅ Navbar detected\n";
            } else {
                echo "❌ No navbar detected\n";
            }
            
        } elseif ($httpCode == 302) {
            echo "❌ REDIRECT detected\n";
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            if (preg_match('/Location: (.+)/i', $headers, $matches)) {
                echo "Redirects to: " . trim($matches[1]) . "\n";
            }
        } else {
            echo "❌ Error: HTTP $httpCode\n";
        }
    }
    
    curl_close($curl);
    echo "---\n\n";
}

echo "=== SUMMARY ===\n";
echo "✅ Working: $successCount/$totalCount\n";
echo "❌ Failed: " . ($totalCount - $successCount) . "/$totalCount\n\n";

if ($successCount == $totalCount) {
    echo "🎉 ALL TENANT PREVIEW ROUTES ARE WORKING!\n";
    echo "User should use these URLs for testing tenant customization.\n";
} else {
    echo "⚠️  Some tenant preview routes need fixing.\n";
}

echo "\n📝 IMPORTANT NOTES FOR USER:\n";
echo "• Use /t/draft/{tenant}/admin/* URLs for tenant preview testing\n";
echo "• Regular /admin/* URLs are NOT tenant-aware and won't show customization\n";
echo "• All tenant preview routes should show navbar with tenant branding\n";
?>
