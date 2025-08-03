<?php
require_once(__DIR__ . '/vendor/autoload.php');

$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== Admin Users ===\n";
try {
    $admins = \App\Models\Admin::all(['admin_id', 'username', 'email']);
    if ($admins->count() > 0) {
        foreach($admins as $admin) {
            echo "Admin ID: {$admin->admin_id}, Username: {$admin->username}, Email: {$admin->email}\n";
        }
    } else {
        echo "No admin users found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Director Users ===\n";
try {
    $directors = \App\Models\Director::all(['director_id', 'username', 'email']);
    if ($directors->count() > 0) {
        foreach($directors as $director) {
            echo "Director ID: {$director->director_id}, Username: {$director->username}, Email: {$director->email}\n";
        }
    } else {
        echo "No director users found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
