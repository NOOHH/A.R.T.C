<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Checking User-Professor ID Relationship ===\n";
    
    // Check if professor ID 8 exists in users table
    $userWithId8 = DB::table('users')->where('user_id', 8)->first();
    if ($userWithId8) {
        echo "User with ID 8: {$userWithId8->user_firstname} {$userWithId8->user_lastname} ({$userWithId8->email}) - Role: {$userWithId8->role}\n";
    } else {
        echo "No user found with ID 8\n";
    }
    
    // Check professor with ID 8
    $professorWithId8 = DB::table('professors')->where('professor_id', 8)->first();
    if ($professorWithId8) {
        echo "Professor with ID 8: {$professorWithId8->professor_name} ({$professorWithId8->professor_email})\n";
    } else {
        echo "No professor found with ID 8\n";
    }
    
    // Check if there are users with professor role
    echo "\nChecking users with professor role...\n";
    $professorUsers = DB::table('users')->where('role', 'professor')->get();
    echo "Found {$professorUsers->count()} users with professor role:\n";
    foreach ($professorUsers as $user) {
        echo "- User ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}\n";
    }
    
    // Check the chat relationship setup
    echo "\n=== Testing Chat Model Relationships ===\n";
    $chat = App\Models\Chat::with('sender', 'receiver')->latest()->first();
    if ($chat) {
        echo "Latest chat ID: {$chat->chat_id}\n";
        echo "Sender ID: {$chat->sender_id}\n";
        echo "Receiver ID: {$chat->receiver_id}\n";
        
        if ($chat->sender) {
            echo "Sender details: {$chat->sender->user_firstname} {$chat->sender->user_lastname}\n";
        } else {
            echo "❌ Sender relationship not working\n";
        }
        
        if ($chat->receiver) {
            echo "Receiver details: {$chat->receiver->user_firstname} {$chat->receiver->user_lastname}\n";
        } else {
            echo "❌ Receiver relationship not working\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
