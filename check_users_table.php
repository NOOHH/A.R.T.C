<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Users Table Structure ===\n";
$columns = DB::select('DESCRIBE users');
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\n=== Sample Users Data ===\n";
$users = DB::table('users')->limit(3)->get();
foreach ($users as $user) {
    echo "User ID: {$user->user_id}\n";
    echo "Fields: " . implode(', ', array_keys((array)$user)) . "\n";
    print_r($user);
    break;
}
