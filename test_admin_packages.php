<?php
require_once 'vendor/autoload.php';

// Create a simple test to replicate the AdminPackageController::index method
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING ADMIN PACKAGES CONTROLLER LOGIC ===" . PHP_EOL;
    
    // Replicate the controller logic
    
    // 1. Load packages with enrollments count
    $stmt = $pdo->query('
        SELECT p.*, 
               COUNT(e.enrollment_id) as enrollments_count
        FROM packages p
        LEFT JOIN enrollments e ON p.package_id = e.package_id
        GROUP BY p.package_id
        ORDER BY p.created_at DESC
    ');
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Packages loaded: " . count($packages) . PHP_EOL;
    
    // 2. Load programs for dropdown
    $stmt = $pdo->query('SELECT * FROM programs ORDER BY program_name ASC');
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Programs loaded: " . count($programs) . PHP_EOL;
    
    // 3. Load modules for dynamic selection
    $stmt = $pdo->query('SELECT * FROM modules ORDER BY module_name ASC');
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Modules loaded: " . count($modules) . PHP_EOL;
    
    // 4. Calculate analytics
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM packages');
    $totalPackages = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM registrations');
    $totalEnrollments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // No amount_paid column exists in registrations table
    $totalRevenue = 0;
    
    $stmt = $pdo->query('
        SELECT p.*, COUNT(e.enrollment_id) as enrollments_count
        FROM packages p
        LEFT JOIN enrollments e ON p.package_id = e.package_id
        GROUP BY p.package_id
        ORDER BY enrollments_count DESC
        LIMIT 1
    ');
    $popularPackage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Analytics calculated:" . PHP_EOL;
    echo "  Total Packages: $totalPackages" . PHP_EOL;
    echo "  Total Enrollments: $totalEnrollments" . PHP_EOL;
    echo "  Total Revenue: $totalRevenue" . PHP_EOL;
    echo "  Popular Package: " . ($popularPackage['package_name'] ?? 'None') . PHP_EOL;
    
    // 5. Check if all variables are available
    $analytics = [
        'totalPackages' => $totalPackages,
        'totalEnrollments' => $totalEnrollments,
        'totalRevenue' => $totalRevenue,
        'popularPackage' => $popularPackage
    ];
    
    echo PHP_EOL . "=== VARIABLES THAT SHOULD BE PASSED TO VIEW ===" . PHP_EOL;
    echo "packages: " . (isset($packages) ? count($packages) : 'NULL') . PHP_EOL;
    echo "programs: " . (isset($programs) ? count($programs) : 'NULL') . PHP_EOL;
    echo "modules: " . (isset($modules) ? count($modules) : 'NULL') . PHP_EOL;
    echo "analytics: " . (isset($analytics) ? 'SET' : 'NULL') . PHP_EOL;
    
    echo PHP_EOL . "=== SAMPLE PROGRAM DATA ===" . PHP_EOL;
    foreach ($programs as $program) {
        echo "Program ID: {$program['program_id']} | Name: {$program['program_name']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "ALL VARIABLES AVAILABLE - CONTROLLER LOGIC SHOULD WORK" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>
