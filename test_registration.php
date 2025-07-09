<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Create a simple connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'artc',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Test creating a registration with dynamic fields (similar to the error scenario)
try {
    $result = $capsule->connection()->table('registrations')->insert([
        'user_id' => 1,
        'Start_Date' => '2025-07-15',
        'status' => 'pending',
        'package_id' => 2,
        'program_id' => 1,
        'plan_id' => 1,
        'enrollment_type' => 'Full',
        'learning_mode' => 'Synchronous',
        'package_name' => 'Premium Package',
        'program_name' => 'Nursing Board Review',
        'plan_name' => 'Modular',
        'dynamic_fields' => json_encode([
            'First_Name' => '123456789',
            'Middle_Name' => '123456789',
            'ss' => '123456789'
        ]),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "SUCCESS: Registration created successfully!\n";
    echo "The nullable fields fix is working correctly.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Clean up the test data
try {
    $capsule->connection()->table('registrations')->where('dynamic_fields', 'like', '%123456789%')->delete();
    echo "Test data cleaned up.\n";
} catch (Exception $e) {
    echo "Note: Could not clean up test data - " . $e->getMessage() . "\n";
}
