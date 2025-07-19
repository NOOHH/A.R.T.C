<?php
header('Content-Type: text/plain');
echo "File Requirements Analysis\n";
echo "==========================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Education Levels file_requirements field:\n";
    echo "--------------------------------------------\n";
    $result = $pdo->query('SELECT id, level_name, file_requirements FROM education_levels');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Level: {$row['level_name']} (ID: {$row['id']})\n";
        echo "File Requirements: {$row['file_requirements']}\n";
        
        // Try to parse JSON if it's JSON
        if ($row['file_requirements']) {
            $decoded = json_decode($row['file_requirements'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "Parsed requirements:\n";
                print_r($decoded);
            }
        }
        echo "\n" . str_repeat("-", 50) . "\n";
    }
    
    echo "\n2. Form Requirements table:\n";
    echo "--------------------------\n";
    $result = $pdo->query('DESCRIBE form_requirements');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']}\n";
    }
    
    echo "\nForm Requirements data:\n";
    $result = $pdo->query('SELECT * FROM form_requirements LIMIT 10');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
