<?php
/**
 * COMPREHENSIVE ISSUE INVESTIGATION
 * Testing all reported problems:
 * - Registration, Pending/History, Archived sections
 * - Module selection redirect issues  
 * - Missing Add Program button
 * - Course Content Upload on modules
 */

echo "🔍 COMPREHENSIVE ISSUE INVESTIGATION - " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n\n";

$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

// Issues to investigate
$issueUrls = [
    'Registration' => "/t/draft/{$tenant}/admin/student-registration?{$params}",
    'Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'Archived Programs' => "/t/draft/{$tenant}/admin/archived/programs?{$params}",
    'Archived Courses' => "/t/draft/{$tenant}/admin/archived/courses?{$params}",
    'Archived Materials' => "/t/draft/{$tenant}/admin/archived/materials?{$params}",
    'Course Content Upload' => "/t/draft/{$tenant}/admin/modules?{$params}",
    'Certificates Management' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'Programs Management' => "/t/draft/{$tenant}/admin/programs?{$params}",
    'Packages Management' => "/t/draft/{$tenant}/admin/packages?{$params}",
];

$results = [];
$redirectIssues = [];
$errors = [];

echo "1️⃣ TESTING ALL REPORTED ISSUE URLS\n";
echo str_repeat('-', 60) . "\n";

foreach ($issueUrls as $issueName => $url) {
    echo "Testing: {$issueName}\n";
    
    $fullUrl = $baseUrl . $url;
    echo "URL: {$fullUrl}\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects to catch them
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result = [
        'working' => false,
        'has_redirect' => false,
        'redirect_url' => $redirectUrl,
        'size' => strlen($response),
        'branded' => false
    ];
    
    if ($error) {
        echo "❌ CURL Error: {$error}\n";
        $errors[] = "{$issueName}: {$error}";
    } elseif ($httpCode == 302 || $httpCode == 301) {
        echo "🔄 HTTP {$httpCode} - Redirect detected\n";
        echo "📍 Redirect to: {$redirectUrl}\n";
        
        $result['has_redirect'] = true;
        $result['redirect_url'] = $redirectUrl;
        
        // Check if redirecting to ARTC instead of staying in tenant
        if (strpos($redirectUrl, '/t/draft/') === false && strpos($redirectUrl, 'localhost') !== false) {
            echo "❌ PROBLEM: Redirecting outside tenant context to ARTC\n";
            $redirectIssues[] = "{$issueName}: Redirects to {$redirectUrl} (outside tenant)";
        } else {
            echo "✅ Redirect within tenant context\n";
        }
    } elseif ($httpCode == 404) {
        echo "❌ HTTP 404 - Route not found\n";
        $errors[] = "{$issueName}: 404 Not Found";
    } elseif ($httpCode == 500) {
        echo "❌ HTTP 500 - Server Error\n";
        if (strpos($response, 'not defined') !== false) {
            echo "   🔍 Likely route definition error\n";
        }
        $errors[] = "{$issueName}: 500 Server Error";
    } elseif ($httpCode == 200) {
        echo "✅ HTTP 200 - Working\n";
        echo "📄 Size: " . number_format(strlen($response)) . " bytes\n";
        
        $result['working'] = true;
        
        // Check for TEST11 branding
        $brandingCount = substr_count($response, 'TEST11');
        if ($brandingCount >= 2) {
            echo "✅ TEST11 Branding: {$brandingCount} instances\n";
            $result['branded'] = true;
        } else {
            echo "⚠️  TEST11 Branding: Only {$brandingCount} instances\n";
        }
        
        // Check for specific issues mentioned
        if ($issueName === 'Programs Management') {
            if (strpos($response, 'Add Program') !== false || strpos($response, 'add-program') !== false) {
                echo "✅ Add Program button found\n";
            } else {
                echo "❌ Add Program button MISSING\n";
                $errors[] = "{$issueName}: Add Program button missing";
            }
        }
        
        if ($issueName === 'Packages Management') {
            if (strpos($response, 'Add Package') !== false || strpos($response, 'add-package') !== false) {
                echo "✅ Add Package button found\n";
            } else {
                echo "❌ Add Package button MISSING\n";
                $errors[] = "{$issueName}: Add Package button missing";
            }
        }
    } else {
        echo "⚠️  HTTP {$httpCode}\n";
        $errors[] = "{$issueName}: HTTP {$httpCode}";
    }
    
    $results[$issueName] = $result;
    echo "\n";
}

// 2. SPECIFIC MODULE REDIRECT TEST
echo "2️⃣ MODULE SELECTION REDIRECT TEST\n";
echo str_repeat('-', 60) . "\n";

$moduleUrl = "http://127.0.0.1:8000/t/draft/test1/admin/modules?website=15&preview=true&t=1755963892028";
echo "Testing module selection URL: {$moduleUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $moduleUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

if ($httpCode == 302 || $httpCode == 301) {
    echo "🔄 Redirect detected to: {$redirectUrl}\n";
    if (strpos($redirectUrl, '/t/draft/') === false) {
        echo "❌ CONFIRMED: Module selection redirects to ARTC instead of staying in tenant\n";
        $redirectIssues[] = "Module Selection: Redirects outside tenant to {$redirectUrl}";
    } else {
        echo "✅ Module selection stays within tenant\n";
    }
} elseif ($httpCode == 200) {
    echo "✅ Module page loads without redirect\n";
} else {
    echo "❌ Module page returns HTTP {$httpCode}\n";
}
echo "\n";

// 3. SIDEBAR CONTENT CHECK
echo "3️⃣ SIDEBAR CONTENT ANALYSIS\n";
echo str_repeat('-', 60) . "\n";

$sidebarFile = 'resources/views/admin/admin-layouts/admin-sidebar.blade.php';
if (file_exists($sidebarFile)) {
    $sidebarContent = file_get_contents($sidebarFile);
    
    echo "Checking sidebar for 'Archived Content Management'...\n";
    if (strpos($sidebarContent, 'Archived Content Management') !== false) {
        echo "❌ FOUND: 'Archived Content Management' still in sidebar\n";
        $errors[] = "Sidebar: 'Archived Content Management' needs to be removed";
    } else {
        echo "✅ 'Archived Content Management' not found in sidebar\n";
    }
    
    // Check for archived content links
    if (strpos($sidebarContent, 'Archived Content') !== false) {
        echo "⚠️  'Archived Content' found in sidebar (may need review)\n";
    }
} else {
    echo "❌ Sidebar file not found\n";
    $errors[] = "Sidebar: File not found";
}
echo "\n";

// SUMMARY
echo str_repeat('=', 80) . "\n";
echo "📊 INVESTIGATION SUMMARY\n";
echo str_repeat('=', 80) . "\n\n";

$totalIssues = count($issueUrls);
$workingIssues = array_filter($results, fn($r) => $r['working']);
$brandedIssues = array_filter($results, fn($r) => $r['branded']);

echo "STATISTICS:\n";
echo "• Total Issues Tested: {$totalIssues}\n";
echo "• Working: " . count($workingIssues) . "\n";
echo "• Properly Branded: " . count($brandedIssues) . "\n";
echo "• Redirect Issues: " . count($redirectIssues) . "\n";
echo "• Other Errors: " . count($errors) . "\n\n";

if (!empty($redirectIssues)) {
    echo "🔄 REDIRECT ISSUES:\n";
    foreach ($redirectIssues as $issue) {
        echo "   • {$issue}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS FOUND:\n";
    foreach ($errors as $error) {
        echo "   • {$error}\n";
    }
    echo "\n";
}

echo "🔧 PRIORITY FIXES NEEDED:\n";
echo "1. Fix module selection redirect to stay in tenant context\n";
echo "2. Add missing archived routes (courses, materials)\n";
echo "3. Restore Add Program and Add Package buttons\n";
echo "4. Remove 'Archived Content Management' from sidebar\n";
echo "5. Fix any registration/pending/history issues\n";

echo "\nInvestigation completed at " . date('Y-m-d H:i:s') . "\n";

?>
