<?php
/**
 * Test the specific URLs the user reported as 404
 */

echo "🔍 TESTING USER'S SPECIFIC 404 URLS\n";
echo "===================================\n";

$test_urls = [
    'Pending Page' => 'http://127.0.0.1:8000/t/draft/test1/admin-student-registration/pending?website=15&preview=true&t=1755965595578',
    'History Page' => 'http://127.0.0.1:8000/t/draft/test1/admin-student-registration/history?website=15&preview=true&t=1755965595578'
];

foreach ($test_urls as $name => $url) {
    echo "\nTesting {$name}: {$url}\n";
    
    $response = @file_get_contents($url, false, stream_context_create([
        'http' => ['timeout' => 10, 'ignore_errors' => true]
    ]));
    
    if ($response === false) {
        echo "❌ FAILED - Could not fetch URL (still 404)\n";
    } else {
        if (strpos($response, 'TEST11') !== false || strpos($response, 'test1') !== false) {
            echo "✅ SUCCESS - Page loaded with tenant branding\n";
            echo "Response snippet: " . substr(strip_tags($response), 0, 100) . "...\n";
        } else {
            echo "⚠️  LOADED - But may need TEST11/test1 branding\n";
            echo "Response snippet: " . substr(strip_tags($response), 0, 100) . "...\n";
        }
    }
}

// Check the routes are registered
echo "\n📋 CHECKING ROUTE REGISTRATION:\n";
echo "==============================\n";

$routes_to_check = [
    'tenant.draft.admin.student.registration.pending',
    'tenant.draft.admin.student.registration.history'
];

foreach ($routes_to_check as $route) {
    echo "Checking route: {$route}\n";
    
    // Use artisan to check if route exists
    $result = shell_exec("php artisan route:list --name={$route} 2>&1");
    
    if (strpos($result, $route) !== false) {
        echo "✅ Route registered\n";
    } else {
        echo "❌ Route not found\n";
        echo "Output: {$result}\n";
    }
}

echo "\n🏁 Test completed!\n";
?>
