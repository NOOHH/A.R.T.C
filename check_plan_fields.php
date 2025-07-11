<?php
// Quick test to check if learning mode fields exist in plan table
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    // Get all plans and their learning mode settings
    $plans = \App\Models\Plan::all();
    
    echo "Found " . $plans->count() . " plans:" . PHP_EOL;
    
    foreach ($plans as $plan) {
        echo "Plan ID: " . $plan->plan_id . PHP_EOL;
        echo "Plan Name: " . $plan->plan_name . PHP_EOL;
        
        // Check if learning mode fields exist
        if (isset($plan->enable_synchronous)) {
            echo "Enable Synchronous: " . ($plan->enable_synchronous ? 'true' : 'false') . PHP_EOL;
        } else {
            echo "Enable Synchronous: FIELD NOT FOUND" . PHP_EOL;
        }
        
        if (isset($plan->enable_asynchronous)) {
            echo "Enable Asynchronous: " . ($plan->enable_asynchronous ? 'true' : 'false') . PHP_EOL;
        } else {
            echo "Enable Asynchronous: FIELD NOT FOUND" . PHP_EOL;
        }
        
        echo "---" . PHP_EOL;
    }
    
    echo "Done!" . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
