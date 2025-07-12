<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing message retrieval for user 112 (bryan) with user 8:" . PHP_EOL;

$myId = 112;
$otherId = 8;

try {
    $messages = DB::table('chats')
        ->where(function($q) use ($myId, $otherId) {
            $q->where('sender_id', $myId)
              ->where('receiver_id', $otherId);
        })
        ->orWhere(function($q) use ($myId, $otherId) {
            $q->where('sender_id', $otherId)
              ->where('receiver_id', $myId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    echo "Found " . count($messages) . " messages" . PHP_EOL;
    
    // Test transformation
    $transformedMessages = $messages->map(function($message) {
        return [
            'id' => $message->chat_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'content' => $message->message,
            'message' => $message->message,
            'created_at' => $message->created_at,
            'sender' => [
                'id' => $message->sender_id,
                'name' => getUserName($message->sender_id)
            ]
        ];
    });
    
    echo "Transformed messages:" . PHP_EOL;
    foreach ($transformedMessages as $msg) {
        echo "  From {$msg['sender']['name']} ({$msg['sender_id']}) to {$msg['receiver_id']}: {$msg['content']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

function getUserName($userId) {
    // Check in users table first
    $user = DB::table('users')->where('user_id', $userId)->first();
    if ($user) {
        return $user->user_firstname . ' ' . $user->user_lastname;
    }

    // Check in students table
    $student = DB::table('students')->where('user_id', $userId)->first();
    if ($student) {
        return $student->firstname . ' ' . $student->lastname;
    }

    // Check in professors table
    $professor = DB::table('professors')->where('professor_id', $userId)->first();
    if ($professor) {
        return $professor->professor_first_name . ' ' . $professor->professor_last_name;
    }

    // Check in directors table
    $director = DB::table('directors')->where('directors_id', $userId)->first();
    if ($director) {
        return $director->directors_first_name . ' ' . $director->directors_last_name;
    }

    // Check in admins table
    $admin = DB::table('admins')->where('admin_id', $userId)->first();
    if ($admin) {
        return $admin->admin_name;
    }

    return 'Unknown User';
}
?>
