<?php
echo "🔧 COMPREHENSIVE ENROLLMENT BUTTON TENANT-AWARENESS FIX\n";
echo "=====================================================\n\n";

// 1. ANALYZE CURRENT ISSUE
echo "1️⃣ ANALYZING CURRENT ENROLLMENT BUTTON ISSUES:\n";
echo "----------------------------------------------\n";

$enrollmentView = 'resources/views/welcome/enrollment.blade.php';
if (file_exists($enrollmentView)) {
    $content = file_get_contents($enrollmentView);
    
    echo "✅ Found enrollment view file\n";
    
    // Check for hardcoded URLs
    $hardcodedPatterns = [
        'localhost URLs' => '/127\.0\.0\.1:8000/',
        'Direct enrollment paths' => '/(?<!")\/enrollment\/(?:full|modular)(?!")/',
        'JavaScript redirects' => '/window\.location\.href\s*=\s*[\'"]\/enrollment\//',
        'Data URL attributes' => '/data-url\s*=\s*[\'"]\/enrollment\//'
    ];
    
    foreach ($hardcodedPatterns as $name => $pattern) {
        $matches = preg_match_all($pattern, $content);
        echo ($matches > 0 ? "❌" : "✅") . " $name: $matches occurrences\n";
    }
    
} else {
    echo "❌ Enrollment view file not found\n";
    exit(1);
}

echo "\n2️⃣ CHECKING TENANT ROUTE STRUCTURE:\n";
echo "-----------------------------------\n";

// Check tenant routes
$tenantRoutes = 'routes/tenant.php';
if (file_exists($tenantRoutes)) {
    $tenantContent = file_get_contents($tenantRoutes);
    echo "✅ Tenant routes file exists\n";
    
    $requiredRoutes = [
        'enrollment.full' => 'enrollment/full',
        'enrollment.modular' => 'enrollment/modular',
        'enrollment.modular.submit' => 'enrollment/modular/submit'
    ];
    
    foreach ($requiredRoutes as $routeName => $routePath) {
        if (strpos($tenantContent, $routePath) !== false) {
            echo "✅ $routeName route exists\n";
        } else {
            echo "❌ $routeName route missing\n";
        }
    }
} else {
    echo "❌ Tenant routes file not found\n";
}

// Check web routes for tenant preview structure
$webRoutes = 'routes/web.php';
if (file_exists($webRoutes)) {
    $webContent = file_get_contents($webRoutes);
    
    $tenantPatterns = [
        'Draft tenant enrollment' => '/draft\/\{tenant\}\/enrollment/',
        'Active tenant enrollment' => '/\{tenant\}\/enrollment/',
        'PreviewController enrollment' => '/PreviewController.*enrollment/'
    ];
    
    foreach ($tenantPatterns as $name => $pattern) {
        $matches = preg_match($pattern, $webContent);
        echo ($matches ? "✅" : "❌") . " $name: " . ($matches ? "Found" : "Not found") . "\n";
    }
} else {
    echo "❌ Web routes file not found\n";
}

echo "\n3️⃣ CHECKING CURRENT TENANT CONTEXT AVAILABILITY:\n";
echo "------------------------------------------------\n";

// Check if we can determine tenant context
$potentialTenantSources = [
    'Request URL' => 'Check if current URL contains tenant info',
    'Session data' => 'Check for tenant in session',
    'Route parameters' => 'Check route for tenant parameter',
    'Helper functions' => 'Check for tenant helper functions'
];

foreach ($potentialTenantSources as $source => $description) {
    echo "🔍 $source: $description\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
echo "NEXT: Creating comprehensive fix with testing...\n";
?>
