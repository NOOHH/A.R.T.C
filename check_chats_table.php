<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking chats table structure:\n";
    $columns = DB::select('SHOW COLUMNS FROM chats');
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nChecking if table has data:\n";
    $count = DB::table('chats')->count();
    echo "Total chats: $count\n";
    
    if ($count > 0) {
        echo "\nSample records:\n";
        $samples = DB::table('chats')->limit(3)->get();
        foreach ($samples as $chat) {
            echo "Chat ID: {$chat->chat_id}, Sender: {$chat->sender_id}, Receiver: {$chat->receiver_id}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
