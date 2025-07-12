<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $admin = DB::select("SHOW TABLES LIKE 'admin'");
    if ($admin) {
        echo 'Admin table exists' . PHP_EOL;
        $admins = DB::table('admin')->get();
        foreach ($admins as $a) {
            echo 'Admin: ' . print_r($a, true) . PHP_EOL;
        }
    } else {
        echo 'Admin table does not exist' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
