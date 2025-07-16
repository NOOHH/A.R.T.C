<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    // Check courses table
    echo "Checking courses table structure:\n";
    $stmt = $pdo->query('DESCRIBE courses');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo $column['Field'] . ' - ' . $column['Type'] . "\n";
    }
    
    // Check if lessons table exists
    echo "\nChecking if lessons table exists:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'lessons'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Lessons table exists\n";
        $stmt = $pdo->query('DESCRIBE lessons');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo $column['Field'] . ' - ' . $column['Type'] . "\n";
        }
    } else {
        echo "✗ Lessons table does not exist\n";
    }
    
    // Check if content_items table exists
    echo "\nChecking if content_items table exists:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'content_items'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Content_items table exists\n";
        $stmt = $pdo->query('DESCRIBE content_items');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo $column['Field'] . ' - ' . $column['Type'] . "\n";
        }
    } else {
        echo "✗ Content_items table does not exist\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
