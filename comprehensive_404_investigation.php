<?php
/**
 * Comprehensive 404 Investigation and Testing Script
 * Tests all reported problematic sections and validates customization
 */

echo "🔍 COMPREHENSIVE 404 INVESTIGATION - " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n\n";

// Base URL for testing
$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

// List of all problematic sections to test
$testSections = [
    // Payment sections (previously working)
    'Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'FAQ Management' => "/t/draft/{$tenant}/admin/faq?{$params}",
    'Assignment Submissions' => "/t/draft/{$tenant}/admin/submissions?{$params}",
    
    // Certificate and Archived Content sections
    'Certificates' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'Certificate Management' => "/t/draft/{$tenant}/admin/certificates/manage?{$params}",
    'Archived Content' => "/t/draft/{$tenant}/admin/archived?{$params}",
    'Archived Programs' => "/t/draft/{$tenant}/admin/archived/programs?{$params}",
    'Course Content Upload' => "/t/draft/{$tenant}/admin/courses/upload?{$params}",
    'Course Content Management' => "/t/draft/{$tenant}/admin/courses/content?{$params}",
    
    // Additional admin sections that might be affected
    'Student Management' => "/t/draft/{$tenant}/admin/students?{$params}",
    'Professor Management' => "/t/draft/{$tenant}/admin/professors?{$params}",
    'Program Management' => "/t/draft/{$tenant}/admin/programs?{$params}",
    'Settings' => "/t/draft/{$tenant}/admin/settings?{$params}",
    
    // Regular non-tenant routes for comparison
    'Regular Payment Pending' => "/admin/payments/pending",
    'Regular Payment History' => "/admin/payments/history",
    'Regular Certificates' => "/admin/certificates",
    'Regular Archived' => "/admin/archived",
    'Regular Course Upload' => "/admin/courses/upload"
];

$results = [];
$errors = [];
$workingPages = [];
$notFoundPages = [];
$brandedPages = [];
$unbrandedPages = [];

echo "Testing all sections for 404 errors and customization...\n\n";

foreach ($testSections as $sectionName => $url) {
    echo "Testing: {$sectionName}\n";
    echo str_repeat('-', 60) . "\n";
    
    $fullUrl = $baseUrl . $url;
    echo "URL: {$fullUrl}\n";
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'temp_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'temp_cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result = [
        'section' => $sectionName,
        'url' => $url,
        'http_code' => $httpCode,
        'response_size' => strlen($response),
        'error' => $error,
        'working' => false,
        'branded' => false,
        'branding_count' => 0
    ];
    
    if ($error) {
        echo "❌ CURL Error: {$error}\n";
        $errors[] = $sectionName . ": " . $error;
    } elseif ($httpCode == 404) {
        echo "❌ HTTP Status: {$httpCode} (NOT FOUND)\n";
        $notFoundPages[] = $sectionName;
    } elseif ($httpCode == 200) {
        echo "✅ HTTP Status: {$httpCode} (Success)\n";
        echo "📄 Response Size: " . number_format(strlen($response)) . " bytes\n";
        
        $result['working'] = true;
        $workingPages[] = $sectionName;
        
        // Check for TEST11 branding
        $brandingCount = substr_count($response, 'TEST11');
        $result['branding_count'] = $brandingCount;
        
        if ($brandingCount > 0) {
            echo "🎨 TEST11 Branding Instances: {$brandingCount}\n";
            echo "✅ Tenant customization applied\n";
            $result['branded'] = true;
            $brandedPages[] = $sectionName;
        } else {
            echo "❌ No TEST11 branding found\n";
            echo "❌ Tenant customization NOT applied\n";
            $unbrandedPages[] = $sectionName;
        }
        
        // Check for specific error messages
        if (strpos($response, '404') !== false || strpos($response, 'Not Found') !== false) {
            echo "⚠️  Page contains 404/Not Found content\n";
        }
        
        if (strpos($response, 'Route not defined') !== false) {
            echo "⚠️  Route definition missing\n";
        }
        
    } else {
        echo "⚠️  HTTP Status: {$httpCode}\n";
        echo "📄 Response Size: " . number_format(strlen($response)) . " bytes\n";
    }
    
    $results[$sectionName] = $result;
    echo "\n";
}

// Summary Report
echo "\n" . str_repeat('=', 80) . "\n";
echo "COMPREHENSIVE SUMMARY REPORT\n";
echo str_repeat('=', 80) . "\n\n";

echo "📊 STATISTICS:\n";
echo "• Total Sections Tested: " . count($testSections) . "\n";
echo "• Working Pages (200): " . count($workingPages) . "\n";
echo "• 404 Not Found: " . count($notFoundPages) . "\n";
echo "• Branded Pages: " . count($brandedPages) . "\n";
echo "• Unbranded Pages: " . count($unbrandedPages) . "\n";
echo "• Errors: " . count($errors) . "\n\n";

if (!empty($notFoundPages)) {
    echo "❌ 404 NOT FOUND PAGES:\n";
    foreach ($notFoundPages as $page) {
        echo "   • {$page}\n";
    }
    echo "\n";
}

if (!empty($unbrandedPages)) {
    echo "❌ WORKING BUT UNBRANDED PAGES:\n";
    foreach ($unbrandedPages as $page) {
        echo "   • {$page}\n";
    }
    echo "\n";
}

if (!empty($brandedPages)) {
    echo "✅ WORKING AND BRANDED PAGES:\n";
    foreach ($brandedPages as $page) {
        echo "   • {$page}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "🚨 ERRORS ENCOUNTERED:\n";
    foreach ($errors as $error) {
        echo "   • {$error}\n";
    }
    echo "\n";
}

// Detailed Results
echo "📋 DETAILED RESULTS:\n";
echo str_repeat('-', 80) . "\n";
foreach ($results as $section => $result) {
    $status = $result['working'] ? '✅ WORKING' : '❌ BROKEN';
    $branding = $result['branded'] ? '✅ BRANDED' : '❌ NO BRAND';
    $size = number_format($result['response_size']);
    
    echo sprintf("%-30s %s %s (Size: %s bytes, Instances: %d)\n", 
        $section . ':', $status, $branding, $size, $result['branding_count']);
}

echo "\nTest completed at " . date('Y-m-d H:i:s') . "\n";

// Generate action plan
echo "\n" . str_repeat('=', 80) . "\n";
echo "🔧 ACTION PLAN FOR FIXES\n";
echo str_repeat('=', 80) . "\n";

if (!empty($notFoundPages)) {
    echo "\n1. FIX 404 ROUTES (HIGH PRIORITY):\n";
    echo "   • Add missing tenant preview routes in routes/web.php\n";
    echo "   • Implement corresponding controller methods\n";
    echo "   • Update admin sidebar links\n";
}

if (!empty($unbrandedPages)) {
    echo "\n2. FIX MISSING BRANDING (MEDIUM PRIORITY):\n";
    echo "   • Apply AdminPreviewCustomization trait\n";
    echo "   • Ensure tenant context is loaded\n";
    echo "   • Add mock data for preview mode\n";
}

echo "\n3. VERIFICATION STEPS:\n";
echo "   • Clear route cache: php artisan route:clear\n";
echo "   • Check route list: php artisan route:list | findstr draft\n";
echo "   • Test database connections\n";
echo "   • Validate JavaScript integration\n";
echo "   • Run comprehensive tests\n";

?>
