<?php
// Comprehensive test of admin preview pages with branding validation
echo "=== COMPREHENSIVE ADMIN PREVIEW TEST WITH BRANDING VALIDATION ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$params = '?website=15&preview=true&t=' . time();

$adminPages = [
    'Dashboard' => '/t/draft/test1/admin-dashboard',
    'Students' => '/t/draft/test1/admin/students',
    'Professors' => '/t/draft/test1/admin/professors', 
    'Programs' => '/t/draft/test1/admin/programs',
    'Modules' => '/t/draft/test1/admin/modules',
    'Announcements' => '/t/draft/test1/admin/announcements',
    'Batch Enrollment' => '/t/draft/test1/admin/batches',
    'Analytics' => '/t/draft/test1/admin/analytics',
    'Settings' => '/t/draft/test1/admin/settings',
    'Packages' => '/t/draft/test1/admin/packages',
    'Directors' => '/t/draft/test1/admin/directors',
    'Quiz Generator' => '/t/draft/test1/admin/quiz-generator',
    'Payment Pending' => '/t/draft/test1/admin-student-registration/payment/pending',
    'Payment History' => '/t/draft/test1/admin-student-registration/payment/history',
    'Archived Programs' => '/t/draft/test1/admin/programs/archived'
];

$successCount = 0;
$totalCount = count($adminPages);
$results = [];

echo "Testing " . $totalCount . " admin pages for functionality and branding...\n\n";

foreach ($adminPages as $pageName => $path) {
    $url = $baseUrl . $path . $params;
    
    echo "Testing {$pageName}... ";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            $error = error_get_last();
            echo "❌ FAILED - Network error\n";
            $results[$pageName] = [
                'status' => 'FAILED', 
                'error' => 'Network error: ' . $error['message'],
                'branding' => 'N/A'
            ];
            continue;
        }
        
        // Check for HTTP errors by examining response headers
        if (isset($http_response_header)) {
            $statusLine = $http_response_header[0];
            preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches);
            $statusCode = isset($matches[1]) ? (int)$matches[1] : 200;
            
            if ($statusCode >= 400) {
                echo "❌ FAILED - HTTP {$statusCode}\n";
                $results[$pageName] = [
                    'status' => 'FAILED', 
                    'error' => "HTTP {$statusCode}",
                    'branding' => 'N/A'
                ];
                continue;
            }
        }
        
        // Check for PHP errors or Laravel error pages
        if (strpos($response, 'Whoops') !== false || 
            strpos($response, 'Laravel') !== false && strpos($response, 'error') !== false ||
            strpos($response, 'Exception') !== false ||
            strpos($response, 'Fatal error') !== false) {
            echo "❌ FAILED - PHP/Laravel error\n";
            $results[$pageName] = [
                'status' => 'FAILED', 
                'error' => 'PHP/Laravel error detected',
                'branding' => 'N/A'
            ];
            continue;
        }
        
        // Branding validation - exclude support email from ARTC count
        $artcCount = 0;
        $test1Count = substr_count(strtolower($response), 'test1');
        
        // Count ARTC occurrences but exclude support email context
        $artcMatches = [];
        if (preg_match_all('/ARTC/i', $response, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $context = substr($response, max(0, $match[1] - 100), 200);
                // Skip if it's in support email context
                if (!strpos($context, 'support@artc.edu') && !strpos($context, 'artc.edu')) {
                    $artcCount++;
                    $artcMatches[] = $context;
                }
            }
        }
        
        // Check if page has proper content (not just error page)
        $hasContent = (
            strpos($response, 'Admin Portal') !== false ||
            strpos($response, 'dashboard') !== false ||
            strpos($response, 'admin') !== false
        );
        
        if (!$hasContent) {
            echo "❌ FAILED - No admin content\n";
            $results[$pageName] = [
                'status' => 'FAILED', 
                'error' => 'No admin content detected',
                'branding' => 'N/A'
            ];
            continue;
        }
        
        // Determine branding status
        $brandingStatus = 'UNKNOWN';
        if ($test1Count > 0 && $artcCount === 0) {
            $brandingStatus = '✅ CLEAN TEST1 ONLY';
        } elseif ($test1Count > 0 && $artcCount > 0) {
            $brandingStatus = '⚠️ MIXED (Test1 + ARTC)';
        } elseif ($test1Count === 0 && $artcCount > 0) {
            $brandingStatus = '❌ ARTC ONLY';
        } elseif ($test1Count === 0 && $artcCount === 0) {
            $brandingStatus = '❓ NO BRANDING';
        }
        
        echo "✅ SUCCESS - {$brandingStatus}\n";
        $successCount++;
        $results[$pageName] = [
            'status' => 'SUCCESS', 
            'error' => null,
            'branding' => $brandingStatus,
            'artc_count' => $artcCount,
            'test1_count' => $test1Count
        ];
        
    } catch (Exception $e) {
        echo "❌ FAILED - Exception: " . $e->getMessage() . "\n";
        $results[$pageName] = [
            'status' => 'FAILED', 
            'error' => 'Exception: ' . $e->getMessage(),
            'branding' => 'N/A'
        ];
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "SUMMARY REPORT\n";
echo str_repeat("=", 80) . "\n";
echo "Success Rate: {$successCount}/{$totalCount} (" . round(($successCount/$totalCount)*100, 1) . "%)\n\n";

// Categorize results
$successful = [];
$failed = [];
$brandingIssues = [];

foreach ($results as $page => $result) {
    if ($result['status'] === 'SUCCESS') {
        $successful[] = $page;
        if (strpos($result['branding'], 'MIXED') !== false || strpos($result['branding'], 'ARTC ONLY') !== false) {
            $brandingIssues[] = $page . ' - ' . $result['branding'];
        }
    } else {
        $failed[] = $page . ' - ' . $result['error'];
    }
}

echo "✅ SUCCESSFUL PAGES (" . count($successful) . "):\n";
foreach ($successful as $page) {
    echo "  • {$page}\n";
}

if (!empty($failed)) {
    echo "\n❌ FAILED PAGES (" . count($failed) . "):\n";
    foreach ($failed as $failure) {
        echo "  • {$failure}\n";
    }
}

if (!empty($brandingIssues)) {
    echo "\n⚠️ BRANDING ISSUES (" . count($brandingIssues) . "):\n";
    foreach ($brandingIssues as $issue) {
        echo "  • {$issue}\n";
    }
} else {
    echo "\n🎉 ALL SUCCESSFUL PAGES HAVE CLEAN TEST1 BRANDING!\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "BRANDING ANALYSIS:\n";
echo str_repeat("=", 80) . "\n";

$cleanBranding = 0;
$mixedBranding = 0;
$artcOnly = 0;
$noBranding = 0;

foreach ($results as $page => $result) {
    if ($result['status'] === 'SUCCESS') {
        if (strpos($result['branding'], 'CLEAN TEST1 ONLY') !== false) {
            $cleanBranding++;
        } elseif (strpos($result['branding'], 'MIXED') !== false) {
            $mixedBranding++;
        } elseif (strpos($result['branding'], 'ARTC ONLY') !== false) {
            $artcOnly++;
        } else {
            $noBranding++;
        }
    }
}

echo "✅ Clean Test1 Branding: {$cleanBranding} pages\n";
echo "⚠️ Mixed Branding: {$mixedBranding} pages\n";
echo "❌ ARTC Only: {$artcOnly} pages\n";
echo "❓ No Branding: {$noBranding} pages\n";

if ($cleanBranding === $successCount) {
    echo "\n🎯 PERFECT! All successful pages show clean Test1 branding.\n";
} else {
    echo "\n⚠️ Some pages still have branding issues that need attention.\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
?>
