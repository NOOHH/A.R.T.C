<?php
/**
 * Director Accessibility Test
 * Test the directors page to ensure it doesn't redirect to dashboard
 */

echo "🔍 Testing Director Accessibility\n";
echo "================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test11';
$url = "/t/draft/$tenant/admin/directors";

echo "Testing Directors Page\n";
echo "URL: $baseUrl$url\n";

try {
    // Initialize curl with redirect tracking
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'temp_cookies.txt');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Test Client');
    
    // Track redirects
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) {
        if (stripos($header, 'Location:') === 0) {
            echo "🔄 Redirect detected: " . trim($header) . "\n";
        }
        return strlen($header);
    });
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
    curl_close($ch);
    
    echo "Status: HTTP $httpCode\n";
    echo "Redirect count: $redirectCount\n";
    echo "Final URL: $finalUrl\n";
    
    if ($redirectCount > 0) {
        echo "⚠️  WARNING: Page redirected $redirectCount time(s)\n";
        
        // Check if redirected to dashboard
        if (strpos($finalUrl, 'admin-dashboard') !== false) {
            echo "❌ PROBLEM: Redirected to admin dashboard\n";
        } elseif (strpos($finalUrl, 'directors') !== false) {
            echo "✅ OK: Stayed on directors page after redirect\n";
        } else {
            echo "⚠️  Redirected to unknown page: $finalUrl\n";
        }
    } else {
        echo "✅ No redirects - direct page load\n";
    }
    
    if ($httpCode === 200) {
        echo "✅ Page loaded successfully\n";
        
        // Check content
        $hasDirectors = strpos($response, 'director') !== false || strpos($response, 'Director') !== false;
        $hasDashboard = strpos($response, 'dashboard') !== false && strpos($response, 'directors') === false;
        $hasPreview = strpos($response, 'Preview') !== false || strpos($response, 'preview') !== false;
        $hasNavigation = strpos($response, 'navbar') !== false || strpos($response, 'nav-') !== false;
        
        if ($hasDirectors) {
            echo "✅ Contains director-related content\n";
        } else {
            echo "⚠️  No director-related content found\n";
        }
        
        if ($hasDashboard && !$hasDirectors) {
            echo "❌ PROBLEM: Shows dashboard content instead of directors\n";
        }
        
        if ($hasPreview) {
            echo "✅ Preview mode active\n";
        }
        
        if ($hasNavigation) {
            echo "✅ Has navigation elements\n";
        }
        
        // Content length check
        $contentLength = strlen($response);
        echo "Content length: " . number_format($contentLength) . " characters\n";
        
        if ($contentLength > 10000) {
            echo "✅ Rich content (proper page)\n";
        } elseif ($contentLength > 2000) {
            echo "⚠️  Moderate content\n";
        } else {
            echo "⚠️  Minimal content\n";
        }
        
    } else {
        echo "❌ Failed with HTTP $httpCode\n";
        
        if ($response) {
            $errorSnippet = substr(strip_tags($response), 0, 300);
            echo "Error content: $errorSnippet...\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n📋 SUMMARY:\n";
echo "- Directors page should load without redirecting to dashboard\n";
echo "- Should contain director-specific content\n";
echo "- Should have preview mode indicators\n";
echo "- Should maintain tenant context (/t/draft/$tenant/...)\n";

// Clean up
if (file_exists('temp_cookies.txt')) {
    unlink('temp_cookies.txt');
}
?>
