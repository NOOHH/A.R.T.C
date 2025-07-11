<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;

class AdminStudentListController extends Controller
{
    /**
     * Display a listing of students (non-archived), with filters.
     */
    public function index(Request $request)
    {
        // Pull all non-archived programs for the dropdown
        $programs = Program::where('is_archived', false)
                           ->orderBy('program_name')
                           ->get();

        // Build the students query
        $students = Student::with(['user', 'program', 'enrollment.batch'])
            ->where('is_archived', false)

            // Filter by selected program via the relation (qualified column)
            ->when($request->filled('program_id'), function($q) use ($request) {
                $q->whereHas('program', function($q2) use ($request) {
                    $q2->where('programs.program_id', $request->program_id);
                });
            })

            // Search by name, ID, or email
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname',  'like', "%{$search}%")
                       ->orWhere('student_id','like', "%{$search}%")
                       ->orWhere('email',      'like', "%{$search}%");
                });
            })

            // Filter by approval status
            ->when($request->status === 'approved', fn($q) => $q->whereNotNull('date_approved'))
            ->when($request->status === 'pending',  fn($q) => $q->whereNull('date_approved'))

            // Sort and paginate
            ->orderBy('lastname')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'programs'));
    }

    /**
     * Show a single student's details.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'program']);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Approve a pending student.
     */
    public function approve(Student $student)
    {
        $student->update(['date_approved' => now()]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student approved successfully!');
    }

    /**
     * Revoke approval from a student.
     */
    public function disapprove(Student $student)
    {
        $student->update(['date_approved' => null]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student approval revoked!');
    }

    /**
     * Archive a student (soft-archive).
     */
    public function archive(Student $student)
    {
        $student->update(['is_archived' => true]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student archived successfully!');
    }

    /**
     * List archived students.
     */
    public function archived(Request $request)
    {
        $programs = Program::where('is_archived', false)
                           ->orderBy('program_name')
                           ->get();

        $students = Student::with(['user', 'program'])
            ->where('is_archived', true)

            // Filter by selected program via the relation (qualified column)
            ->when($request->filled('program_id'), function($q) use ($request) {
                $q->whereHas('program', function($q2) use ($request) {
                    $q2->where('programs.program_id', $request->program_id);
                });
            })

            // Search by name, ID, or email
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname',  'like', "%{$search}%")
                       ->orWhere('student_id','like', "%{$search}%")
                       ->orWhere('email',      'like', "%{$search}%");
                });
            })

            // Sort and paginate
            ->orderBy('lastname')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.archived', compact('students', 'programs'));
    }

    /**
     * Restore an archived student.
     */
    public function restore(Student $student)
    {
        $student->update(['is_archived' => false]);

        return redirect()
            ->route('admin.students.archived')
            ->with('success', 'Student restored successfully!');
    }

    /**
     * Permanently delete a student record.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()
            ->route('admin.students.archived')
            ->with('success', 'Student deleted permanently!');
    }
}
