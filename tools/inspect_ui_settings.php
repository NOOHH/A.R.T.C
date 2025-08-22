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

$cols = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.columns WHERE table_schema = ? AND table_name = ?', [$dbName, 'ui_settings']);
if (!$cols) { echo "No ui_settings table in $dbName\n"; exit(1); }

foreach ($cols as $c) {
    echo "{$c->COLUMN_NAME} | {$c->COLUMN_TYPE} | Nullable: {$c->IS_NULLABLE} | Default: {$c->COLUMN_DEFAULT}\n";
}
