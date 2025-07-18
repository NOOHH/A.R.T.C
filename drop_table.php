<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    DB::statement('DROP TABLE IF EXISTS registration_modules');
    echo "Table dropped successfully.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
