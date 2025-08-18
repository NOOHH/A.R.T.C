<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ADMIN MODEL ===\n\n";

try {
    echo "Testing Admin model with tenant connection:\n";
    $admins = \App\Models\Admin::all();
    echo "Found " . $admins->count() . " admins in tenant database:\n";
    
    foreach($admins as $admin) {
        echo "- ID: " . $admin->admin_id . "\n";
        echo "  Name: " . $admin->admin_name . "\n";
        echo "  Name (via accessor): " . $admin->name . "\n";
        echo "  Email: " . $admin->email . "\n";
        echo "  Password: " . substr($admin->password, 0, 20) . "...\n";
        echo "  Created: " . $admin->created_at . "\n\n";
    }
    
    echo "Now testing specific login credential:\n";
    $admin = \App\Models\Admin::where('email', 'admin@smartprep.com')->first();
    if ($admin) {
        echo "✅ Found admin with email admin@smartprep.com\n";
        echo "   ID: " . $admin->admin_id . "\n";
        echo "   Name: " . $admin->name . "\n";
        echo "   Email: " . $admin->email . "\n";
        echo "   Has password: " . (!empty($admin->password) ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Admin with email admin@smartprep.com not found\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
