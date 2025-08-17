<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

try {
    echo "Setting up ARTC tenant...\n";
    
    // First, check if tenant already exists
    $existingTenant = Tenant::where('domain', 'artc.smartprep.local')->first();
    
    if ($existingTenant) {
        echo "ARTC tenant already exists with ID: " . $existingTenant->id . "\n";
        echo "Domain: " . $existingTenant->domain . "\n";
        echo "Database: " . $existingTenant->database . "\n";
    } else {
        // Create the ARTC tenant record
        $tenant = Tenant::create([
            'name' => 'ARTC - Advanced Real-Time Computing',
            'domain' => 'artc.smartprep.local',
            'database' => 'smartprep_artc',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ ARTC tenant created successfully!\n";
        echo "Tenant ID: " . $tenant->id . "\n";
        echo "Name: " . $tenant->name . "\n";
        echo "Domain: " . $tenant->domain . "\n";
        echo "Database: " . $tenant->database . "\n";
    }
    
    // Test connection to tenant database
    echo "\nTesting connection to tenant database...\n";
    
    // Configure the tenant connection
    config(['database.connections.tenant.database' => 'smartprep_artc']);
    DB::purge('tenant');
    
    $tables = DB::connection('tenant')->select('SHOW TABLES');
    echo "✅ Connected to smartprep_artc database successfully!\n";
    echo "Found " . count($tables) . " tables in the tenant database.\n";
    
    // List some key tables
    echo "\nKey tables in tenant database:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (in_array($tableName, ['users', 'courses', 'quizzes', 'enrollments', 'quiz_attempts'])) {
            echo "- $tableName\n";
        }
    }
    
    echo "\n✅ Multi-tenant setup completed successfully!\n";
    echo "\nYour configuration:\n";
    echo "- Main Database: smartprep (for tenant management)\n";
    echo "- Template Database: smartprep_artc (current client database)\n";
    echo "- Future tenants will get copies of smartprep_artc structure\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
