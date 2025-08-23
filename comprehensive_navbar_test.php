<?php
// Comprehensive test to check navbar dynamic changes

echo "🔍 COMPREHENSIVE NAVBAR DYNAMIC CHANGES TEST\n";
echo "=============================================\n\n";

$testUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin-dashboard',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/students',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/programs',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator',
    'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload'
];

foreach ($testUrls as $url) {
    echo "🔍 Testing URL: $url\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 15
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_error($curl)) {
        echo "❌ CURL Error: " . curl_error($curl) . "\n";
    } else {
        echo "HTTP Status: $httpCode\n";
        
        if ($httpCode == 200) {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            
            // Check for title customization
            if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
                $title = trim($matches[1]);
                echo "Title: $title\n";
                
                if (str_contains($title, 'TEST11') || str_contains($title, 'SmartPrep')) {
                    echo "✅ Title shows tenant branding\n";
                } else {
                    echo "❌ Title does NOT show tenant branding\n";
                }
            }
            
            // Check for navbar brand/logo
            if (preg_match('/<.*class="navbar-brand"[^>]*>([^<]+)</i', $body, $matches)) {
                $navbarBrand = trim($matches[1]);
                echo "Navbar Brand: $navbarBrand\n";
                
                if (str_contains($navbarBrand, 'TEST11') || str_contains($navbarBrand, 'SmartPrep')) {
                    echo "✅ Navbar brand shows tenant customization\n";
                } else {
                    echo "❌ Navbar brand does NOT show tenant customization\n";
                }
            } else {
                echo "❌ Navbar brand not found\n";
            }
            
            // Check for specific tenant variables in JavaScript
            if (str_contains($body, 'window.tenantSettings') || str_contains($body, 'tenantSlug')) {
                echo "✅ Tenant JavaScript variables detected\n";
            } else {
                echo "❌ No tenant JavaScript variables found\n";
            }
            
            // Check for CSS customization
            if (str_contains($body, '--primary-color') || str_contains($body, 'tenant-theme')) {
                echo "✅ CSS customization detected\n";
            } else {
                echo "❌ No CSS customization found\n";
            }
            
            // Look for hardcoded vs dynamic values
            if (str_contains($body, 'A.R.T.C')) {
                echo "⚠️  WARNING: Found hardcoded 'A.R.T.C' text\n";
            }
            
            if (str_contains($body, 'Admin Dashboard') && !str_contains($body, 'TEST11')) {
                echo "⚠️  WARNING: Found generic 'Admin Dashboard' without tenant branding\n";
            }
            
        } else {
            echo "❌ Failed with HTTP $httpCode\n";
        }
    }
    
    curl_close($curl);
    echo "---\n\n";
}

echo "Next: Check database, session data, and view compilation\n";
?>
