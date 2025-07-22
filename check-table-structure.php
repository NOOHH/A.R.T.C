<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "=== PAYMENT METHODS TABLE STRUCTURE ===\n\n";
    
    $stmt = $pdo->query("DESCRIBE payment_methods");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Column Details:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) " . 
             ($column['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column['Default'] !== null ? " DEFAULT {$column['Default']}" : '') . "\n";
    }
    
    echo "\nFile Upload Columns Check:\n";
    $fileColumns = ['school_id', 'diploma', 'tor', 'psa_birth_certificate', 'form_137'];
    $existingColumns = array_column($columns, 'Field');
    
    foreach ($fileColumns as $column) {
        $exists = in_array($column, $existingColumns);
        echo ($exists ? "✓" : "✗") . " {$column}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
