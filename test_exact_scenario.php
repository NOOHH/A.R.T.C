<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing the exact controller scenario that was failing...\n";

try {
    // Set up the request context
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_HOST'] = '127.0.0.1:8000';
    $_SERVER['REQUEST_URI'] = '/smartprep/dashboard/websites';
    
    // Create a test request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'name' => 'what',
        'status' => 'draft',
        '_token' => 'test'
    ]);
    
    // Test the exact flow from the controller
    echo "1. Testing middleware setup...\n";
    $middleware = new \App\Http\Middleware\Smartprep\UseMainDatabase(new \App\Services\TenantService());
    $next = function($req) { return $req; };
    $middleware->handle($request, $next);
    
    echo "2. Testing authentication context...\n";
    // Simulate an authenticated user (user_id 7 from the error)
    $isAdmin = false; // Simulate non-admin user
    $userId = 7;
    
    echo "3. Testing slug generation...\n";
    $slugBase = 'smartprep-' . \Illuminate\Support\Str::slug('what');
    echo "   Generated slug base: $slugBase\n";
    
    $slug = $slugBase;
    $i = 1;
    while (\App\Models\Client::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $i++;
    }
    echo "   Final slug: $slug\n";
    
    echo "4. Testing TenantProvisioner...\n";
    $conn = \App\Services\TenantProvisioner::createDatabaseFromSqlDump('what');
    echo "   Database provisioned: " . $conn['db_name'] . "\n";
    
    echo "5. Testing Client creation...\n";
    $client = \App\Models\Client::create([
        'name' => 'what',
        'slug' => $slug,
        'status' => 'draft',
        'user_id' => $userId,
        'archived' => false,
        'db_name' => $conn['db_name'] ?? null,
        'db_host' => $conn['db_host'] ?? null,
        'db_port' => $conn['db_port'] ?? null,
        'db_username' => $conn['db_username'] ?? null,
        'db_password' => $conn['db_password'] ?? null,
    ]);
    echo "   Client created with ID: " . $client->id . "\n";
    echo "   Client database connection: " . $client->getConnection()->getDatabaseName() . "\n";
    
    echo "6. Testing Tenant creation...\n";
    $tenant = \App\Models\Tenant::updateOrCreate(
        ['slug' => $client->slug],
        [
            'name' => $client->name,
            'database_name' => $conn['db_name'] ?? null,
            'domain' => $client->domain,
            'status' => $client->status ?? 'draft',
            'settings' => ['client_id' => $client->id],
        ]
    );
    echo "   Tenant created/updated with ID: " . $tenant->id . "\n";
    
    echo "7. Cleaning up...\n";
    $client->delete();
    $tenant->delete();
    if (isset($conn['db_name'])) {
        \App\Services\TenantProvisioner::dropDatabase($conn['db_name']);
        echo "   Database dropped: " . $conn['db_name'] . "\n";
    }
    
    echo "\n✅ SUCCESS! The original error has been fixed.\n";
    echo "The system can now create websites without the 'clients table not found' error.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    // Clean up on error
    if (isset($client) && $client->exists) {
        $client->delete();
    }
    if (isset($tenant) && $tenant->exists) {
        $tenant->delete();
    }
    if (isset($conn['db_name'])) {
        \App\Services\TenantProvisioner::dropDatabase($conn['db_name']);
    }
}
