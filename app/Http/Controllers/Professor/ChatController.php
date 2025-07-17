<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;

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
}
