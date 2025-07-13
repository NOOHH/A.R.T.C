<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Admins table:" . PHP_EOL;
$admins = DB::table('admins')->get();
foreach ($admins as $admin) {
    echo "Admin: " . print_r($admin, true) . PHP_EOL;
}

echo "Admins table structure:" . PHP_EOL;
$columns = DB::select("DESCRIBE admins");
foreach ($columns as $column) {
    echo $column->Field . " ";
}
echo PHP_EOL;
?>
