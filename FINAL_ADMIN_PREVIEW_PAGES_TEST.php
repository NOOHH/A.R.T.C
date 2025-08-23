<?php
/**
 * Final Test: All Admin Preview Pages
 * Tests all the admin preview URLs to ensure they work with proper Blade templates
 */

echo "🧪 Final Admin Preview Pages Test\n";
echo "================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test11';

// All admin preview URLs to test
$testUrls = [
    'Student Registration' => "/t/draft/$tenant/admin-student-registration/pending",
    'Archived Content' => "/t/draft/$tenant/admin/archived", 
    'Quiz Generator' => "/t/draft/$tenant/admin/quiz-generator", 
    'Course Content Upload' => "/t/draft/$tenant/admin/courses/upload",
    'Certificates' => "/t/draft/$tenant/admin/certificates"
];

foreach ($testUrls as $pageName => $url) {
    echo "Testing: $pageName\n";
    echo "URL: $baseUrl$url\n";
    
    try {
        // Initialize curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'temp_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Test Client');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        echo "Status: HTTP $httpCode\n";
        
        if ($httpCode === 200) {
            // Check for signs of proper Blade template rendering
            $hasNavbar = strpos($response, 'navbar') !== false || strpos($response, 'nav-') !== false;
            $hasAdminLayout = strpos($response, 'admin-dashboard') !== false || strpos($response, 'sidebar') !== false;
            $hasBootstrap = strpos($response, 'bootstrap') !== false || strpos($response, 'card') !== false;
            $hasPreviewMode = strpos($response, 'Preview Mode') !== false || strpos($response, 'preview_mode') !== false;
            $hasTenantBranding = strpos($response, 'TEST11') !== false;
            $hasHardcodedHTML = strpos($response, '<html>') !== false && strpos($response, '@extends') === false;
            
            echo "✅ Response received successfully\n";
            
            if ($hasNavbar) echo "✅ Has navigation elements\n";
            if ($hasAdminLayout) echo "✅ Uses admin layout structure\n";
            if ($hasBootstrap) echo "✅ Has proper styling (Bootstrap)\n";
            if ($hasPreviewMode) echo "✅ Preview mode is active\n";
            if ($hasTenantBranding) echo "✅ Shows tenant branding (TEST11)\n";
            
            if ($hasHardcodedHTML) {
                echo "⚠️  WARNING: Still shows hardcoded HTML response\n";
            } else {
                echo "✅ Uses proper Blade template (no hardcoded HTML)\n";
            }
            
            // Brief content check
            $contentLength = strlen($response);
            echo "Content length: " . number_format($contentLength) . " characters\n";
            
            if ($contentLength > 10000) {
                echo "✅ Rich content (likely proper template)\n";
            } elseif ($contentLength > 2000) {
                echo "⚠️  Moderate content\n";
            } else {
                echo "⚠️  Minimal content (possibly fallback HTML)\n";
            }
            
        } else {
            echo "❌ Failed with HTTP $httpCode\n";
            if ($httpCode >= 300 && $httpCode < 400) {
                echo "   Redirected to: $finalUrl\n";
            }
            
            // Show a bit of the error response
            if ($response) {
                $errorSnippet = substr(strip_tags($response), 0, 200);
                echo "   Error: $errorSnippet...\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo str_repeat("-", 50) . "\n\n";
}

echo "🏁 Test Complete!\n";
echo "\nSUMMARY:\n";
echo "- All URLs should return HTTP 200\n";
echo "- All pages should use proper Blade templates (not hardcoded HTML)\n";
echo "- All pages should show tenant branding (TEST11)\n";
echo "- All pages should have preview mode indicators\n";
echo "- All pages should have consistent navbar/admin layout\n";

// Clean up
if (file_exists('temp_cookies.txt')) {
    unlink('temp_cookies.txt');
}
?>
