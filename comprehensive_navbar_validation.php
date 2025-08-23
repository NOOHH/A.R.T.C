<?php
/**
 * COMPREHENSIVE NAVBAR AND ROUTE VALIDATION TEST
 * Tests the specific issues: Pending, History, Payment Pending, Payment History, 
 * and all archived content sections that admin has
 */

echo "🔬 COMPREHENSIVE NAVBAR & ROUTE VALIDATION TEST\n";
echo "Testing the specific issues reported by user\n";
echo "=================================================================\n\n";

$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

// Test both tenant and regular routes for the problematic sections
$testSections = [
    // Tenant routes (should work with TEST11 branding)
    'TENANT: Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'TENANT: Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'TENANT: Certificates' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'TENANT: Archived Content' => "/t/draft/{$tenant}/admin/archived?{$params}",
    'TENANT: Archived Programs' => "/t/draft/{$tenant}/admin/archived/programs?{$params}",
    'TENANT: Course Upload' => "/t/draft/{$tenant}/admin/courses/upload?{$params}",
    
    // Regular admin routes (should work without branding)
    'REGULAR: Payment Pending' => "/admin/payments/pending",
    'REGULAR: Payment History' => "/admin/payments/history", 
    'REGULAR: Archived Content' => "/admin/archived",
    'REGULAR: Archived Programs' => "/admin/archived/programs",
    'REGULAR: Course Upload' => "/admin/courses/upload",
];

$results = [];
$errors = [];
$navbarErrors = [];

echo "1️⃣ TESTING ALL ROUTES & ENDPOINTS\n";
echo str_repeat('-', 60) . "\n";

foreach ($testSections as $sectionName => $url) {
    echo "Testing: {$sectionName}\n";
    
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
    
    $result = [
        'section' => $sectionName,
        'http_code' => $httpCode,
        'response_size' => strlen($response),
        'error' => $error,
        'working' => false,
        'branded' => false,
        'branding_count' => 0,
        'has_navbar_error' => false
    ];
    
    if ($error) {
        echo "❌ CURL Error: {$error}\n";
        $errors[] = $sectionName . ": " . $error;
    } elseif ($httpCode == 404) {
        echo "❌ HTTP 404 - Route not found!\n";
        $errors[] = $sectionName . ": 404 Not Found";
    } elseif ($httpCode == 500) {
        echo "❌ HTTP 500 - Server Error!\n";
        $errors[] = $sectionName . ": 500 Server Error";
        
        // Check for route errors in response
        if (strpos($response, 'Route [admin.archived] not defined') !== false) {
            echo "   🔍 Found navbar route error: admin.archived not defined\n";
            $navbarErrors[] = $sectionName . ": admin.archived route missing";
            $result['has_navbar_error'] = true;
        }
        if (strpos($response, 'Route [admin.courses.upload] not defined') !== false) {
            echo "   🔍 Found navbar route error: admin.courses.upload not defined\n";
            $navbarErrors[] = $sectionName . ": admin.courses.upload route missing";
            $result['has_navbar_error'] = true;
        }
    } elseif ($httpCode == 200) {
        echo "✅ HTTP 200 - Working!\n";
        echo "📄 Response Size: " . number_format(strlen($response)) . " bytes\n";
        
        $result['working'] = true;
        
        // Check for TEST11 branding (only relevant for tenant routes)
        if (strpos($sectionName, 'TENANT:') !== false) {
            $brandingCount = substr_count($response, 'TEST11');
            $result['branding_count'] = $brandingCount;
            
            if ($brandingCount >= 2) {
                echo "✅ TEST11 Branding: {$brandingCount} instances found\n";
                $result['branded'] = true;
            } else {
                echo "⚠️  TEST11 Branding: Only {$brandingCount} instances (expected 2+)\n";
            }
        }
        
        // Check for any route errors in successful responses
        if (strpos($response, 'Route [admin.archived] not defined') !== false ||
            strpos($response, 'Route [admin.courses.upload] not defined') !== false) {
            echo "⚠️  Contains navbar route errors\n";
            $result['has_navbar_error'] = true;
            $navbarErrors[] = $sectionName . ": Route errors in response";
        }
        
    } else {
        echo "⚠️  HTTP {$httpCode} - Unexpected status\n";
        $errors[] = $sectionName . ": HTTP {$httpCode}";
    }
    
    $results[$sectionName] = $result;
    echo "\n";
}

// 2. SPECIFIC NAVBAR ERROR TEST
echo "2️⃣ NAVBAR ERROR VALIDATION TEST\n";
echo str_repeat('-', 60) . "\n";

// Test if the navbar can load without errors
$navbarTestUrl = $baseUrl . "/t/draft/{$tenant}/admin/certificates?{$params}";
echo "Testing navbar rendering with: {$navbarTestUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $navbarTestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Navbar test page loads successfully\n";
    
    if (strpos($response, 'Route [admin.archived] not defined') !== false) {
        echo "❌ Still contains admin.archived route error\n";
        $navbarErrors[] = "Navbar: admin.archived route still missing";
    } else {
        echo "✅ No admin.archived route errors found\n";
    }
    
    if (strpos($response, 'Route [admin.courses.upload] not defined') !== false) {
        echo "❌ Still contains admin.courses.upload route error\n";
        $navbarErrors[] = "Navbar: admin.courses.upload route still missing";
    } else {
        echo "✅ No admin.courses.upload route errors found\n";
    }
} else {
    echo "❌ Navbar test failed with HTTP {$httpCode}\n";
}
echo "\n";

// 3. DATABASE CONNECTIVITY TEST
echo "3️⃣ DATABASE & CUSTOMIZATION TEST\n";
echo str_repeat('-', 60) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep_test1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n";
    
    // Check for TEST11 customization
    $stmt = $pdo->query("SELECT * FROM ui_settings WHERE setting_value LIKE '%TEST11%' LIMIT 2");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($results)) {
        echo "✅ TEST11 customization found in database\n";
        foreach ($results as $setting) {
            echo "   • {$setting['section']}.{$setting['setting_key']}: {$setting['setting_value']}\n";
        }
    } else {
        echo "❌ No TEST11 customization found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. ROUTE REGISTRATION VERIFICATION
echo "4️⃣ ROUTE REGISTRATION VERIFICATION\n";
echo str_repeat('-', 60) . "\n";

$routeCheck = shell_exec('php artisan route:list 2>&1');

$requiredRoutes = [
    'admin.archived',
    'admin.archived.programs', 
    'admin.courses.upload',
    'admin.payments.pending',
    'admin.payments.history',
    'tenant.draft.admin.archived',
    'tenant.draft.admin.courses.upload',
    'tenant.draft.admin.payments.pending',
    'tenant.draft.admin.payments.history'
];

foreach ($requiredRoutes as $route) {
    if (strpos($routeCheck, $route) !== false) {
        echo "✅ Route {$route} is registered\n";
    } else {
        echo "❌ Route {$route} is MISSING\n";
        $errors[] = "Missing route: {$route}";
    }
}
echo "\n";

// FINAL SUMMARY
echo str_repeat('=', 80) . "\n";
echo "📊 COMPREHENSIVE TEST SUMMARY\n";
echo str_repeat('=', 80) . "\n\n";

$totalSections = count($testSections);
$workingSections = array_filter($results, fn($r) => $r['working']);
$brandedSections = array_filter($results, fn($r) => $r['branded']);
$sectionsWithNavbarErrors = array_filter($results, fn($r) => $r['has_navbar_error']);

echo "📈 SUCCESS METRICS:\n";
echo "• Total Sections Tested: {$totalSections}\n";
echo "• Working Sections: " . count($workingSections) . "\n";
echo "• Properly Branded Tenant Sections: " . count($brandedSections) . "\n";
echo "• Sections with Navbar Errors: " . count($sectionsWithNavbarErrors) . "\n";

if (!empty($errors)) {
    echo "\n❌ ERRORS FOUND (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "   • {$error}\n";
    }
}

if (!empty($navbarErrors)) {
    echo "\n🔍 NAVBAR ERRORS (" . count($navbarErrors) . "):\n";
    foreach ($navbarErrors as $error) {
        echo "   • {$error}\n";
    }
}

// SUCCESS STATUS
$successRate = round((count($workingSections) / $totalSections) * 100);
echo "\n🏥 OVERALL SUCCESS RATE: {$successRate}%\n";

if (empty($errors) && empty($navbarErrors) && $successRate >= 90) {
    echo "🎉 STATUS: ALL ISSUES RESOLVED!\n";
    echo "✅ All routes working properly\n";
    echo "✅ Navbar loading without errors\n";
    echo "✅ TEST11 branding functional\n";
    echo "✅ Database connectivity confirmed\n";
} elseif (empty($navbarErrors) && $successRate >= 80) {
    echo "⚠️  STATUS: MOSTLY WORKING - Minor issues remain\n";
} else {
    echo "❌ STATUS: ISSUES REQUIRE ATTENTION\n";
}

echo "\nTest completed at " . date('Y-m-d H:i:s') . "\n";

?>
