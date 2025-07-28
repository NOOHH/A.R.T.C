<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Chat;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $professor = Professor::find(session('professor_id'));

        if (!$professor) {
            return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
        }

        return view('professor.chat.index', compact('professor'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'message' => 'required|string|max:1000'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        if (!$professor || !$professor->user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $chat = Chat::create([
            'sender_id' => $professor->user->user_id,
            'receiver_id' => $request->receiver_id,
            'message' => encrypt($request->message),
            'sent_at' => now(),
            'is_read' => false
        ]);

        // Load the relationships for broadcasting
        $chat->load(['sender', 'receiver']);

        // Broadcast the message
        broadcast(new MessageSent($chat))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $chat->chat_id,
                'message' => $request->message,
                'sent_at' => $chat->sent_at,
                'sender' => [
                    'id' => $professor->user->user_id,
                    'name' => $professor->user->full_name,
                    'role' => $professor->user->role
                ]
            ]
        ]);
    }
}
