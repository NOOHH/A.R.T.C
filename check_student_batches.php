<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Student Batches Table Structure ===\n";
$structure = DB::select('DESCRIBE student_batches');
foreach($structure as $column) {
    echo "{$column->Field} ({$column->Type})\n";
}

echo "\n=== Sample Data ===\n";
$sample = DB::table('student_batches')->first();
if ($sample) {
    foreach($sample as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No data found\n";
}
