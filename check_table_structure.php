<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking registrations table structure:\n";
try {
    $result = DB::select('DESCRIBE registrations');
    foreach($result as $col) {
        echo "{$col->Field} - {$col->Type} - Key: {$col->Key}\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nChecking modules table structure:\n";
try {
    $result = DB::select('DESCRIBE modules');
    foreach($result as $col) {
        echo "{$col->Field} - {$col->Type} - Key: {$col->Key}\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nChecking courses table structure:\n";
try {
    $result = DB::select('DESCRIBE courses');
    foreach($result as $col) {
        echo "{$col->Field} - {$col->Type} - Key: {$col->Key}\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nChecking packages table structure:\n";
try {
    $result = DB::select('DESCRIBE packages');
    foreach($result as $col) {
        echo "{$col->Field} - {$col->Type} - Key: {$col->Key}\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
