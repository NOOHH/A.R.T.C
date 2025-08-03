<?php
require_once 'vendor/autoload.php';

// Test script to debug module restore functionality
echo "=== Debug Module Restore Issues ===\n\n";

// Test database connection
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Check if modules table exists and has archived modules
$stmt = $pdo->query("SELECT COUNT(*) as total, SUM(is_archived) as archived FROM modules");
$counts = $stmt->fetch();
echo "✓ Total modules: {$counts['total']}, Archived: {$counts['archived']}\n";

// Check if the toggle_archive route exists in web.php
$webRoutes = file_get_contents('routes/web.php');
if (strpos($webRoutes, 'toggle-archive') !== false) {
    echo "✓ toggle-archive route found in web.php\n";
} else {
    echo "✗ toggle-archive route NOT found in web.php\n";
}

// Check AdminModuleController for toggleArchive method
$controllerFile = 'app/Http/Controllers/AdminModuleController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    if (strpos($controllerContent, 'function toggleArchive') !== false) {
        echo "✓ toggleArchive method found in AdminModuleController\n";
    } else {
        echo "✗ toggleArchive method NOT found in AdminModuleController\n";
    }
} else {
    echo "✗ AdminModuleController.php not found\n";
}

// Test route availability
echo "\n=== Testing Route Availability ===\n";
$routes = [
    'admin.students.export',
    'admin.modules.toggle-archive',
    'admin.enrollments.index'
];

foreach ($routes as $route) {
    if (strpos($webRoutes, $route) !== false) {
        echo "✓ Route '$route' found\n";
    } else {
        echo "✗ Route '$route' NOT found\n";
    }
}

// Check for CSV export functionality
echo "\n=== CSV Export Check ===\n";
$exportController = 'app/Http/Controllers/AdminStudentListController.php';
if (file_exists($exportController)) {
    $exportContent = file_get_contents($exportController);
    if (strpos($exportContent, 'function export') !== false) {
        echo "✓ Export function found in AdminStudentListController\n";
    } else {
        echo "✗ Export function NOT found in AdminStudentListController\n";
    }
} else {
    echo "✗ AdminStudentListController.php not found\n";
}

echo "\n=== Debug Complete ===\n";
