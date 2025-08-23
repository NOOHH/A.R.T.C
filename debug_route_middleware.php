<?php
// Debug middleware stack for tenant admin routes

use Illuminate\Support\Facades\Route;

// Test different route patterns to see which middleware is applied
$testRoutes = [
    '/t/draft/smartprep/admin/archived',
    '/t/draft/smartprep/admin/certificates', 
    '/t/draft/smartprep/test', // Test non-admin path
    '/admin/archived', // Regular admin path
];

echo "=== ROUTE MIDDLEWARE ANALYSIS ===\n\n";

foreach ($testRoutes as $testPath) {
    echo "Testing path: $testPath\n";
    
    // Find the route that matches this path
    $route = null;
    foreach (Route::getRoutes() as $routeItem) {
        if ($routeItem->matches(request()->create($testPath))) {
            $route = $routeItem;
            break;
        }
    }
    
    if ($route) {
        echo "Route found: {$route->getName()}\n";
        echo "Route URI: {$route->uri()}\n";
        echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n";
        echo "Controller: {$route->getActionName()}\n";
    } else {
        echo "No route found for this path\n";
    }
    
    echo "---\n\n";
}
?>
