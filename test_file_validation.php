<?php
// Quick test script for file validation debugging
header('Content-Type: text/plain');
echo "Education Level File Validation Test\n";
echo "===================================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Education Levels:\n";
    echo "--------------------\n";
    $stmt = $pdo->query('SELECT id, level_name FROM education_levels');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Level: {$row['level_name']} (ID: {$row['id']})\n";
    }
    
    echo "\n2. File Requirements by Education Level:\n";
    echo "----------------------------------------\n";
    $stmt = $pdo->query('
        SELECT er.*, el.level_name 
        FROM education_requirements er 
        LEFT JOIN education_levels el ON er.education_level_id = el.id 
        WHERE er.available_modular_plan = 1 
        ORDER BY el.id, er.field_name
    ');
    
    $currentLevel = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($currentLevel !== $row['level_name']) {
            echo "\n{$row['level_name']}:\n";
            $currentLevel = $row['level_name'];
        }
        $required = $row['is_required'] ? 'REQUIRED' : 'OPTIONAL';
        echo "  - {$row['field_name']} ({$row['file_type']}) - {$required}\n";
    }
    
    echo "\n3. Testing Form Field Mapping:\n";
    echo "------------------------------\n";
    // Test common field names that would appear in form
    $testFields = ['tor', 'psa', 'good_moral', 'diploma', 'transcript'];
    
    foreach ($testFields as $field) {
        $stmt = $pdo->prepare('
            SELECT er.*, el.level_name 
            FROM education_requirements er 
            LEFT JOIN education_levels el ON er.education_level_id = el.id 
            WHERE er.field_name LIKE ? AND er.available_modular_plan = 1
        ');
        $stmt->execute(["%{$field}%"]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $required = $row['is_required'] ? 'REQUIRED' : 'OPTIONAL';
            echo "Field '{$field}' -> '{$row['field_name']}' for {$row['level_name']} - {$required}\n";
        } else {
            echo "Field '{$field}' -> NOT FOUND in database\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
