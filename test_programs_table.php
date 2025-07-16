<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING PROGRAMS TABLE ===" . PHP_EOL;
    
    $stmt = $pdo->query('DESCRIBE programs');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Programs table structure:" . PHP_EOL;
    foreach ($columns as $col) {
        echo "  {$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']}" . PHP_EOL;
    }
    
    $stmt = $pdo->query('SELECT * FROM programs ORDER BY program_name ASC');
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo PHP_EOL . "Programs data:" . PHP_EOL;
    foreach ($programs as $program) {
        echo "  ID: {$program['program_id']} | Name: {$program['program_name']} | Archived: " . ($program['is_archived'] ?? 'NULL') . PHP_EOL;
    }
    
    echo PHP_EOL . "PROGRAMS TABLE EXISTS AND HAS DATA" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
