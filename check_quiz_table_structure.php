<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    echo "Current quizzes table structure:\n";
    $columns = DB::select('DESCRIBE quizzes');
    foreach ($columns as $column) {
        echo $column->Field . " - " . $column->Type . "\n";
    }
    
    echo "\nSample quiz data to check existing columns:\n";
    $quiz = DB::table('quizzes')->first();
    if ($quiz) {
        foreach ((array)$quiz as $key => $value) {
            echo "$key: $value\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
