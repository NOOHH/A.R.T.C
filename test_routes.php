<?php

// Test route generation functionality
echo "=== ROUTE GENERATION TEST ===\n\n";

// Simulate the route helper functionality
function testRoute($routeName, $params = []) {
    $routes = [
        'tenant.student.dashboard' => '/t/{tenant}/student/dashboard',
        'tenant.student.calendar' => '/t/{tenant}/student/calendar',
        'tenant.student.enrolled-courses' => '/t/{tenant}/student/enrolled-courses',
        'tenant.student.meetings' => '/t/{tenant}/student/meetings',
        'tenant.student.settings' => '/t/{tenant}/student/settings',
        'student.dashboard' => '/student/dashboard',
        'student.calendar' => '/student/calendar',
        'student.enrolled-courses' => '/student/enrolled-courses',
        'student.meetings' => '/student/meetings',
        'student.settings' => '/student/settings',
    ];
    
    if (!isset($routes[$routeName])) {
        return "Route not found: {$routeName}";
    }
    
    $route = $routes[$routeName];
    
    // Replace parameters
    foreach ($params as $key => $value) {
        $route = str_replace('{' . $key . '}', $value, $route);
    }
    
    return $route;
}

// Test tenant routes
echo "Testing tenant route generation:\n";
$tenantRoutes = [
    'tenant.student.dashboard' => ['tenant' => 'test1'],
    'tenant.student.calendar' => ['tenant' => 'test1'],
    'tenant.student.enrolled-courses' => ['tenant' => 'test1'],
    'tenant.student.meetings' => ['tenant' => 'test1'],
    'tenant.student.settings' => ['tenant' => 'test1'],
];

foreach ($tenantRoutes as $routeName => $params) {
    $generatedRoute = testRoute($routeName, $params);
    echo "  {$routeName} => {$generatedRoute}\n";
}

echo "\nTesting non-tenant routes:\n";
$nonTenantRoutes = [
    'student.dashboard' => [],
    'student.calendar' => [],
    'student.enrolled-courses' => [],
    'student.meetings' => [],
    'student.settings' => [],
];

foreach ($nonTenantRoutes as $routeName => $params) {
    $generatedRoute = testRoute($routeName, $params);
    echo "  {$routeName} => {$generatedRoute}\n";
}

echo "\n=== ROUTE GENERATION TEST COMPLETE ===\n";
