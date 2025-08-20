<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Client model database connection...\n";

$client = new App\Models\Client();
echo "Client model database: " . $client->getConnection()->getDatabaseName() . "\n";

// Test creating a client
try {
    $testClient = App\Models\Client::create([
        'name' => 'Test Client ' . time(),
        'slug' => 'test-client-' . time(),
        'db_name' => 'test_db_' . time(),
        'status' => 'draft'
    ]);
    echo "Successfully created client with ID: " . $testClient->id . "\n";
    
    // Clean up - delete the test client
    $testClient->delete();
    echo "Test client deleted successfully\n";
    
} catch (Exception $e) {
    echo "Error creating client: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
