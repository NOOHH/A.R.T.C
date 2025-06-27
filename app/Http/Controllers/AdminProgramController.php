<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
        return view('admin.admin-programs', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:100',
        ]);
        Program::create([
            'program_name' => $request->program_name,
            'created_by_admin_id' => Auth::user()->admin_id ?? 1, // fallback for demo
        ]);
        return redirect()->route('admin.programs.index')->with('success', 'Program added successfully!');
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();
        return redirect()->route('admin.programs.index')->with('success', 'Program deleted successfully!');
    }
}