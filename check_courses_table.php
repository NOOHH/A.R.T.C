<?php
// Check courses table structure
try {
    $pdo = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE courses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Courses table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} ({$column['Null']}, {$column['Key']}, {$column['Default']})\n";
    }
    
    // Check if there are any existing courses
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nExisting courses count: {$count['count']}\n";
    
    // Check modules table structure for reference
    $stmt = $pdo->query("DESCRIBE modules");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nModules table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} ({$column['Null']}, {$column['Key']}, {$column['Default']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
