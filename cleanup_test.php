<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('chats')->where('message', 'Test message from system')->delete();
echo 'Test message cleaned up' . PHP_EOL;
?>
