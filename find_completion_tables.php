<?php

echo "=== FINDING COMPLETION TABLES ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. All completion-related tables:" . PHP_EOL;
    $stmt = $pdo->query("SHOW TABLES LIKE '%completion%'");
    while($row = $stmt->fetch()) {
        echo "   {$row[0]}" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. All tables in database:" . PHP_EOL;
    $stmt = $pdo->query("SHOW TABLES");
    $tables = [];
    while($row = $stmt->fetch()) {
        $tables[] = $row[0];
    }
    
    // Look for relevant tables
    $relevantTables = [];
    foreach ($tables as $table) {
        if (stripos($table, 'completion') !== false || 
            stripos($table, 'progress') !== false || 
            stripos($table, 'module') !== false || 
            stripos($table, 'course') !== false ||
            stripos($table, 'subject') !== false) {
            $relevantTables[] = $table;
        }
    }
    
    echo "   Relevant tables found:" . PHP_EOL;
    foreach ($relevantTables as $table) {
        echo "   - {$table}" . PHP_EOL;
    }
    
    // Check what completion data exists for our test student
    echo PHP_EOL . "3. Checking completion data for student 2025-07-00001:" . PHP_EOL;
    
    foreach ($relevantTables as $table) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE student_id = '2025-07-00001'");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                echo "   {$table}: {$count} records" . PHP_EOL;
            }
        } catch (Exception $e) {
            // Table might not have student_id column
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
