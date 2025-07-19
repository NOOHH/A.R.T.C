<?php
header('Content-Type: text/plain');
echo "Complete Education Level Requirements Test\n";
echo "==========================================\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get both education levels
    $result = $pdo->query('SELECT * FROM education_levels ORDER BY id');
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Education Level: {$row['level_name']} (ID: {$row['id']})\n";
        echo str_repeat("=", 50) . "\n";
        
        echo "Available for:\n";
        echo "  - General: " . ($row['available_for_general'] ? 'YES' : 'NO') . "\n";
        echo "  - Professional: " . ($row['available_for_professional'] ? 'YES' : 'NO') . "\n";
        echo "  - Review: " . ($row['available_for_review'] ? 'YES' : 'NO') . "\n";
        echo "  - Active: " . ($row['is_active'] ? 'YES' : 'NO') . "\n\n";
        
        if ($row['file_requirements']) {
            echo "Raw file_requirements (first 200 chars):\n";
            echo substr($row['file_requirements'], 0, 200) . "...\n\n";
            
            $decoded = json_decode($row['file_requirements'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                echo "Parsed Requirements (Modular Plan Only):\n";
                foreach ($decoded as $req) {
                    if (isset($req['available_modular_plan']) && $req['available_modular_plan']) {
                        $required = (isset($req['is_required']) && $req['is_required']) ? 'REQUIRED' : 'OPTIONAL';
                        echo "  ✓ Field: {$req['field_name']}\n";
                        echo "    Type: {$req['file_type']}\n";
                        echo "    Required: {$required}\n";
                        echo "    Document Type: {$req['document_type']}\n";
                        echo "    Description: " . ($req['description'] ?? 'N/A') . "\n";
                        echo "    Modular: " . ($req['available_modular_plan'] ? 'YES' : 'NO') . "\n\n";
                    }
                }
                
                // Count requirements by type
                $totalReqs = count($decoded);
                $modularReqs = 0;
                $requiredReqs = 0;
                foreach ($decoded as $req) {
                    if (isset($req['available_modular_plan']) && $req['available_modular_plan']) {
                        $modularReqs++;
                        if (isset($req['is_required']) && $req['is_required']) {
                            $requiredReqs++;
                        }
                    }
                }
                echo "Summary: {$totalReqs} total, {$modularReqs} for modular, {$requiredReqs} required\n";
                
            } else {
                echo "JSON Error: " . json_last_error_msg() . "\n";
                echo "Raw data first 500 chars:\n";
                echo substr($row['file_requirements'], 0, 500) . "\n";
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
