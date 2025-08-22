<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Client;

$clients = Client::all(['id','name','slug','domain'])->toArray();
echo json_encode($clients, JSON_PRETTY_PRINT);
