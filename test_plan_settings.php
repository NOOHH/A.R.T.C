<?php
// Test script to check plan learning mode settings
require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check Plan 1 (Full Plan)
$fullPlan = \App\Models\Plan::find(1);
if ($fullPlan) {
    echo "Plan 1 (Full Plan) Learning Mode Settings:" . PHP_EOL;
    echo "ID: " . $fullPlan->plan_id . PHP_EOL;
    echo "Name: " . $fullPlan->plan_name . PHP_EOL;
    echo "Enable Synchronous: " . ($fullPlan->enable_synchronous ? 'true' : 'false') . PHP_EOL;
    echo "Enable Asynchronous: " . ($fullPlan->enable_asynchronous ? 'true' : 'false') . PHP_EOL;
    echo "Learning Mode Config: " . json_encode($fullPlan->learning_mode_config) . PHP_EOL;
} else {
    echo "Plan 1 not found!" . PHP_EOL;
}

echo PHP_EOL;

// Check Plan 2 (Modular Plan)
$modularPlan = \App\Models\Plan::find(2);
if ($modularPlan) {
    echo "Plan 2 (Modular Plan) Learning Mode Settings:" . PHP_EOL;
    echo "ID: " . $modularPlan->plan_id . PHP_EOL;
    echo "Name: " . $modularPlan->plan_name . PHP_EOL;
    echo "Enable Synchronous: " . ($modularPlan->enable_synchronous ? 'true' : 'false') . PHP_EOL;
    echo "Enable Asynchronous: " . ($modularPlan->enable_asynchronous ? 'true' : 'false') . PHP_EOL;
    echo "Learning Mode Config: " . json_encode($modularPlan->learning_mode_config) . PHP_EOL;
} else {
    echo "Plan 2 not found!" . PHP_EOL;
}

echo PHP_EOL . "Done!" . PHP_EOL;
?>
