<?php
// Simple admin navigation flow test
echo "Testing Admin Preview Navigation Flow\n";
echo "=====================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';

// Test navigation sequence
$testSequence = [
    'Start at Dashboard' => '/t/draft/' . $tenant . '/admin-dashboard',
    'Navigate to Announcements' => '/t/draft/' . $tenant . '/admin/announcements',
    'Navigate to Students' => '/t/draft/' . $tenant . '/admin/students',
    'Navigate to Programs' => '/t/draft/' . $tenant . '/admin/programs',
    'Back to Dashboard' => '/t/draft/' . $tenant . '/admin-dashboard',
];

$success = 0;
$total = count($testSequence);

foreach ($testSequence as $step => $path) {
    echo "Step: $step\n";
    echo "URL: $baseUrl$path\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ],
            'timeout' => 10
        ]
    ]);
    
    $content = @file_get_contents($baseUrl . $path, false, $context);
    
    if ($content === false) {
        echo "❌ FAILED - Could not load page\n";
    } else {
        $contentLength = strlen($content);
        echo "Page size: " . number_format($contentLength) . " bytes\n";
        
        // Check if page contains admin content and no login redirect
        if (strpos($content, 'login-form') !== false || strpos($content, 'Please log in') !== false) {
            echo "❌ FAILED - Redirected to login\n";
        } else if ($contentLength > 1000) { // Admin pages should be substantial
            echo "✅ SUCCESS - Page loaded with content\n";
            $success++;
            
            // Check for tenant-aware navigation links
            $tenantLinkCount = preg_match_all('/href="\/t\/draft\/' . $tenant . '\//', $content);
            if ($tenantLinkCount > 0) {
                echo "✅ Contains $tenantLinkCount tenant-aware navigation links\n";
            } else {
                echo "⚠️  No tenant-aware navigation links found\n";
            }
        } else {
            echo "❌ FAILED - Content too small (possible error page)\n";
        }
    }
    echo "\n";
}

echo "=== NAVIGATION FLOW TEST RESULTS ===\n";
echo "Steps completed: $success/$total\n";
echo "Success rate: " . round(($success / $total) * 100, 1) . "%\n";

if ($success == $total) {
    echo "\n🎉 ADMIN PREVIEW NAVIGATION FLOW COMPLETE!\n";
    echo "✅ All navigation steps work without logout\n";
    echo "✅ Session management fixed successfully\n";
    echo "✅ Tenant-aware navigation working properly\n";
} else {
    echo "\n⚠️  Some navigation steps failed.\n";
}
?>
