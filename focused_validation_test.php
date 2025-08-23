<?php
/**
 * Focused Validation Test for User-Reported Issues
 * Tests the specific sections mentioned: Pending, History, Payment Pending, Payment History, 
 * Certificates, Archived Content, Course Content Upload
 */

echo "🎯 FOCUSED VALIDATION TEST - " . date('Y-m-d H:i:s') . "\n";
echo "Testing specifically reported problematic sections\n";
echo "=================================================================\n\n";

$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

// Focus on the exact sections mentioned in the user request
$userReportedIssues = [
    'Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'Certificates' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'Archived Content' => "/t/draft/{$tenant}/admin/archived?{$params}",
    'Course Content Upload' => "/t/draft/{$tenant}/admin/courses/upload?{$params}",
];

$allFixed = true;
$results = [];

foreach ($userReportedIssues as $sectionName => $url) {
    echo "🔍 TESTING: {$sectionName}\n";
    echo str_repeat('-', 60) . "\n";
    
    $fullUrl = $baseUrl . $url;
    
    // Test the URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: {$error}\n";
        $allFixed = false;
    } elseif ($httpCode == 404) {
        echo "❌ STILL 404 - Route not found!\n";
        $allFixed = false;
    } elseif ($httpCode == 200) {
        $responseSize = strlen($response);
        $brandingCount = substr_count($response, 'TEST11');
        
        echo "✅ HTTP Status: 200 (Working)\n";
        echo "📄 Response Size: " . number_format($responseSize) . " bytes\n";
        
        if ($brandingCount >= 2) {
            echo "✅ TEST11 Branding: {$brandingCount} instances found\n";
            echo "✅ FULLY FIXED - Working with proper branding!\n";
        } elseif ($brandingCount > 0) {
            echo "⚠️  TEST11 Branding: {$brandingCount} instances (partial)\n";
            echo "⚠️  PARTIALLY FIXED - Working but may need more branding\n";
        } else {
            echo "❌ TEST11 Branding: None found\n";
            echo "❌ PARTIALLY FIXED - Working but no customization\n";
            $allFixed = false;
        }
        
        // Check for specific error indicators
        if (strpos($response, '404') !== false || strpos($response, 'Not Found') !== false) {
            echo "⚠️  Note: Page content mentions 404/Not Found (may be old content)\n";
        }
        
        $results[$sectionName] = [
            'working' => true,
            'branded' => $brandingCount >= 2,
            'size' => $responseSize,
            'branding_count' => $brandingCount
        ];
    } else {
        echo "⚠️  HTTP Status: {$httpCode} (Unexpected)\n";
        echo "📄 Response Size: " . number_format(strlen($response)) . " bytes\n";
        $allFixed = false;
    }
    
    echo "\n";
}

// Summary for user
echo str_repeat('=', 80) . "\n";
echo "📊 SUMMARY FOR REPORTED ISSUES\n";
echo str_repeat('=', 80) . "\n\n";

$fixedCount = 0;
foreach ($results as $section => $result) {
    $status = $result['working'] ? ($result['branded'] ? '✅ FULLY FIXED' : '⚠️ PARTIALLY FIXED') : '❌ STILL BROKEN';
    echo sprintf("%-25s %s\n", $section . ':', $status);
    if ($result['working'] && $result['branded']) {
        $fixedCount++;
    }
}

echo "\n📈 PROGRESS:\n";
echo "• Total Reported Issues: " . count($userReportedIssues) . "\n";
echo "• Fully Fixed: {$fixedCount}\n";
echo "• Success Rate: " . round(($fixedCount / count($userReportedIssues)) * 100) . "%\n\n";

if ($allFixed && $fixedCount === count($userReportedIssues)) {
    echo "🎉 SUCCESS! All reported issues have been resolved!\n";
    echo "✅ All sections are now working with TEST11 branding\n";
    echo "✅ No more 404 errors for the reported sections\n";
    echo "✅ Tenant customization is working properly\n\n";
    
    echo "🔧 IMPLEMENTED SOLUTIONS:\n";
    echo "• Fixed Payment routes to use controller methods instead of missing views\n";
    echo "• Added missing tenant preview routes for Certificates, Archived Content, Course Upload\n";
    echo "• Implemented controller methods with TEST11 branding integration\n";
    echo "• Updated admin sidebar to use tenant-aware URLs\n";
    echo "• Applied AdminPreviewCustomization trait consistently\n";
} else {
    echo "⚠️  SOME ISSUES REMAIN - Need additional fixes\n";
}

echo "\nValidation completed at " . date('Y-m-d H:i:s') . "\n";

// Quick route verification
echo "\n" . str_repeat('=', 80) . "\n";
echo "🛣️  ROUTE VERIFICATION\n";
echo str_repeat('=', 80) . "\n";

$routeCheck = shell_exec('php artisan route:list | findstr "draft.*admin.*certificates\|draft.*admin.*archived\|draft.*admin.*courses\|draft.*admin.*payments"');
echo "Current tenant routes for the fixed sections:\n";
echo $routeCheck ?: "No routes found (this might indicate an issue)";

?>
