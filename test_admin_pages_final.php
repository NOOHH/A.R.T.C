<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test routes
$routes = [
    'Dashboard' => '/t/draft/test1/admin-dashboard?website=15&preview=true',
    'Students' => '/t/draft/test1/admin/students?website=15&preview=true',
    'Professors' => '/t/draft/test1/admin/professors?website=15&preview=true',
    'Programs' => '/t/draft/test1/admin/programs?website=15&preview=true',
    'Directors' => '/t/draft/test1/admin/directors?website=15&preview=true',
    'Modules' => '/t/draft/test1/admin/modules?website=15&preview=true',
    'Announcements' => '/t/draft/test1/admin/announcements?website=15&preview=true',
    'Batch Enrollment' => '/t/draft/test1/admin/batches?website=15&preview=true',
    'Analytics' => '/t/draft/test1/admin/analytics?website=15&preview=true',
    'Settings' => '/t/draft/test1/admin/settings?website=15&preview=true',
    'Packages' => '/t/draft/test1/admin/packages?website=15&preview=true',
    'Quiz Generator' => '/t/draft/test1/admin/quiz-generator?website=15&preview=true',
    'Payment Pending' => '/t/draft/test1/admin-student-registration/payment/pending?website=15&preview=true',
    'Payment History' => '/t/draft/test1/admin-student-registration/payment/history?website=15&preview=true',
    'Archived Programs' => '/t/draft/test1/admin/programs/archived?website=15&preview=true'
];

echo "=== ADMIN PAGES DETAILED TEST ===\n";
echo "Testing " . count($routes) . " admin routes...\n\n";

$successCount = 0;
$results = [];

foreach ($routes as $name => $url) {
    echo "Testing {$name}...\n";
    
    try {
        $request = Illuminate\Http\Request::create($url, 'GET');
        $app->instance('request', $request);
        
        $response = $kernel->handle($request);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        
        if ($statusCode === 200) {
            // Check if it's actually a valid HTML page with expected content
            $hasDoctype = strpos($content, '<!DOCTYPE html') !== false;
            $hasTitle = strpos($content, '<title>') !== false;
            $hasHtml = strpos($content, '<html') !== false;
            $hasTestBranding = strpos($content, 'Test1') !== false || strpos($content, 'test1') !== false;
            $hasNoArtc = strpos(strtolower($content), 'artc') === false;
            
            if ($hasDoctype && $hasTitle && $hasHtml) {
                echo "✅ SUCCESS";
                if ($hasTestBranding) echo " - Has Test1 branding";
                if ($hasNoArtc) echo " - No ARTC references";
                echo "\n";
                $successCount++;
                $results[$name] = 'SUCCESS';
            } else {
                echo "⚠️  PARTIAL - Loads but incomplete HTML\n";
                $results[$name] = 'PARTIAL';
            }
        } else {
            echo "❌ FAILED - Status: {$statusCode}\n";
            $results[$name] = 'FAILED';
        }
        
    } catch (Exception $e) {
        echo "❌ EXCEPTION - " . $e->getMessage() . "\n";
        $results[$name] = 'EXCEPTION';
    }
}

echo "\n=== SUMMARY ===\n";
echo "Success Rate: {$successCount}/" . count($routes) . " (" . round(($successCount/count($routes))*100, 1) . "%)\n\n";

echo "Successful Pages:\n";
foreach ($results as $page => $status) {
    if ($status === 'SUCCESS') {
        echo "✅ {$page}\n";
    }
}

echo "\nFailed Pages:\n";
foreach ($results as $page => $status) {
    if ($status !== 'SUCCESS') {
        echo "❌ {$page} ({$status})\n";
    }
}
