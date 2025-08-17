<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    // Force main DB (smartprep)
    config(['database.default' => 'mysql']);
    DB::purge('mysql');
    DB::connection('mysql');

    $email = getenv('SEED_EMAIL') ?: 'admin@smartprep.com';
    $password = getenv('SEED_PASSWORD') ?: 'admin123';

    // Ensure users table exists
    $tables = DB::select("SHOW TABLES LIKE 'users'");
    if (count($tables) === 0) {
        throw new RuntimeException("Table 'users' does not exist in main database.");
    }

    $existing = DB::table('users')->where('email', $email)->first();
    if ($existing) {
        echo "User already exists: {$email}\n";
    } else {
        $id = DB::table('users')->insertGetId([
            'user_firstname' => 'SmartPrep',
            'user_lastname' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Created SmartPrep user #{$id} with email {$email}\n";
        echo "Password: {$password}\n";
    }

    // Show quick status
    $row = DB::table('users')->select('user_id','email','role')->where('email',$email)->first();
    echo json_encode($row, JSON_PRETTY_PRINT)."\n";
    echo "Database: ".(DB::select('select database() as db')[0]->db ?? 'unknown')."\n";

} catch (Throwable $e) {
    echo "ERROR: ".$e->getMessage()."\n";
}
