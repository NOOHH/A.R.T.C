<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== Testing Admin Database Connection ===" . PHP_EOL;

try {
    $admin = App\Models\Admin::first();
    
    if ($admin) {
        echo "✓ Admin found in smartprep_artc database!" . PHP_EOL;
        echo "Email: " . $admin->email . PHP_EOL;
        echo "Name: " . $admin->admin_name . PHP_EOL;
        echo "ID: " . $admin->admin_id . PHP_EOL;
        
        echo PHP_EOL . "All admins:" . PHP_EOL;
        $allAdmins = App\Models\Admin::all();
        foreach ($allAdmins as $admin) {
            echo "- {$admin->admin_name} ({$admin->email})" . PHP_EOL;
        }
        
    } else {
        echo "✗ No admin found" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== Admin Login Credentials ===" . PHP_EOL;
echo "URL: http://localhost/A.R.T.C/public/login" . PHP_EOL;
echo "Admins available:" . PHP_EOL;
echo "1. admin@artc.com" . PHP_EOL;
echo "2. bmjustimbaste2003@gmail.com" . PHP_EOL;
echo "Test with the existing passwords." . PHP_EOL;

?>
