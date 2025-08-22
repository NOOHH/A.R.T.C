<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\TenantService;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

$clientId = $argv[1] ?? 11;
$client = Client::find($clientId);
if (!$client) {
    echo "Client not found: $clientId\n";
    exit(1);
}

/** @var TenantService $tenantService */
$tenantService = app()->make(TenantService::class);

try {
    echo "Provisioning tenant for client {$client->id} ({$client->name}) domain={$client->domain}\n";
    $tenant = $tenantService->createTenant($client->name, $client->domain);
    echo "✅ Tenant created: id={$tenant->id} database={$tenant->database_name}\n";
    exit(0);
} catch (\Throwable $e) {
    echo "Failed to provision tenant: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(2);
}
