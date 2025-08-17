<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DIRECTORS TABLE STRUCTURE ===\n";
$columns = DB::select('DESCRIBE smartprep_artc.directors');
foreach($columns as $column) {
    echo $column->Field . ' (' . $column->Type . ')' . "\n";
}

echo "\n=== ADMINS TABLE STRUCTURE ===\n";
$columns = DB::select('DESCRIBE smartprep_artc.admins');
foreach($columns as $column) {
    echo $column->Field . ' (' . $column->Type . ')' . "\n";
}
