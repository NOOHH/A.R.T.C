<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Professor_Program Table Structure ===\n";
try {
    $columns = DB::select('DESCRIBE professor_program');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "Error describing professor_program table: " . $e->getMessage() . "\n";
}

echo "\n=== Sample Professor_Program Data ===\n";
try {
    $data = DB::table('professor_program')->limit(5)->get();
    echo "Found " . count($data) . " records\n\n";
    foreach ($data as $record) {
        echo "Record:\n";
        print_r($record);
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error querying professor_program table: " . $e->getMessage() . "\n";
}

echo "\n=== Professors Table Structure ===\n";
try {
    $columns = DB::select('DESCRIBE professors');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "Error describing professors table: " . $e->getMessage() . "\n";
}

echo "\n=== Sample Professors Data ===\n";
try {
    $data = DB::table('professors')->limit(5)->get();
    echo "Found " . count($data) . " records\n\n";
    foreach ($data as $record) {
        echo "Professor Record:\n";
        print_r($record);
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error querying professors table: " . $e->getMessage() . "\n";
}

echo "\n=== Programs Table Structure ===\n";
try {
    $columns = DB::select('DESCRIBE programs');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "Error describing programs table: " . $e->getMessage() . "\n";
}

echo "\n=== Sample Programs Data ===\n";
try {
    $data = DB::table('programs')->limit(3)->get();
    echo "Found " . count($data) . " records\n\n";
    foreach ($data as $record) {
        echo "Program Record:\n";
        print_r($record);
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error querying programs table: " . $e->getMessage() . "\n";
}
