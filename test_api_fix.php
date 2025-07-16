<?php
// Test the fixed API endpoint
require_once 'vendor/autoload.php';
require_once 'config/database.php';

// Test the new method
$programId = 33;
$modules = \App\Models\Module::where('program_id', $programId)
                             ->where('is_archived', false)
                             ->orderBy('module_order', 'asc')
                             ->get(['modules_id', 'module_name', 'module_description', 'program_id']);

echo "Raw modules data:\n";
foreach ($modules as $module) {
    echo "modules_id: {$module->modules_id}, module_name: {$module->module_name}\n";
}

echo "\n\nTransformed modules data:\n";
$transformedModules = $modules->map(function ($module) {
    return [
        'id' => $module->modules_id,
        'module_name' => $module->module_name,
        'module_description' => $module->module_description,
        'program_id' => $module->program_id,
    ];
});

foreach ($transformedModules as $module) {
    echo "id: {$module['id']}, module_name: {$module['module_name']}\n";
}

echo "\n\nJSON output:\n";
echo json_encode([
    'success' => true,
    'modules' => $transformedModules
], JSON_PRETTY_PRINT);
?>
