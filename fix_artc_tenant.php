<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;

try {
    echo "Fixing ARTC tenant database field...\n";
    
    $tenant = Tenant::where('domain', 'artc.smartprep.local')->first();
    
    if ($tenant) {
        $tenant->database_name = 'smartprep_artc';
        $tenant->slug = 'artc';
        $tenant->save();
        
        echo "✅ ARTC tenant updated successfully!\n";
        echo "Tenant ID: " . $tenant->id . "\n";
        echo "Name: " . $tenant->name . "\n";
        echo "Slug: " . $tenant->slug . "\n";
        echo "Domain: " . $tenant->domain . "\n";
        echo "Database: " . $tenant->database_name . "\n";
    } else {
        echo "❌ ARTC tenant not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
