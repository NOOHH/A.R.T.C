<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$content = \Illuminate\Support\Facades\DB::table('content_items')->where('id', 89)->first();
echo "Content Data Raw: " . var_export($content->content_data, true) . "\n";

// The JSON is double-encoded, so decode twice
$decoded1 = json_decode($content->content_data, true);
echo "First decode: " . var_export($decoded1, true) . "\n";

$decoded2 = json_decode($decoded1, true);
echo "Second decode: " . var_export($decoded2, true) . "\n";

echo "Quiz ID: " . ($decoded2['quiz_id'] ?? 'NOT FOUND') . "\n";
