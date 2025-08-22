<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

$slug = $argv[1] ?? 'testwebsite';
$tenant = Tenant::where('slug', $slug)->first();
if (!$tenant) { echo "Tenant not found: $slug\n"; exit(1); }
$dbName = $tenant->database_name;
echo "Tenant DB: $dbName\n";
$tables = DB::select('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$dbName]);
if (!$tables) { echo "No tables found or DB missing.\n"; exit(1); }
foreach ($tables as $t) { echo $t->TABLE_NAME . "\n"; }
