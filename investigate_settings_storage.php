<?php
echo "=== INVESTIGATING SETTINGS STORAGE ISSUE ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    
    echo "1. Checking tables in tenant database (smartprep_test1):\n";
    $pdo->exec("USE smartprep_test1");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n2. Checking for settings-related tables in main smartprep database:\n";
    $pdo->exec("USE smartprep");
    $settingsTables = $pdo->query("SHOW TABLES LIKE '%setting%'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($settingsTables as $table) {
        echo "   - $table\n";
        
        // Check the structure
        $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        echo "     Columns: ";
        foreach ($columns as $col) {
            echo $col['Field'] . " ";
        }
        echo "\n";
        
        // Check sample data
        $sampleData = $pdo->query("SELECT * FROM $table LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($sampleData)) {
            echo "     Sample data:\n";
            foreach ($sampleData as $row) {
                echo "       " . json_encode($row) . "\n";
            }
        }
    }
    
    echo "\n3. Checking for any customization-related tables:\n";
    $customTables = $pdo->query("SHOW TABLES LIKE '%custom%'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($customTables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n4. Checking for any website/client-specific settings:\n";
    $allTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($allTables as $table) {
        try {
            // Check if table has client_id or website_id columns
            $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('client_id', $columns) || in_array('website_id', $columns)) {
                echo "   - $table (has client/website reference)\n";
                
                // Check for any data
                $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "     Records: $count\n";
            }
        } catch (Exception $e) {
            // Skip tables we can't access
        }
    }
    
    echo "\n=== INVESTIGATION COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
