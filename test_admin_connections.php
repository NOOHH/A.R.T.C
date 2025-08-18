<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing SmartPrep Admin model with main database:" . PHP_EOL;

try {
    $admins = \App\Models\Smartprep\Admin::all();
    echo "Found " . $admins->count() . " admins in main database" . PHP_EOL;
    
    foreach($admins as $admin) {
        echo "- ID: " . $admin->id . ", Name: " . ($admin->name ?? 'N/A') . ", Email: " . $admin->email . PHP_EOL;
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Testing ARTC tenant Admin model with tenant database:" . PHP_EOL;

try {
    $tenantAdmins = \App\Models\Admin::all();
    echo "Found " . $tenantAdmins->count() . " admins in tenant database" . PHP_EOL;
    
    foreach($tenantAdmins as $admin) {
        echo "- ID: " . $admin->admin_id . ", Name: " . ($admin->admin_name ?? 'N/A') . ", Email: " . $admin->email . PHP_EOL;
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
