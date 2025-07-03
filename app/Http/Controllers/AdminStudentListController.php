<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;

class AdminStudentListController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'program'])->where('is_archived', false);

        // Filter by program if provided
        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('program_id', $request->program_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'approved') {
                $query->whereNotNull('date_approved');
            } elseif ($request->status === 'pending') {
                $query->whereNull('date_approved');
            }
        }

        $students = $query->orderBy('lastname')->paginate(20);
        $programs = Program::where('is_archived', false)->orderBy('program_name')->get();

        return view('admin.students.index', compact('students', 'programs'));
    }

    public function show(Student $student)
    {
        $student->load(['user', 'program']);
        return view('admin.students.show', compact('student'));
    }

    public function approve(Student $student)
    {
        $student->update([
            'date_approved' => now()
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student approved successfully!');
    }

    public function disapprove(Student $student)
    {
        $student->update([
            'date_approved' => null
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student approval revoked!');
    }

    public function archive(Student $student)
    {
        $student->update(['is_archived' => true]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student archived successfully!');
    }

    public function archived(Request $request)
    {
        $query = Student::with(['user', 'program'])->where('is_archived', true);

        // Filter by program if provided
        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('program_id', $request->program_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('lastname')->paginate(20);
        $programs = Program::where('is_archived', false)->orderBy('program_name')->get();

        return view('admin.students.archived', compact('students', 'programs'));
    }

    public function restore(Student $student)
    {
        $student->update(['is_archived' => false]);

        return redirect()->route('admin.students.archived')
            ->with('success', 'Student restored successfully!');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.archived')
            ->with('success', 'Student deleted permanently!');
    }
}
