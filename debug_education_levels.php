<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Education Levels:\n\n";

$educationLevels = \App\Models\EducationLevel::all();

foreach ($educationLevels as $level) {
    echo "ID: {$level->id}\n";
    echo "Name: {$level->level_name}\n";
    echo "Plan Type: {$level->plan_type}\n";
    
    if ($level->file_requirements) {
        echo "File Requirements:\n";
        $requirements = is_string($level->file_requirements) 
            ? json_decode($level->file_requirements, true) 
            : $level->file_requirements;
        
        if (is_array($requirements)) {
            foreach ($requirements as $req) {
                echo "  - Document: " . ($req['document_type'] ?? 'N/A') . "\n";
                echo "    Field: " . ($req['field_name'] ?? 'N/A') . "\n";
                echo "    Required: " . (isset($req['is_required']) && $req['is_required'] ? 'Yes' : 'No') . "\n";
                echo "    Available Modular: " . (isset($req['available_modular_plan']) && $req['available_modular_plan'] ? 'Yes' : 'No') . "\n";
                echo "    Available Professional: " . (isset($req['available_professional_plan']) && $req['available_professional_plan'] ? 'Yes' : 'No') . "\n\n";
            }
        } else {
            echo "  Raw data: " . var_export($level->file_requirements, true) . "\n";
        }
    } else {
        echo "No file requirements\n";
    }
    
    echo "-------------------\n\n";
}
