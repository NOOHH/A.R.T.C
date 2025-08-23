<?php
// Test the correct tenant preview URLs for the problematic admin pages

$tenantPreviewUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload',
    'http://127.0.0.1:8000/admin/modules/archived' // This one should work but has navbar issues
];

echo "=== TESTING CORRECT TENANT PREVIEW URLS ===\n\n";

foreach ($tenantPreviewUrls as $url) {
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
            
            // Extract page content to analyze navbar
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            
            // Check page title
            if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
                echo "Page Title: " . trim($matches[1]) . "\n";
            }
            
            // Check if navbar is present and functional
            if (str_contains($body, 'navbar') || str_contains($body, 'nav-item')) {
                echo "✓ Navbar detected\n";
                
                // Check for specific navbar elements that should change
                if (str_contains($body, 'TEST11') || str_contains($body, 'smartprep')) {
                    echo "✓ Tenant branding detected in navbar\n";
                } else {
                    echo "❌ NO tenant branding in navbar - This is the issue!\n";
                }
            } else {
                echo "❌ NO navbar detected\n";
            }
            
            // Check layout type
            if (str_contains($body, 'admin-dashboard-layout')) {
                echo "✓ Uses admin-dashboard-layout\n";
            } else {
                echo "? Different layout pattern\n";
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

echo "Summary: Need to check why these pages don't reflect navbar/tenant customization changes\n";
?>
