<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$course = DB::table('courses')->first();
$module = DB::table('modules')->where('modules_id', $course->module_id)->first();

echo "Course: " . json_encode($course, JSON_PRETTY_PRINT) . "\n";
echo "Module: " . json_encode($module, JSON_PRETTY_PRINT) . "\n";
?>
