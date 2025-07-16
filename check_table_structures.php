<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Directors Table Structure ===\n";
$structure = DB::select('DESCRIBE directors');
foreach($structure as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}\n";
}

echo "\n=== Professors Table Structure ===\n";
$structure = DB::select('DESCRIBE professors');
foreach($structure as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}\n";
}

// Check actual data
echo "\n=== Sample Director Data ===\n";
$director = DB::table('directors')->first();
if ($director) {
    foreach($director as $key => $value) {
        echo "$key: $value\n";
    }
}

echo "\n=== Sample Professor Data ===\n";
$professor = DB::table('professors')->first();
if ($professor) {
    foreach($professor as $key => $value) {
        echo "$key: $value\n";
    }
}
