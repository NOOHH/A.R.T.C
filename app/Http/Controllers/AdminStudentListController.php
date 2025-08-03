<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Get students for batch enrollment (AJAX)
     */
    public function getStudentsForBatchEnrollment(Request $request)
    {
        $students = Student::with(['user', 'enrollments.program'])
            ->where('is_archived', false)
            ->whereNotNull('date_approved') // Only approved students
            
            // Search filter
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname',  'like', "%{$search}%")
                       ->orWhere('student_id','like', "%{$search}%")
                       ->orWhere('email',      'like', "%{$search}%");
                });
            })
            
            ->orderBy('lastname')
            ->get()
            ->map(function($student) {
                $enrolledPrograms = $student->enrollments->pluck('program.program_name')->toArray();
                return [
                    'student_id' => $student->student_id,
                    'user_id' => $student->user_id,
                    'name' => trim($student->firstname . ' ' . $student->lastname),
                    'email' => $student->email ?? ($student->user->email ?? ''),
                    'enrolled_programs' => $enrolledPrograms,
                    'enrollment_count' => count($enrolledPrograms)
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Batch enroll multiple students to programs
     */
    public function batchEnrollStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|string',
            'program_id' => 'required|exists:programs,program_id',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:full,modular',
            'learning_mode' => 'required|in:online,face-to-face,hybrid',
            'batch_id' => 'nullable|exists:student_batches,batch_id'
        ]);

        $results = [
            'successful' => [],
            'failed' => [],
            'duplicates' => []
        ];

        DB::beginTransaction();
        
        try {
            foreach ($request->student_ids as $studentId) {
                $student = Student::where('student_id', $studentId)->first();
                
                if (!$student) {
                    $results['failed'][] = "Student ID {$studentId} not found";
                    continue;
                }

                // Check for duplicate enrollment
                $existingEnrollment = Enrollment::where('student_id', $studentId)
                    ->where('program_id', $request->program_id)
                    ->first();

                if ($existingEnrollment) {
                    $results['duplicates'][] = "{$student->firstname} {$student->lastname} ({$studentId}) - Already enrolled";
                    continue;
                }

                // Create enrollment
                $enrollment = Enrollment::create([
                    'student_id' => $studentId,
                    'user_id' => $student->user_id,
                    'program_id' => $request->program_id,
                    'package_id' => $request->package_id,
                    'batch_id' => $request->batch_id,
                    'enrollment_type' => $request->enrollment_type,
                    'learning_mode' => $request->learning_mode,
                    'enrollment_status' => 'approved', // Auto-approve admin enrollments
                    'payment_status' => 'pending',
                    'enrollment_date' => now()
                ]);

                $results['successful'][] = "{$student->firstname} {$student->lastname} ({$studentId}) - Enrolled successfully";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch enrollment completed',
                'results' => $results,
                'summary' => [
                    'total_processed' => count($request->student_ids),
                    'successful' => count($results['successful']),
                    'failed' => count($results['failed']),
                    'duplicates' => count($results['duplicates'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Batch enrollment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export students to CSV with applied filters
     */
    public function export(Request $request)
    {
        try {
            // Clear any output buffers to prevent corruption
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            Log::info('Student CSV export started', ['filters' => $request->all()]);
            
            $students = Student::with(['user', 'program', 'enrollment.batch', 'enrollments.program', 'enrollments.package'])
                ->where('is_archived', false)

            // Apply program filter
            ->when($request->filled('program_id'), function($q) use ($request) {
                $q->whereHas('program', function($q2) use ($request) {
                    $q2->where('programs.program_id', $request->program_id);
                });
            })

            // Apply batch filter
            ->when($request->filled('batch_id'), function($q) use ($request) {
                $q->whereHas('enrollments', function($q2) use ($request) {
                    $q2->where('batch_id', $request->batch_id);
                });
            })

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

        // Generate filename with filter info
        $filterInfo = [];
        if ($request->filled('program_id')) {
            $program = Program::find($request->program_id);
            if ($program) {
                $filterInfo[] = 'program_' . str_replace(' ', '_', strtolower($program->program_name));
            }
        }
        if ($request->filled('batch_id')) {
            $filterInfo[] = 'batch_' . $request->batch_id;
        }
        if ($request->filled('status')) {
            $filterInfo[] = $request->status;
        }
        if ($request->filled('search')) {
            $filterInfo[] = 'search_' . str_replace(' ', '_', strtolower($request->search));
        }

        $filename = 'students_export';
        if (!empty($filterInfo)) {
            $filename .= '_' . implode('_', $filterInfo);
        }
        $filename .= '_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Program',
                'Batch',
                'Learning Mode',
                'Start Date',
                'End Date',
                'Status',
                'Registered'
            ]);

            // Add data rows
            foreach ($students as $student) {
                $enrollment = $student->enrollments->first();
                
                // Handle date_approved properly - it might be a string or Carbon object
                $registeredDate = '';
                if ($student->date_approved) {
                    if (is_string($student->date_approved)) {
                        $registeredDate = $student->date_approved;
                    } else {
                        $registeredDate = $student->date_approved->format('Y-m-d H:i:s');
                    }
                }

                fputcsv($file, [
                    $student->student_id ?? '',
                    trim(($student->firstname ?? '') . ' ' . ($student->lastname ?? '')),
                    $student->email ?? ($student->user->email ?? ''),
                    $student->program->program_name ?? 'N/A',
                    $enrollment->batch->batch_name ?? 'N/A',
                    $enrollment->learning_mode ?? 'N/A',
                    $student->Start_Date ?? '',
                    $enrollment->end_date ?? 'N/A', // You may need to adjust this field name
                    $student->date_approved ? 'Approved' : 'Pending',
                    $registeredDate
                ]);
            }

            fclose($file);
        };

        Log::info('Student CSV export completed', ['student_count' => $students->count()]);
        return response()->stream($callback, 200, $headers);
        
        } catch (\Exception $e) {
            Log::error('Student CSV export failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
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
