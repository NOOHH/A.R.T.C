<?php

/**
 * COMPREHENSIVE ADMIN PREVIEW SYSTEM TEST
 * Tests all admin preview pages with proper error reporting and validation
 */

echo "🧪 COMPREHENSIVE ADMIN PREVIEW SYSTEM TEST\n";
echo "==========================================\n\n";

$baseUrl = "http://localhost:8000/t/draft/test1";
$params = "?website=15&preview=true&t=" . time();

// Define ALL admin preview pages that should work
$adminPages = [
    'Dashboard' => '/admin-dashboard',
    'Students' => '/admin/students', 
    'Professors' => '/admin/professors',
    'Programs' => '/admin/programs',
    'Modules' => '/admin/modules',
    'Announcements' => '/admin/announcements',
    'Batch Enrollment' => '/admin/batches',
    'Analytics' => '/admin/analytics',
    'Settings' => '/admin/settings',
    'Packages' => '/admin/packages',
    'Directors' => '/admin/directors',
    'Quiz Generator' => '/admin/quiz-generator',
    'Payment Pending' => '/admin-student-registration/payment/pending',
    'Payment History' => '/admin-student-registration/payment/history'
];

// Additional pages mentioned by user
$additionalPages = [
    'Archived Programs' => '/admin/programs/archived',
    'FAQ Management' => '/admin/faq',
    'Create New Announcement' => '/admin/announcements/create',
    'View Announcement' => '/admin/announcements/1',
    'Edit Announcement' => '/admin/announcements/1/edit'
];

$allPages = array_merge($adminPages, $additionalPages);
$results = [];
$detailedErrors = [];

echo "Testing " . count($allPages) . " admin preview pages...\n\n";

foreach ($allPages as $name => $path) {
    $url = $baseUrl . $path . $params;
    echo "🔍 Testing: {$name}\n";
    echo "   URL: {$url}\n";
    
    // Test with cURL to get detailed error information
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AdminPreviewTester/2.0');
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ❌ cURL Error: {$error}\n";
        $results[$name] = "❌ cURL Error";
        $detailedErrors[$name] = $error;
    } elseif ($httpCode !== 200) {
        echo "   ❌ HTTP {$httpCode}\n";
        $results[$name] = "❌ HTTP {$httpCode}";
        $detailedErrors[$name] = "HTTP {$httpCode}";
    } elseif (strpos($response, 'Error rendering full view') !== false) {
        // Extract detailed error message
        preg_match('/Error rendering full view: ([^<]+)/', $response, $matches);
        $errorMsg = isset($matches[1]) ? trim($matches[1]) : 'Unknown error';
        echo "   ❌ View Error: {$errorMsg}\n";
        $results[$name] = "❌ View Error";
        $detailedErrors[$name] = $errorMsg;
    } elseif (strpos($response, 'Test1') !== false || strpos($response, 'test1') !== false) {
        echo "   ✅ Working with Test1 branding\n";
        $results[$name] = "✅ Working";
    } elseif (strpos($response, '200 OK') !== false || strpos($response, 'HTTP/1.1 200') !== false) {
        echo "   ⚠️  Page loads but no Test1 branding found\n";
        $results[$name] = "⚠️ No branding";
        $detailedErrors[$name] = "Page loads but no tenant customization detected";
    } else {
        echo "   ❓ Unknown response\n";
        $results[$name] = "❓ Unknown";
        $detailedErrors[$name] = "Unknown response format";
    }
    
    echo "\n";
    usleep(500000); // 0.5 second delay between requests
}

// Generate comprehensive report
echo "=== COMPREHENSIVE TEST RESULTS ===\n";
$working = 0;
$errors = 0;
$warnings = 0;
$unknown = 0;

foreach ($results as $page => $status) {
    echo "{$page}: {$status}\n";
    if (strpos($status, '✅') !== false) {
        $working++;
    } elseif (strpos($status, '⚠️') !== false) {
        $warnings++;
    } elseif (strpos($status, '❓') !== false) {
        $unknown++;
    } else {
        $errors++;
    }
}

echo "\n=== SUMMARY STATISTICS ===\n";
echo "Total Pages Tested: " . count($allPages) . "\n";
echo "✅ Working: {$working}\n";
echo "⚠️  Warnings: {$warnings}\n";
echo "❌ Errors: {$errors}\n";
echo "❓ Unknown: {$unknown}\n";

$percentage = round(($working / count($allPages)) * 100, 1);
echo "✅ Success Rate: {$percentage}%\n";

// Detailed error analysis
if ($errors > 0) {
    echo "\n=== DETAILED ERROR ANALYSIS ===\n";
    foreach ($detailedErrors as $page => $error) {
        if (strpos($results[$page], '❌') !== false) {
            echo "\n🔴 {$page}:\n";
            echo "   Error: {$error}\n";
            
            // Categorize error type
            if (strpos($error, 'Undefined property') !== false) {
                echo "   Type: Missing Property Error\n";
                echo "   Fix: Add missing property to mock data\n";
            } elseif (strpos($error, 'SQLSTATE') !== false) {
                echo "   Type: Database Query Error\n";
                echo "   Fix: Replace database queries with mock data\n";
            } elseif (strpos($error, 'HTTP 404') !== false) {
                echo "   Type: Route Not Found\n";
                echo "   Fix: Add missing preview route\n";
            } elseif (strpos($error, 'HTTP 500') !== false) {
                echo "   Type: Server Error\n";
                echo "   Fix: Debug controller method\n";
            }
        }
    }
}

// Generate action plan
echo "\n=== ACTION PLAN ===\n";
if ($working === count($allPages)) {
    echo "🎉 ALL PAGES WORKING! Admin preview system is 100% functional!\n";
} elseif ($working >= (count($allPages) * 0.8)) {
    echo "🚀 EXCELLENT! {$percentage}% of pages working. Focus on fixing the remaining " . ($errors + $unknown) . " pages.\n";
} elseif ($working >= (count($allPages) * 0.6)) {
    echo "👍 GOOD PROGRESS! {$percentage}% working. Continue fixing remaining issues.\n";
} else {
    echo "⚠️  MORE WORK NEEDED! Only {$percentage}% working. Systematic fixes required.\n";
}

echo "\nNext Steps:\n";
echo "1. Fix missing property errors in mock data\n";
echo "2. Add missing preview routes for 404 errors\n";
echo "3. Replace database queries with mock data\n";
echo "4. Debug and fix server errors\n";
echo "5. Re-run this test to validate fixes\n";
echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
