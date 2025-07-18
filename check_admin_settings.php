<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking admin_settings table structure:\n";

try {
    if (Schema::hasTable('admin_settings')) {
        $columns = DB::select('DESCRIBE admin_settings');
        foreach ($columns as $column) {
            echo $column->Field . ' (' . $column->Type . ")\n";
        }
    } else {
        echo "admin_settings table does not exist\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
