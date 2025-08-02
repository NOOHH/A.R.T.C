<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== ADMIN ACCOUNTS CHECK ===\n";

try {
    $admins = App\Models\Admin::select('admin_id', 'admin_name', 'email')->get();
    
    echo "Found " . $admins->count() . " admin accounts:\n";
    
    foreach($admins as $admin) {
        echo $admin->admin_id . " - " . $admin->admin_name . " (" . $admin->email . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END ===\n";
