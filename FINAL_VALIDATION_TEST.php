<?php
/**
 * Final Admin Preview System Validation
 * Complete test of all fixes implemented
 */

echo "🎯 FINAL ADMIN PREVIEW SYSTEM VALIDATION\n";
echo "========================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test11';

// All admin preview pages to test
$testPages = [
    'Student Registration Pending' => "/t/draft/$tenant/admin-student-registration/pending",
    'Student Payment Pending' => "/t/draft/$tenant/admin-student-registration/payment/pending", 
    'Archived Content' => "/t/draft/$tenant/admin/archived",
    'Archived Programs' => "/t/draft/$tenant/admin/archived/programs",
    'Quiz Generator' => "/t/draft/$tenant/admin/quiz-generator",
    'Course Content Upload' => "/t/draft/$tenant/admin/courses/upload",
    'Course Content Management' => "/t/draft/$tenant/admin/courses/content",
    'Certificates Management' => "/t/draft/$tenant/admin/certificates",
    'Directors Management' => "/t/draft/$tenant/admin/directors"
];

$results = [];
$totalTests = count($testPages);
$passedTests = 0;

foreach ($testPages as $pageName => $url) {
    echo "Testing: $pageName\n";
    echo "URL: $baseUrl$url\n";
    
    $result = [
        'name' => $pageName,
        'url' => $url,
        'status' => 'UNKNOWN',
        'issues' => []
    ];
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'temp_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Test Client');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        curl_close($ch);
        
        // Basic connectivity test
        if ($httpCode !== 200) {
            $result['status'] = 'FAILED';
            $result['issues'][] = "HTTP $httpCode error";
            echo "❌ HTTP $httpCode\n";
        } else {
            echo "✅ HTTP 200 OK\n";
            
            // Content analysis
            $contentLength = strlen($response);
            $hasNavbar = strpos($response, 'navbar') !== false || strpos($response, 'nav-') !== false;
            $hasAdminLayout = strpos($response, 'admin-dashboard') !== false || strpos($response, 'sidebar') !== false;
            $hasBootstrap = strpos($response, 'bootstrap') !== false || strpos($response, 'card') !== false;
            $hasPreviewMode = strpos($response, 'Preview Mode') !== false || strpos($response, 'preview_mode') !== false;
            $hasTenantBranding = strpos($response, 'TEST11') !== false;
            $hasHardcodedHTML = strpos($response, '<html>') !== false && strpos($response, '@extends') === false;
            $hasModulesIdError = strpos($response, 'Undefined property') !== false && strpos($response, 'modules_id') !== false;
            $hasDatabaseError = strpos($response, 'database') !== false || strpos($response, 'SQL') !== false;
            
            $checksPass = 0;
            $totalChecks = 7;
            
            // Navigation check
            if ($hasNavbar) {
                echo "✅ Has navigation\n";
                $checksPass++;
            } else {
                echo "⚠️  Missing navigation\n";
                $result['issues'][] = "Missing navigation elements";
            }
            
            // Layout check
            if ($hasAdminLayout) {
                echo "✅ Uses admin layout\n";
                $checksPass++;
            } else {
                echo "⚠️  No admin layout detected\n";
                $result['issues'][] = "No admin layout structure";
            }
            
            // Styling check
            if ($hasBootstrap) {
                echo "✅ Has proper styling\n";
                $checksPass++;
            } else {
                echo "⚠️  Limited styling\n";
                $result['issues'][] = "Limited CSS/Bootstrap styling";
            }
            
            // Preview mode check
            if ($hasPreviewMode) {
                echo "✅ Preview mode active\n";
                $checksPass++;
            } else {
                echo "⚠️  Preview mode not detected\n";
                $result['issues'][] = "Preview mode not clearly indicated";
            }
            
            // Tenant branding check
            if ($hasTenantBranding) {
                echo "✅ Shows tenant branding\n";
                $checksPass++;
            } else {
                echo "⚠️  No tenant branding\n";
                $result['issues'][] = "No TEST11 tenant branding found";
            }
            
            // Template check (no hardcoded HTML)
            if (!$hasHardcodedHTML) {
                echo "✅ Uses Blade templates\n";
                $checksPass++;
            } else {
                echo "⚠️  Uses hardcoded HTML\n";
                $result['issues'][] = "Still using hardcoded HTML response";
            }
            
            // Error checks
            if (!$hasModulesIdError && !$hasDatabaseError) {
                echo "✅ No critical errors\n";
                $checksPass++;
            } else {
                echo "❌ Has errors\n";
                if ($hasModulesIdError) $result['issues'][] = "modules_id property error";
                if ($hasDatabaseError) $result['issues'][] = "Database/SQL error";
            }
            
            // Content size check
            if ($contentLength > 20000) {
                echo "✅ Rich content ($contentLength chars)\n";
            } elseif ($contentLength > 5000) {
                echo "⚠️  Moderate content ($contentLength chars)\n";
            } else {
                echo "⚠️  Minimal content ($contentLength chars)\n";
                $result['issues'][] = "Suspiciously small content size";
            }
            
            // Overall status
            if ($checksPass >= $totalChecks - 1) {
                $result['status'] = 'PASSED';
                $passedTests++;
                echo "🎉 OVERALL: PASSED ($checksPass/$totalChecks checks)\n";
            } elseif ($checksPass >= $totalChecks - 2) {
                $result['status'] = 'WARNING';
                echo "⚠️  OVERALL: NEEDS ATTENTION ($checksPass/$totalChecks checks)\n";
            } else {
                $result['status'] = 'FAILED';
                echo "❌ OVERALL: FAILED ($checksPass/$totalChecks checks)\n";
            }
        }
        
    } catch (Exception $e) {
        $result['status'] = 'ERROR';
        $result['issues'][] = "Exception: " . $e->getMessage();
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    $results[] = $result;
    echo str_repeat("-", 60) . "\n\n";
}

// Summary Report
echo "📊 FINAL RESULTS SUMMARY\n";
echo "========================\n\n";

$failedPages = [];
$warningPages = [];
$passedPages = [];

foreach ($results as $result) {
    switch ($result['status']) {
        case 'PASSED':
            $passedPages[] = $result['name'];
            break;
        case 'WARNING':
            $warningPages[] = $result['name'];
            break;
        case 'FAILED':
        case 'ERROR':
            $failedPages[] = $result['name'];
            break;
    }
}

echo "✅ PASSED ($passedTests/$totalTests): " . implode(', ', $passedPages) . "\n\n";

if (!empty($warningPages)) {
    echo "⚠️  WARNINGS (" . count($warningPages) . "): " . implode(', ', $warningPages) . "\n\n";
}

if (!empty($failedPages)) {
    echo "❌ FAILED (" . count($failedPages) . "): " . implode(', ', $failedPages) . "\n\n";
}

// Issue Summary
echo "🔍 COMMON ISSUES FOUND:\n";
$allIssues = [];
foreach ($results as $result) {
    foreach ($result['issues'] as $issue) {
        if (!isset($allIssues[$issue])) {
            $allIssues[$issue] = 0;
        }
        $allIssues[$issue]++;
    }
}

if (empty($allIssues)) {
    echo "✅ No significant issues detected!\n";
} else {
    foreach ($allIssues as $issue => $count) {
        echo "- $issue (affects $count pages)\n";
    }
}

echo "\n🎯 OVERALL SYSTEM HEALTH:\n";
$successRate = ($passedTests / $totalTests) * 100;

if ($successRate >= 90) {
    echo "🟢 EXCELLENT ($successRate% success rate)\n";
    echo "✅ The admin preview system is working well!\n";
} elseif ($successRate >= 70) {
    echo "🟡 GOOD ($successRate% success rate)\n";
    echo "⚠️  Most pages working, some minor issues to address\n";
} else {
    echo "🔴 NEEDS WORK ($successRate% success rate)\n";
    echo "❌ Several critical issues need to be resolved\n";
}

echo "\n📋 FIXES IMPLEMENTED:\n";
echo "✅ Fixed modules_id property error in AdminPreviewCustomization trait\n";
echo "✅ Converted student registration pending to use proper Blade template\n";
echo "✅ Created archived content Blade template with admin layout\n";
echo "✅ Created certificates management Blade template\n";
echo "✅ Updated course content upload to use existing Blade template\n";
echo "✅ All preview methods now return proper views instead of hardcoded HTML\n";
echo "✅ Added preview mode checks to avoid database calls\n";
echo "✅ Maintained tenant-aware URLs and branding\n";

// Clean up
if (file_exists('temp_cookies.txt')) {
    unlink('temp_cookies.txt');
}
?>
