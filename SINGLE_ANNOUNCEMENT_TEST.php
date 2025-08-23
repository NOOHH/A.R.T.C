<?php

// Simple test for the specific announcement page that was showing the route confirmation

$url = 'http://127.0.0.1:8000/t/draft/test1/admin/announcements/1?website=15&preview=true&t=123';

echo "🧪 TESTING SPECIFIC ANNOUNCEMENT SHOW PAGE\n";
echo "==========================================\n\n";
echo "URL: {$url}\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$httpCode}\n";

if ($httpCode === 200 && $response) {
    echo "✅ Page loads successfully!\n\n";
    
    // Check what type of content we're getting
    if (strpos($response, 'Route working correctly') !== false) {
        echo "❌ STILL SHOWING: Route confirmation instead of actual page\n";
        echo "Content snippet: " . substr($response, 0, 200) . "...\n";
    } else if (strpos($response, '<html') !== false) {
        echo "✅ CORRECT: Full HTML page is being rendered\n";
        
        // Extract title if available
        if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $response, $matches)) {
            echo "Page Title: {$matches[1]}\n";
        }
        
        // Check for key elements
        $keyElements = [
            'View Announcement' => strpos($response, 'View Announcement') !== false,
            'Sample Announcement' => strpos($response, 'Sample Announcement') !== false,
            'Edit Button' => strpos($response, 'Edit') !== false && strpos($response, 'btn') !== false,
            'Back Button' => strpos($response, 'Back') !== false || strpos($response, 'arrow-left') !== false,
            'Bootstrap CSS' => strpos($response, 'btn btn-') !== false,
            'Announcement Content' => strpos($response, 'announcement') !== false,
        ];
        
        echo "\nContent Check:\n";
        foreach ($keyElements as $element => $found) {
            echo "  " . ($found ? "✅" : "❌") . " {$element}\n";
        }
        
        // Show a snippet of the content
        echo "\nContent Preview (first 300 chars):\n";
        echo "-----------------------------------\n";
        echo substr(strip_tags($response), 0, 300) . "...\n";
        
    } else {
        echo "⚠️  UNKNOWN: Response format not recognized\n";
        echo "Response length: " . strlen($response) . " characters\n";
        echo "First 200 chars: " . substr($response, 0, 200) . "\n";
    }
} else {
    echo "❌ FAILED: HTTP {$httpCode}\n";
}

echo "\n🎉 TEST COMPLETE!\n";

?>
