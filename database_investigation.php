<?php
// Quick database investigation
$databases = ['smartprep_test1', 'smartprep_artc', 'artc_main'];

echo "🔍 DATABASE INVESTIGATION\n";
echo "=========================\n\n";

foreach ($databases as $dbName) {
    echo "Checking database: {$dbName}\n";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname={$dbName}", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ Connected to {$dbName}\n";
        
        // Check for tenant-related tables
        $stmt = $pdo->query("SHOW TABLES LIKE '%tenant%'");
        $tenantTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($tenantTables)) {
            echo "📋 Tenant tables found:\n";
            foreach ($tenantTables as $table) {
                echo "   • {$table}\n";
            }
            
            // Check for customization data
            foreach ($tenantTables as $table) {
                if (strpos($table, 'setting') !== false || strpos($table, 'config') !== false) {
                    $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 3");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($data)) {
                        echo "📄 Sample data from {$table}:\n";
                        foreach ($data as $row) {
                            echo "   " . json_encode($row) . "\n";
                        }
                    }
                }
            }
        } else {
            echo "❌ No tenant tables found\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to connect to {$dbName}: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Check for TEST11 branding source
echo "🎨 SEARCHING FOR TEST11 BRANDING SOURCE\n";
echo "=====================================\n";

// Check if branding comes from session or other source
session_start();
if (isset($_SESSION['navbar_customization'])) {
    echo "✅ Session contains navbar_customization\n";
    echo "Data: " . print_r($_SESSION['navbar_customization'], true) . "\n";
} else {
    echo "❌ No navbar_customization in session\n";
}

// Check AdminPreviewCustomization trait file
$traitFile = 'app/Http/Traits/AdminPreviewCustomization.php';
if (file_exists($traitFile)) {
    $content = file_get_contents($traitFile);
    if (strpos($content, 'TEST11') !== false) {
        echo "✅ Found TEST11 in AdminPreviewCustomization trait\n";
    } else {
        echo "❌ No TEST11 found in AdminPreviewCustomization trait\n";
    }
} else {
    echo "❌ AdminPreviewCustomization trait file not found\n";
}

?>
