<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    // Use main DB
    config(['database.default' => 'mysql']);
    DB::purge('mysql');
    DB::connection('mysql');

    $dbName = DB::select('select database() as db')[0]->db ?? 'unknown';
    echo "DB: {$dbName}\n";

    // Columns of users
    $cols = DB::select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' ORDER BY ORDINAL_POSITION", [$dbName]);
    foreach ($cols as $c) {
        echo $c->COLUMN_NAME.' ('.$c->DATA_TYPE.")\n";
    }

    // Accept CLI args: php debug_smartprep_users.php [emailOrUsername] [password]
    $lookup = $argv[1] ?? 'admin@smartprep.com';
    $passwordToTest = $argv[2] ?? null;

    echo "\nLookup: {$lookup}\n";
    $user = DB::table('users')
        ->where('email', $lookup)
        ->orWhere(function($q) use ($lookup) {
            $q->whereNotNull('username')->where('username', $lookup);
        })
        ->first();

    if (!$user) {
        echo "User not found.\n";
    } else {
        echo "User row:\n";
        echo json_encode($user, JSON_PRETTY_PRINT)."\n";
        if ($passwordToTest !== null) {
            $hash = $user->password ?? '';
            $ok = $hash ? (Hash::check($passwordToTest, $hash) ? 'MATCH' : 'NO MATCH') : 'NO PASSWORD FIELD';
            echo "\nPassword check for provided password: {$ok}\n";
        }
    }

} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}
