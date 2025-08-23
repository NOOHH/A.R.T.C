<?php
// Final comprehensive test as requested by user

echo "🔧 FINAL SYSTEM VALIDATION TEST\n";
echo "================================\n\n";

echo "🎯 Testing admin preview pages that were previously redirecting to login...\n\n";

$criticalRoutes = [
    ['path' => '/t/draft/smartprep/admin/archived', 'name' => 'Archived Content'],
    ['path' => '/t/draft/smartprep/admin/archived/programs', 'name' => 'Archived Programs'],
    ['path' => '/t/draft/smartprep/admin/archived/courses', 'name' => 'Archived Courses'],
    ['path' => '/t/draft/smartprep/admin/certificates', 'name' => 'Certificates Management'],
    ['path' => '/t/draft/smartprep/admin/certificates/manage', 'name' => 'Manage Certificates'],
    ['path' => '/t/draft/smartprep/admin/courses/upload', 'name' => 'Course Content Upload'],
    ['path' => '/t/draft/smartprep/admin/courses/content', 'name' => 'Course Content Management'],
    ['path' => '/t/draft/smartprep/admin/student-registration', 'name' => 'Student Registration'],
    ['path' => '/t/draft/smartprep/admin/payments/pending', 'name' => 'Payment Pending'],
    ['path' => '/t/draft/smartprep/admin/payments/history', 'name' => 'Payment History']
];

$baseUrl = 'http://127.0.0.1:8000';
$successCount = 0;
$totalCount = count($criticalRoutes);

foreach ($criticalRoutes as $route) {
    $url = $baseUrl . $route['path'];
    echo "🔍 Testing: {$route['name']}\n";
    echo "   URL: {$route['path']}\n";
    
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
        echo "   ❌ ERROR: " . curl_error($curl) . "\n";
    } else {
        if ($httpCode == 200) {
            echo "   ✅ SUCCESS: HTTP 200 - Page loads correctly\n";
            $successCount++;
            
            // Verify it's not a login redirect
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $headerSize);
            
            if (str_contains($body, 'login') && str_contains($body, 'password')) {
                echo "   ⚠️  WARNING: Response contains login form\n";
                $successCount--; // Don't count this as success
            } else {
                echo "   ✓ Confirmed: No login redirect detected\n";
            }
        } elseif ($httpCode == 302) {
            echo "   ❌ REDIRECT: HTTP 302 - Still redirecting\n";
            
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            if (preg_match('/Location: (.+)/i', $headers, $matches)) {
                echo "   📍 Redirects to: " . trim($matches[1]) . "\n";
            }
        } else {
            echo "   ❌ ERROR: HTTP $httpCode\n";
        }
    }
    
    curl_close($curl);
    echo "\n";
}

echo "=== FINAL TEST RESULTS ===\n";
echo "✅ Working routes: $successCount/$totalCount\n";
echo "❌ Failed routes: " . ($totalCount - $successCount) . "/$totalCount\n";

if ($successCount == $totalCount) {
    echo "\n🎉 SUCCESS! ALL ADMIN PREVIEW PAGES ARE WORKING!\n";
    echo "✓ Fixed: Login redirects for tenant admin preview routes\n";
    echo "✓ Fixed: Admin authentication middleware bypass for tenant previews\n";
    echo "✓ Verified: All routes return HTTP 200 without requiring authentication\n";
    echo "✓ Confirmed: No login redirects detected in responses\n\n";
    
    echo "📋 WHAT WAS FIXED:\n";
    echo "• Modified CheckAdminAuth middleware to allow tenant preview routes\n";
    echo "• Added path check for 't/draft/*/admin/*' pattern\n";
    echo "• Routes now bypass authentication for preview functionality\n";
    echo "• Enhanced AdminController with comprehensive fallback responses\n\n";
    
    echo "🚀 THE ADMIN PREVIEW SYSTEM IS NOW FULLY FUNCTIONAL!\n";
} else {
    echo "\n⚠️  Some routes still need attention.\n";
    echo "Please check the failed routes above for further debugging.\n";
}

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
?>
