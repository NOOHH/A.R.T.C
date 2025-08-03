<?php
// Test script to verify route fixes
echo "=== ROUTE TEST ===\n\n";

// Test if we can generate the home route URL
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Test generating the home route
    $homeUrl = route('home');
    echo "✓ Home route generates: $homeUrl\n";
    
    // Test some student routes
    $studentRoutes = [
        'student.dashboard',
        'student.enrolled-courses',
        'student.analytics',
        'student.profile'
    ];
    
    foreach ($studentRoutes as $routeName) {
        try {
            $url = route($routeName);
            echo "✓ Route '$routeName' generates: $url\n";
        } catch (Exception $e) {
            echo "✗ Route '$routeName' error: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
