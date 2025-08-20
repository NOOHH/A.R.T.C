<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing website creation process...\n";

// Test the exact scenario from the error
try {
    echo "1. Testing database connection for Client model...\n";
    $client = new App\Models\Client();
    echo "   Client model database: " . $client->getConnection()->getDatabaseName() . "\n";
    
    echo "2. Testing TenantProvisioner...\n";
    $conn = \App\Services\TenantProvisioner::createDatabaseFromSqlDump('what');
    echo "   Database provisioned: " . $conn['db_name'] . "\n";
    
    echo "3. Testing Client creation...\n";
    $slug = 'smartprep-what';
    $testClient = App\Models\Client::create([
        'name' => 'what',
        'slug' => $slug,
        'status' => 'draft',
        'user_id' => 7, // Using the same user_id from the error
        'archived' => false,
        'db_name' => $conn['db_name'] ?? null,
        'db_host' => $conn['db_host'] ?? null,
        'db_port' => $conn['db_port'] ?? null,
        'db_username' => $conn['db_username'] ?? null,
        'db_password' => $conn['db_password'] ?? null,
    ]);
    echo "   Successfully created client with ID: " . $testClient->id . "\n";
    
    echo "4. Testing Tenant creation...\n";
    $tenant = \App\Models\Tenant::updateOrCreate(
        ['slug' => $testClient->slug],
        [
            'name' => $testClient->name,
            'database_name' => $conn['db_name'] ?? null,
            'domain' => $testClient->domain,
            'status' => $testClient->status ?? 'draft',
            'settings' => ['client_id' => $testClient->id],
        ]
    );
    echo "   Tenant created/updated with ID: " . $tenant->id . "\n";
    
    echo "5. Cleaning up test data...\n";
    $testClient->delete();
    $tenant->delete();
    
    // Drop the test database
    if (isset($conn['db_name'])) {
        \App\Services\TenantProvisioner::dropDatabase($conn['db_name']);
        echo "   Test database dropped: " . $conn['db_name'] . "\n";
    }
    
    echo "✅ Test completed successfully! The issue appears to be fixed.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    // Clean up on error
    if (isset($testClient) && $testClient->exists) {
        $testClient->delete();
    }
    if (isset($tenant) && $tenant->exists) {
        $tenant->delete();
    }
    if (isset($conn['db_name'])) {
        \App\Services\TenantProvisioner::dropDatabase($conn['db_name']);
    }
}
