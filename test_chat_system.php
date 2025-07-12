<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Chat System..." . PHP_EOL;

// Test 1: Check if we can search users
echo "Test 1: Search users" . PHP_EOL;
try {
    $students = DB::table('students')
        ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
        ->where('students.is_archived', false)
        ->select(
            'students.user_id as id',
            DB::raw("CONCAT(students.firstname, ' ', students.lastname) as name"),
            'users.email',
            DB::raw("'student' as role")
        )
        ->get();
    
    echo "Found " . count($students) . " students" . PHP_EOL;
    foreach ($students as $student) {
        echo "  - {$student->name} ({$student->email})" . PHP_EOL;
    }
    
    $professors = DB::table('professors')
        ->where('professor_archived', false)
        ->select(
            'professor_id as id',
            DB::raw("CONCAT(professor_first_name, ' ', professor_last_name) as name"),
            'professor_email as email',
            DB::raw("'professor' as role")
        )
        ->get();
    
    echo "Found " . count($professors) . " professors" . PHP_EOL;
    foreach ($professors as $professor) {
        echo "  - {$professor->name} ({$professor->email})" . PHP_EOL;
    }
    
    $admins = DB::table('admins')
        ->select(
            'admin_id as id',
            'admin_name as name',
            'email',
            DB::raw("'admin' as role")
        )
        ->get();
    
    echo "Found " . count($admins) . " admins" . PHP_EOL;
    foreach ($admins as $admin) {
        echo "  - {$admin->name} ({$admin->email})" . PHP_EOL;
    }
    
    $directors = DB::table('directors')
        ->where('directors_archived', false)
        ->select(
            'directors_id as id',
            DB::raw("CONCAT(directors_first_name, ' ', directors_last_name) as name"),
            'directors_email as email',
            DB::raw("'director' as role")
        )
        ->get();
    
    echo "Found " . count($directors) . " directors" . PHP_EOL;
    foreach ($directors as $director) {
        echo "  - {$director->name} ({$director->email})" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// Test 2: Check if we can save a message
echo PHP_EOL . "Test 2: Save test message" . PHP_EOL;
try {
    $chatId = DB::table('chats')->insertGetId([
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Test message from system',
        'sent_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "Test message saved with ID: {$chatId}" . PHP_EOL;
    
    // Test 3: Retrieve messages
    echo PHP_EOL . "Test 3: Retrieve messages" . PHP_EOL;
    $messages = DB::table('chats')
        ->where(function($q) {
            $q->where('sender_id', 1)->where('receiver_id', 2);
        })
        ->orWhere(function($q) {
            $q->where('sender_id', 2)->where('receiver_id', 1);
        })
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "Found " . count($messages) . " messages between users 1 and 2" . PHP_EOL;
    foreach ($messages as $message) {
        echo "  - From {$message->sender_id} to {$message->receiver_id}: {$message->message}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "Chat system test completed." . PHP_EOL;
?>
