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
     * Export students to CSV
     */
    public function export(Request $request)
    {
        $students = Student::with(['user', 'program', 'enrollment.batch', 'enrollments.program', 'enrollments.package'])
            ->where('is_archived', false)

            // Apply search filter
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname',  'like', "%{$search}%")
                       ->orWhere('student_id','like', "%{$search}%")
                       ->orWhere('email',      'like', "%{$search}%");
                });
            })

            // Apply status filter
            ->when($request->status === 'approved', fn($q) => $q->whereNotNull('date_approved'))
            ->when($request->status === 'pending',  fn($q) => $q->whereNull('date_approved'))

            ->orderBy('lastname')
            ->get();

        $filename = 'students_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Student ID',
                'First Name', 
                'Middle Name',
                'Last Name',
                'Email',
                'Contact Number',
                'Emergency Contact',
                'Address',
                'City',
                'State/Province',
                'ZIP Code',
                'Program',
                'Enrollment Type',
                'Package',
                'Batch',
                'Start Date',
                'Date Approved',
                'Status',
                'Is Archived'
            ]);

            // Add data rows
            foreach ($students as $student) {
                $enrollment = $student->enrollments->first();
                fputcsv($file, [
                    $student->student_id ?? '',
                    $student->firstname ?? '',
                    $student->middlename ?? '',
                    $student->lastname ?? '',
                    $student->email ?? ($student->user->email ?? ''),
                    $student->contact_number ?? '',
                    $student->emergency_contact_number ?? '',
                    $student->street_address ?? '',
                    $student->city ?? '',
                    $student->state_province ?? '',
                    $student->zipcode ?? '',
                    $student->program->program_name ?? 'N/A',
                    $enrollment->enrollment_type ?? 'N/A',
                    $enrollment->package->package_name ?? 'N/A',
                    $enrollment->batch->batch_name ?? 'N/A',
                    $student->Start_Date ?? '',
                    $student->date_approved ? $student->date_approved->format('Y-m-d H:i:s') : '',
                    $student->date_approved ? 'Approved' : 'Pending',
                    $student->is_archived ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
