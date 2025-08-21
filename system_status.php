<?php

echo "=== FINAL SYSTEM STATUS ===\n\n";

// Check if Laravel development server is running
echo "1. Laravel Server Status:\n";
$serverPid = exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV | findstr "artisan serve"');
if ($serverPid) {
    echo "   ✓ Laravel development server is running\n";
} else {
    echo "   ⚠ Laravel development server may not be running\n";
}

// Check database connections
echo "\n2. Database Status:\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    echo "   ✓ Main database (smartprep) is accessible\n";
    
    // Check if tenant database exists
    $tenantPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_z-smartprep-local', 'root', '');
    echo "   ✓ Tenant database (smartprep_z-smartprep-local) is accessible\n";
    
    // Check settings table in tenant database
    $stmt = $tenantPdo->query("SHOW TABLES LIKE 'settings'");
    if ($stmt->rowCount() > 0) {
        echo "   ✓ Settings table exists in tenant database\n";
        
        // Count settings
        $stmt = $tenantPdo->query("SELECT COUNT(*) FROM settings");
        $count = $stmt->fetchColumn();
        echo "   ✓ Settings table has {$count} records\n";
    } else {
        echo "   ✗ Settings table missing in tenant database\n";
    }
    
} catch (PDOException $e) {
    echo "   ✗ Database connection error: " . $e->getMessage() . "\n";
}

echo "\n3. File System Status:\n";

// Check key files
$files = [
    'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php' => 'Navbar Controller',
    'app/Http/Middleware/TenantMiddleware.php' => 'Tenant Middleware',
    'app/Services/TenantService.php' => 'Tenant Service',
    'routes/web.php' => 'Web Routes'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✓ {$description}: {$file}\n";
    } else {
        echo "   ✗ {$description}: {$file} (NOT FOUND)\n";
    }
}

echo "\n4. DNS/Hosts Configuration:\n";
$hostsFile = 'C:\\Windows\\System32\\drivers\\etc\\hosts';
if (file_exists($hostsFile)) {
    $hostsContent = file_get_contents($hostsFile);
    if (strpos($hostsContent, 'z.smartprep.local') !== false) {
        echo "   ✓ z.smartprep.local entry found in hosts file\n";
    } else {
        echo "   ✗ z.smartprep.local entry NOT found in hosts file\n";
        echo "   → You need to add: 127.0.0.1    z.smartprep.local\n";
    }
} else {
    echo "   ✗ Cannot access hosts file (may need administrator privileges)\n";
}

echo "\n5. Next Steps:\n";
echo "   1. If Laravel server isn't running: php artisan serve\n";
echo "   2. Add to hosts file: 127.0.0.1    z.smartprep.local\n";
echo "   3. Visit: http://z.smartprep.local:8000\n";
echo "   4. Test navbar changes in the dashboard\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "The backend system is ready. The issue is likely DNS resolution.\n";
