<?php
// Test the basic admin preview routes we just created

$tenant = 'test1';
$base_url = 'http://127.0.0.1:8000';

$admin_pages = [
    'dashboard' => "/t/draft/$tenant/admin-dashboard",
    'students' => "/t/draft/$tenant/admin/students",
    'professors' => "/t/draft/$tenant/admin/professors", 
    'programs' => "/t/draft/$tenant/admin/programs",
    'modules' => "/t/draft/$tenant/admin/modules"
];

echo "=== Testing Basic Admin Preview Routes ===\n\n";

foreach ($admin_pages as $page_name => $url) {
    echo "Testing: " . ucfirst($page_name) . "\n";
    echo "URL: $base_url$url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Cookie: preview_tenant=' . $tenant
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        echo "✅ Status: SUCCESS ($http_code)\n";
        
        // Check for common error indicators
        if (strpos($response, "doesn't exist") !== false) {
            echo "⚠️  Warning: Contains 'doesn't exist' - possible database error\n";
        } elseif (strpos($response, "Error") !== false) {
            echo "⚠️  Warning: Contains 'Error' - possible issue\n";
        } elseif (strpos($response, "preview") !== false || strpos($response, "Preview") !== false) {
            echo "✅ Contains preview content - likely working correctly\n";
        } else {
            echo "ℹ️  Page loaded successfully\n";
        }
    } else {
        echo "❌ Status: FAILED ($http_code)\n";
    }
    
    echo "---\n";
}

echo "\nNext steps if basic routes work:\n";
echo "1. ✅ Create remaining preview methods for other controllers\n";
echo "2. ✅ Update admin sidebar for tenant-aware routing\n";
echo "3. ✅ Add admin middleware bypass for preview mode\n";
echo "4. ✅ Test all admin functionality in preview mode\n";
?>
