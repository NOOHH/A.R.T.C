<?php
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ALL TENANTS ===\n";
$tenants = \App\Models\Tenant::all();
foreach ($tenants as $tenant) {
    echo "Tenant: ID={$tenant->id}, Slug={$tenant->slug}, Name={$tenant->name}, DB={$tenant->database_name}\n";
}

echo "\n=== ALL CLIENTS ===\n";
$clients = \App\Models\Client::all();
foreach ($clients as $client) {
    echo "Client: ID={$client->id}, Slug={$client->slug}, Name={$client->name}, DB={$client->db_name}\n";
}
