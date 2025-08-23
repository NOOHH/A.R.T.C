<?php
// Test the remaining admin pages that are not being affected by navbar changes

$remainingPages = [
    'http://127.0.0.1:8000/admin/modules/archived',
    'http://127.0.0.1:8000/admin/quiz-generator', 
    'http://127.0.0.1:8000/admin/modules/course-content-upload'
];

echo "=== TESTING REMAINING ADMIN PAGES NOT AFFECTED BY NAVBAR ===\n\n";

foreach ($remainingPages as $url) {
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
        
        if ($httpCode == 302) {
            // Check where it redirects
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            if (preg_match('/Location: (.+)/i', $headers, $matches)) {
                $redirectUrl = trim($matches[1]);
                echo "Redirects to: $redirectUrl\n";
                
                if (str_contains($redirectUrl, 'login')) {
                    echo "❌ ISSUE: Redirecting to login (needs authentication)\n";
                } else {
                    echo "🔄 Redirects to different admin page\n";
                }
            }
        } elseif ($httpCode == 200) {
            echo "✅ Page loads successfully\n";
            
            // Extract page content to analyze
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            
            // Check if it contains login form
            if (str_contains($body, 'login') && str_contains($body, 'password')) {
                echo "⚠️  Contains login form\n";
            }
            
            // Check page title
            if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
                echo "Page Title: " . trim($matches[1]) . "\n";
            }
            
            // Check if navbar is present
            if (str_contains($body, 'navbar') || str_contains($body, 'nav-item')) {
                echo "✓ Navbar detected in page\n";
            } else {
                echo "❌ NO NAVBAR detected in page\n";
            }
            
            // Check for specific layout patterns
            if (str_contains($body, 'admin-dashboard-layout')) {
                echo "✓ Uses admin-dashboard-layout\n";
            } elseif (str_contains($body, 'layouts.app')) {
                echo "✓ Uses layouts.app\n";
            } else {
                echo "? Unknown layout pattern\n";
            }
        } else {
            echo "❌ Error: HTTP $httpCode\n";
        }
    }
    
    curl_close($curl);
    echo "---\n\n";
}

echo "Next step: Check if these routes exist and what controllers/views they use\n";
?>
