<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

$slug = $argv[1] ?? 'testwebsite';
$tenant = Tenant::where('slug', $slug)->first();
if (!$tenant) {
    echo "Tenant not found: $slug\n";
    exit(1);
}

$dbName = $tenant->database_name;
echo "Tenant: id={$tenant->id} slug={$tenant->slug} db={$dbName}\n";

// Check if database exists
$exists = DB::selectOne('SELECT SCHEMA_NAME as s FROM information_schema.schemata WHERE SCHEMA_NAME = ?', [$dbName]);
if (!$exists) {
    echo "Database $dbName does not exist.\n";
    exit(2);
}

// List tables count
$tablesCount = DB::selectOne('SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = ?', [$dbName]);
echo "Tables in $dbName: " . ($tablesCount->c ?? 0) . "\n";

// List columns in settings table
$cols = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE FROM information_schema.columns WHERE table_schema = ? AND table_name = ?', [$dbName, 'settings']);
if (!$cols) {
    echo "Table 'settings' not found in $dbName.\n";
    exit(3);
}

echo "settings columns:\n";
foreach ($cols as $c) {
    echo " - {$c->COLUMN_NAME} : {$c->COLUMN_TYPE}\n";
}

// Check if 'type' column exists
$typeCol = DB::selectOne('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND COLUMN_NAME = ?', [$dbName, 'settings', 'type']);
if ($typeCol) {
    echo "'type' column exists.\n";
} else {
    echo "'type' column MISSING.\n";
}

exit(0);
