<?php
// Test professor sidebar tenant-aware routing in preview mode

// Test tenant slug detection
$test_cases = [
    // Case 1: Preview route with tenant parameter
    'preview_route' => '/t/draft/test1/professor/dashboard',
    
    // Case 2: Regular route without tenant
    'regular_route' => '/professor/dashboard'
];

echo "Testing Professor Sidebar Tenant Detection:\n\n";

foreach ($test_cases as $case_name => $test_route) {
    echo "=== Testing $case_name ===\n";
    echo "Route: $test_route\n";
    
    // Simulate route parsing
    $tenantSlug = null;
    if (preg_match('#^/t/draft/([^/]+)/#', $test_route, $matches)) {
        $tenantSlug = $matches[1];
    }
    
    echo "Detected tenant: " . ($tenantSlug ?: 'none') . "\n";
    
    // Simulate route building logic from sidebar
    $routePrefix = $tenantSlug ? 'tenant.preview.' : '';
    $routeParams = $tenantSlug ? ['tenant' => $tenantSlug] : [];
    
    echo "Route prefix: '$routePrefix'\n";
    echo "Route params: " . json_encode($routeParams) . "\n";
    
    // Sample route constructions
    $dashboardRoute = $tenantSlug ? $routePrefix . 'professor.dashboard' : 'professor.dashboard';
    $meetingsRoute = $tenantSlug ? $routePrefix . 'professor.meetings' : 'professor.meetings';
    $modulesRoute = $tenantSlug ? $routePrefix . 'professor.modules' : 'professor.modules.index';
    
    echo "Dashboard route name: '$dashboardRoute'\n";
    echo "Meetings route name: '$meetingsRoute'\n";
    echo "Modules route name: '$modulesRoute'\n";
    echo "\n";
}

echo "Test completed successfully!\n";
?>
