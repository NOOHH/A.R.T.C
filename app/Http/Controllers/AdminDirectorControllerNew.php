<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDirectorController extends Controller
{
    public function index()
    {
        $directors = Director::with(['programs', 'admin'])
            ->where('directors_archived', false)
            ->orderBy('directors_name')
            ->get();

        return view('admin.directors.director', compact('directors'));
    }

    public function create()
    {
        return view('admin.directors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'directors_name' => 'required|string|max:100',
            'directors_email' => 'required|email|unique:directors,directors_email',
            'directors_password' => 'required|string|min:6',
        ]);

        $validated['directors_password'] = bcrypt($validated['directors_password']);
        $validated['admin_id'] = session('admin_id');

        Director::create($validated);

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director added successfully!');
    }

    public function show(Director $director)
    {
        $director->load(['programs', 'admin']);
        return view('admin.directors.show', compact('director'));
    }

    public function edit(Director $director)
    {
        return view('admin.directors.edit', compact('director'));
    }

    public function update(Request $request, Director $director)
    {
        $validated = $request->validate([
            'directors_name' => 'required|string|max:100',
            'directors_email' => ['required', 'email', Rule::unique('directors')->ignore($director->directors_id, 'directors_id')],
        ]);

        if ($request->filled('directors_password')) {
            $validated['directors_password'] = bcrypt($request->directors_password);
        }

        $director->update($validated);

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director updated successfully!');
    }

    public function archive(Director $director)
    {
        $director->update(['directors_archived' => true]);

        return redirect()->route('admin.directors.index')
            ->with('success', 'Director archived successfully!');
    }

    public function archived()
    {
        $directors = Director::with(['programs', 'admin'])
            ->where('directors_archived', true)
            ->orderBy('directors_name')
            ->get();

        return view('admin.directors.archived', compact('directors'));
    }

    public function restore(Director $director)
    {
        $director->update(['directors_archived' => false]);

        return redirect()->route('admin.directors.archived')
            ->with('success', 'Director restored successfully!');
    }

    public function destroy(Director $director)
    {
        if ($director->programs()->count() > 0) {
            return redirect()->route('admin.directors.archived')
                ->with('error', 'Cannot delete director with assigned programs. Please reassign programs first.');
        }

        $director->delete();

        return redirect()->route('admin.directors.archived')
            ->with('success', 'Director deleted permanently!');
    }

    public function assignProgram(Request $request, Director $director)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $program->update(['director_id' => $director->directors_id]);

        return redirect()->route('admin.directors.show', $director)
            ->with('success', 'Program assigned successfully!');
    }

    public function unassignProgram(Request $request, Director $director)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $program->update(['director_id' => null]);

        return redirect()->route('admin.directors.show', $director)
            ->with('success', 'Program unassigned successfully!');
    }
}
