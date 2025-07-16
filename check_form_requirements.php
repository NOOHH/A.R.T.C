<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FORM REQUIREMENTS TABLE STRUCTURE ===" . PHP_EOL;
    $stmt = $pdo->query('DESCRIBE form_requirements');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo $col['Field'] . ' | ' . $col['Type'] . ' | ' . $col['Null'] . ' | ' . $col['Key'] . PHP_EOL;
    }
    
    echo PHP_EOL . "=== FORM REQUIREMENTS TABLE ===" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM form_requirements LIMIT 5');
    $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($requirements)) {
        echo "No form requirements found - this is fine, system should work without them" . PHP_EOL;
    } else {
        foreach ($requirements as $req) {
            echo "Field: {$req['field_name']} | Type: {$req['field_type']} | Required: {$req['is_required']} | Program: {$req['program_type']} | Active: {$req['is_active']}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== ADMIN SETTINGS TABLE ===" . PHP_EOL;
    $stmt = $pdo->query('SELECT * FROM admin_settings ORDER BY setting_key');
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($settings)) {
        echo "No admin settings found" . PHP_EOL;
    } else {
        foreach ($settings as $setting) {
            echo "Key: {$setting['setting_key']} | Value: {$setting['setting_value']} | Type: {$setting['setting_type']}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== RECENT REGISTRATIONS ===" . PHP_EOL;
    $stmt = $pdo->query('SELECT r.*, u.email FROM registrations r LEFT JOIN users u ON r.user_id = u.user_id ORDER BY r.created_at DESC LIMIT 5');
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($registrations)) {
        echo "No registrations found" . PHP_EOL;
    } else {
        foreach ($registrations as $reg) {
            echo "ID: {$reg['registration_id']} | User: {$reg['email']} | Type: {$reg['enrollment_type']} | Status: {$reg['status']} | Created: {$reg['created_at']}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== ADMIN PACKAGES ERROR CHECK ===" . PHP_EOL;
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM packages');
    $packageCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Packages count: {$packageCount['count']}" . PHP_EOL;
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM programs');
    $programCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Programs count: {$programCount['count']}" . PHP_EOL;
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM modules');
    $moduleCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Modules count: {$moduleCount['count']}" . PHP_EOL;
    
    echo PHP_EOL . "=== CHECKING ADMIN PACKAGES CONTROLLER VARIABLES ===" . PHP_EOL;
    
    // Check what data is actually needed by admin packages
    $stmt = $pdo->query('SELECT package_id, package_name, package_type, program_id FROM packages LIMIT 3');
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample packages:" . PHP_EOL;
    foreach ($packages as $package) {
        echo "  ID: {$package['package_id']} | Name: {$package['package_name']} | Type: {$package['package_type']} | Program: {$package['program_id']}" . PHP_EOL;
    }
    
    $stmt = $pdo->query('SELECT program_id, program_name FROM programs LIMIT 3');
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample programs:" . PHP_EOL;
    foreach ($programs as $program) {
        echo "  ID: {$program['program_id']} | Name: {$program['program_name']}" . PHP_EOL;
    }
    
    $stmt = $pdo->query('SELECT modules_id, module_name, program_id FROM modules LIMIT 3');
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample modules:" . PHP_EOL;
    foreach ($modules as $module) {
        echo "  ID: {$module['modules_id']} | Name: {$module['module_name']} | Program: {$module['program_id']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
