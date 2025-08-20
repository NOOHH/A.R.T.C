<?php
// Quick script to create ARTC tenant record
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = \App\Models\Tenant::create([
    'name' => 'ARTC Client',
    'slug' => 'artc',
    'domain' => 'artc.smartprep.local',
    'database_name' => 'smartprep_artc',
    'status' => 'active'
]);

echo "Tenant created: " . $tenant->name . " (" . $tenant->domain . ")\n";
