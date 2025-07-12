<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "All users in users table:" . PHP_EOL;
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Email: {$user->email}, Role: {$user->role}" . PHP_EOL;
}
?>
