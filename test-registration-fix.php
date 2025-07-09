<?php
// Test script to verify registration functionality
require_once 'vendor/autoload.php';

echo "Testing Registration System...\n\n";

// Test 1: Check if FormRequirement model uses 'full'
echo "1. Testing FormRequirement model...\n";
$formRequirements = App\Models\FormRequirement::active()->forProgram('full')->get();
echo "   Found " . $formRequirements->count() . " active requirements for 'full' program type\n";

// Test 2: Check if routes exist
echo "\n2. Testing routes...\n";
$router = app('router');
$routes = $router->getRoutes();
$studentRoutes = 0;
foreach ($routes as $route) {
    if (str_contains($route->getName() ?? '', 'student')) {
        $studentRoutes++;
    }
}
echo "   Found $studentRoutes student-related routes\n";

// Test 3: Check database connection
echo "\n3. Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "   Database connection: OK\n";
} catch (\Exception $e) {
    echo "   Database connection: FAILED - " . $e->getMessage() . "\n";
}

// Test 4: Check CSS file
echo "\n4. Testing CSS file...\n";
$cssPath = public_path('css/ENROLLMENT/Full_Enrollment.css');
if (file_exists($cssPath)) {
    echo "   CSS file exists: OK\n";
    echo "   CSS file size: " . filesize($cssPath) . " bytes\n";
} else {
    echo "   CSS file: NOT FOUND\n";
}

echo "\nTest completed!\n";
?>
