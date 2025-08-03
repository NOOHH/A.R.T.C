<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Director;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "=== DIRECTOR ARCHIVE ROUTE DEBUGGING ===\n";

try {
    // Test 1: Check if director exists for testing
    echo "\n1. TESTING DIRECTOR EXISTENCE\n";
    $director = Director::first();
    if ($director) {
        echo "   ✓ Found director with ID: " . $director->directors_id . "\n";
        echo "   Director name: " . $director->directors_name . "\n";
        echo "   Director email: " . $director->directors_email . "\n";
    } else {
        echo "   ✗ No directors found in database\n";
        return;
    }
    
    // Test 2: Check route existence
    echo "\n2. TESTING ROUTE CONFIGURATION\n";
    $routes = Route::getRoutes();
    $archiveRouteFound = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri, 'admin/directors') && in_array('PATCH', $route->methods)) {
            echo "   Found PATCH route: " . $route->uri . "\n";
            echo "   Methods: " . implode(', ', $route->methods) . "\n";
            echo "   Name: " . ($route->getName() ?? 'No name') . "\n";
            if (str_contains($route->uri, 'archive')) {
                $archiveRouteFound = true;
                echo "   ✓ This is the archive route\n";
            }
            echo "\n";
        }
    }
    
    if ($archiveRouteFound) {
        echo "   ✓ Archive route found and configured correctly\n";
    } else {
        echo "   ✗ Archive route not found\n";
    }
    
    // Test 3: Test route generation
    echo "\n3. TESTING ROUTE GENERATION\n";
    try {
        $archiveUrl = route('admin.directors.archive', $director);
        echo "   ✓ Archive route URL: " . $archiveUrl . "\n";
    } catch (Exception $e) {
        echo "   ✗ Failed to generate archive route: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test route matching
    echo "\n4. TESTING ROUTE MATCHING\n";
    $testUrls = [
        "/admin/directors",
        "/admin/directors/{$director->directors_id}/archive",
        "/admin/directors/{$director->directors_id}",
    ];
    
    foreach ($testUrls as $url) {
        echo "   Testing URL: $url\n";
        try {
            $request = Request::create($url, 'PATCH');
            $matchedRoute = Route::getRoutes()->match($request);
            echo "   ✓ Matched route: " . $matchedRoute->uri . "\n";
            echo "   ✓ Controller: " . $matchedRoute->getActionName() . "\n";
        } catch (Exception $e) {
            echo "   ✗ No route matched: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // Test 5: Check if there are conflicting routes
    echo "\n5. CHECKING FOR ROUTE CONFLICTS\n";
    $adminDirectorRoutes = [];
    foreach ($routes as $route) {
        if (str_contains($route->uri, 'admin/directors')) {
            $key = $route->uri . ' [' . implode(',', $route->methods) . ']';
            $adminDirectorRoutes[] = $key;
        }
    }
    
    sort($adminDirectorRoutes);
    echo "   All admin/directors routes:\n";
    foreach ($adminDirectorRoutes as $routeInfo) {
        echo "   - $routeInfo\n";
    }
    
    // Test 6: Simulate the actual request that's failing
    echo "\n6. SIMULATING FAILING REQUEST\n";
    echo "   Based on the error, it seems like the request is going to /admin/directors with PATCH\n";
    echo "   Let's check what routes handle /admin/directors:\n";
    
    foreach ($routes as $route) {
        if ($route->uri === 'admin/directors') {
            echo "   Route: " . $route->uri . "\n";
            echo "   Methods: " . implode(', ', $route->methods) . "\n";
            echo "   Action: " . $route->getActionName() . "\n";
            echo "\n";
        }
    }
    
    echo "\n=== ANALYSIS ===\n";
    echo "The error suggests that a PATCH request is being sent to '/admin/directors'\n";
    echo "instead of '/admin/directors/{director}/archive'.\n\n";
    echo "This could be caused by:\n";
    echo "1. JavaScript not setting the form action correctly\n";
    echo "2. Form action being empty or malformed\n";
    echo "3. Missing director ID in the JavaScript\n";
    echo "4. Route model binding issue\n\n";
    
    echo "Recommended checks:\n";
    echo "1. Verify the director ID is being passed correctly to the modal\n";
    echo "2. Check browser network tab for the actual URL being requested\n";
    echo "3. Add console.log to the JavaScript to debug the form action\n";
    
} catch (Exception $e) {
    echo "Error during debugging: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
