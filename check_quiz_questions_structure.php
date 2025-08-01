<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Quiz Questions Table Structure ===\n";
$columns = DB::select('DESCRIBE quiz_questions');
foreach($columns as $col) {
    echo $col->Field . ' - ' . $col->Type . ' - Key: ' . $col->Key . "\n";
}
