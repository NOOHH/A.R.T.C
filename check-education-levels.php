<?php

require_once 'bootstrap/app.php';

echo "=== EDUCATION LEVELS DEBUG ===\n\n";

try {
    $levels = DB::table('education_levels')->get();
    
    foreach ($levels as $level) {
        echo "Education Level ID: {$level->id}\n";
        
        // Check all possible name fields
        $fields = get_object_vars($level);
        foreach ($fields as $key => $value) {
            if (in_array($key, ['name', 'level_name', 'education_level']) && $value) {
                echo "  Name field '{$key}': {$value}\n";
            }
        }
        
        if (isset($level->file_requirements) && $level->file_requirements) {
            $requirements = json_decode($level->file_requirements, true);
            if ($requirements) {
                echo "  File Requirements: " . count($requirements) . " items\n";
                foreach ($requirements as $req) {
                    $fieldName = $req['custom_name'] ?? $req['field_name'] ?? $req['document_type'] ?? 'Unknown';
                    $isRequired = $req['is_required'] ?? false;
                    $availableModular = $req['available_modular_plan'] ?? false;
                    echo "    - {$fieldName} (Required: " . ($isRequired ? 'Yes' : 'No') . ", Modular: " . ($availableModular ? 'Yes' : 'No') . ")\n";
                }
            } else {
                echo "  File Requirements: Invalid JSON\n";
            }
        } else {
            echo "  File Requirements: None\n";
        }
        
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
