<?php
header('Content-Type: text/plain');
echo "Complete File Requirements Analysis\n";
echo "==================================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $result = $pdo->query('SELECT id, level_name, file_requirements FROM education_levels ORDER BY id');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Education Level: {$row['level_name']} (ID: {$row['id']})\n";
        echo str_repeat("=", 50) . "\n";
        
        if ($row['file_requirements']) {
            $decoded = json_decode($row['file_requirements'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                echo "Modular Plan Requirements:\n";
                foreach ($decoded as $req) {
                    if (isset($req['available_modular_plan']) && $req['available_modular_plan']) {
                        $required = $req['is_required'] ? 'REQUIRED' : 'OPTIONAL';
                        echo "  - Field: {$req['field_name']}\n";
                        echo "    Type: {$req['file_type']}\n";
                        echo "    Required: {$required}\n";
                        echo "    Description: " . ($req['description'] ?? 'N/A') . "\n";
                        echo "    Document Type: {$req['document_type']}\n";
                        echo "\n";
                    }
                }
            } else {
                echo "Invalid JSON or not an array\n";
            }
        } else {
            echo "No file requirements defined\n";
        }
        
        echo "\n" . str_repeat("-", 70) . "\n\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
