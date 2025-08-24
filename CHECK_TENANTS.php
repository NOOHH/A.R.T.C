<?php
// Check available tenants
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TENANT VERIFICATION ===\n";

// Check available tenants
$tenants = \App\Models\Tenant::all();
echo "Available tenants:\n";
foreach ($tenants as $tenant) {
    echo "- Slug: {$tenant->slug}, DB: {$tenant->database_name}\n";
}

// Check if smartprep tenant exists
$smartprepTenant = \App\Models\Tenant::where('slug', 'smartprep')->first();
if ($smartprepTenant) {
    echo "\n✅ smartprep tenant found: {$smartprepTenant->database_name}\n";
} else {
    echo "\n❌ smartprep tenant not found\n";
}

// Check databases
echo "\nAvailable databases:\n";
$databases = DB::select('SHOW DATABASES LIKE "smartprep_%"');
foreach ($databases as $db) {
    echo "- " . array_values((array)$db)[0] . "\n";
}
?>
