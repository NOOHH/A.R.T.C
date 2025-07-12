<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Users Table Structure ===\n";

// Get column information
$columns = DB::select("DESCRIBE users");
foreach ($columns as $column) {
    echo "Column: {$column->Field} | Type: {$column->Type} | Null: {$column->Null} | Key: {$column->Key} | Default: {$column->Default}\n";
}

echo "\n=== Sample Data ===\n";
$users = DB::table('users')->select('user_id', 'role', 'user_firstname', 'user_lastname', 'email')->take(3)->get();
foreach ($users as $user) {
    echo "ID: {$user->user_id} | Role: '{$user->role}' | Name: {$user->user_firstname} {$user->user_lastname}\n";
}
?>
