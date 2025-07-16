<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the modules API
echo "Testing Modules API...\n";

$program = App\Models\Program::first();
echo "Testing with program ID: {$program->program_id}\n";

// Test the API endpoint directly
$programId = $program->program_id;

try {
    $modules = \App\Models\Module::where('program_id', $programId)
                                 ->where('is_archived', false)
                                 ->orderBy('module_order', 'asc')
                                 ->select('modules_id as id', 'module_name', 'module_description', 'program_id')
                                 ->get();
    
    echo "✅ Modules API query successful!\n";
    echo "Found " . $modules->count() . " modules\n";
    
    foreach ($modules as $module) {
        echo "- Module {$module->id}: {$module->module_name}\n";
    }
    
    // Test the JSON response format
    $response = [
        'success' => true,
        'modules' => $modules
    ];
    
    echo "\n✅ JSON Response format test:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error in modules API: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
