<?php
echo "🔧 DEBUGGING ASSIGNMENT SUBMISSIONS ROUTE\n";
echo "==========================================\n\n";

// Test the route directly
$tenant = 'test1';
$url = "http://127.0.0.1:8000/t/draft/$tenant/admin/submissions?website=15&preview=true&t=" . time();

echo "Testing Assignment Submissions URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 30,
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ],
        'ignore_errors' => true
    ]
]);

$content = @file_get_contents($url, false, $context);

if ($content === false) {
    echo "❌ Failed to fetch content\n";
    
    // Get the last HTTP response headers
    if (isset($http_response_header)) {
        echo "\nHTTP Response Headers:\n";
        foreach ($http_response_header as $header) {
            echo "  $header\n";
        }
    }
} else {
    echo "✅ Content fetched successfully\n";
    echo "Content length: " . strlen($content) . " bytes\n\n";
    
    // Show first 500 characters
    echo "First 500 characters of response:\n";
    echo "==================================\n";
    echo htmlspecialchars(substr($content, 0, 500)) . "\n";
    
    // Check for error indicators
    if (strpos($content, 'Exception') !== false || strpos($content, 'Error') !== false) {
        echo "\n⚠️ Error detected in response!\n";
        
        // Try to extract error message
        if (preg_match('/Exception.*?<\/pre>/s', $content, $matches)) {
            echo "Error details:\n" . htmlspecialchars($matches[0]) . "\n";
        }
    }
}

echo "\n\nNow testing the route configuration...\n";
echo "=====================================\n";

// Test if the route exists in Laravel
echo "Checking if route exists in Laravel route list...\n";
$output = shell_exec('cd /xampp/htdocs/A.R.T.C && php artisan route:list | findstr "admin/submissions"');
if ($output) {
    echo "Route found:\n" . $output . "\n";
} else {
    echo "❌ Route not found in Laravel route list\n";
}
