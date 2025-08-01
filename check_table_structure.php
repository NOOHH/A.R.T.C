<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Quiz Questions Table Structure:\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('quiz_questions');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\nSample question data:\n";
$question = \Illuminate\Support\Facades\DB::table('quiz_questions')->where('id', 416)->first();
if ($question) {
    foreach ($question as $key => $value) {
        echo "$key: $value\n";
    }
}
