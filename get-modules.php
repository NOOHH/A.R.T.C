<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$modules = DB::table('modules')->select('modules_id', 'module_name')->limit(5)->get();

echo "Available modules:\n";
foreach($modules as $module) {
    echo $module->modules_id . ': ' . $module->module_name . "\n";
}

?>
