<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking module data...\n";

$modules = App\Models\Module::where('program_id', 32)->get(['modules_id', 'module_name', 'program_id']);

foreach($modules as $module) {
    echo "ID: {$module->modules_id}, Name: {$module->module_name}\n";
}

echo "\nTesting the select with alias...\n";

$modulesWithAlias = App\Models\Module::where('program_id', 32)
                                     ->select('modules_id as id', 'module_name', 'program_id')
                                     ->get();

foreach($modulesWithAlias as $module) {
    echo "ID: {$module->id}, Name: {$module->module_name}\n";
}
