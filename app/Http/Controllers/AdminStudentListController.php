<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\AdminPreviewCustomization;
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
    use AdminPreviewCustomization;
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

    /**
     * Preview mode for tenant preview system
     */
    public function previewIndex($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock programs
            $programs = collect([
                (object)[
                    'program_id' => 1,
                    'program_name' => 'Nursing Review',
                    'is_archived' => false
                ],
                (object)[
                    'program_id' => 2,
                    'program_name' => 'Medical Technology Review',
                    'is_archived' => false
                ]
            ]);

            // Mock students - simplified structure
            $studentsCollection = collect([
                (object)[
                    'student_id' => 'STU001',
                    'firstname' => 'Juan',
                    'lastname' => 'Dela Cruz',
                    'email' => 'juan.delacruz@example.com',
                    'date_approved' => now(),
                    'is_archived' => false,
                    'created_at' => now(),
                    'program_id' => 1,
                    'program' => (object)[
                        'program_id' => 1,
                        'program_name' => 'Nursing Review'
                    ],
                    'enrollment' => (object)[
                        'learning_mode' => 'online',
                        'start_date' => now(),
                        'end_date' => now()->addMonths(6),
                        'batch' => (object)[
                            'batch_name' => 'Batch 2025-A',
                            'start_date' => now(),
                            'end_date' => now()->addMonths(6)
                        ]
                    ]
                ],
                (object)[
                    'student_id' => 'STU002',
                    'firstname' => 'Maria',
                    'lastname' => 'Santos',
                    'email' => 'maria.santos@example.com',
                    'date_approved' => null,
                    'is_archived' => false,
                    'created_at' => now()->subDays(5),
                    'program_id' => 2,
                    'program' => (object)[
                        'program_id' => 2,
                        'program_name' => 'Medical Technology Review'
                    ],
                    'enrollment' => (object)[
                        'learning_mode' => 'hybrid',
                        'start_date' => now()->addDays(7),
                        'end_date' => now()->addMonths(6)->addDays(7),
                        'batch' => (object)[
                            'batch_name' => 'Batch 2025-B',
                            'start_date' => now()->addDays(7),
                            'end_date' => now()->addMonths(6)->addDays(7)
                        ]
                    ]
                ],
                (object)[
                    'student_id' => 'STU003',
                    'firstname' => 'Pedro',
                    'lastname' => 'Garcia',
                    'email' => 'pedro.garcia@example.com',
                    'date_approved' => now()->subDays(2),
                    'is_archived' => false,
                    'created_at' => now()->subDays(10),
                    'program_id' => 1,
                    'program' => (object)[
                        'program_id' => 1,
                        'program_name' => 'Nursing Review'
                    ],
                    'enrollment' => (object)[
                        'learning_mode' => 'onsite',
                        'start_date' => now()->subDays(1),
                        'end_date' => now()->addMonths(6)->subDays(1),
                        'batch' => (object)[
                            'batch_name' => 'Batch 2025-A',
                            'start_date' => now()->subDays(1),
                            'end_date' => now()->addMonths(6)->subDays(1)
                        ]
                    ]
                ]
            ]);

            // Create paginator
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                $studentsCollection,
                $studentsCollection->count(),
                10,
                1,
                ['path' => request()->url()]
            );

            $html = view('admin.students.index', [
                'students' => $students,
                'programs' => $programs,
                'currentProgram' => null,
                'search' => '',
                'status' => 'all',
                'isPreview' => true
            ])->render();

            
            // Generate mock students data
            $students = $this->generateMockData('students');
            $programs = $this->generateMockData('programs');
            
            // Add program relationship to each student
            $students = $students->map(function($student) use ($programs) {
                $student->program = $programs->first();
                $student->program_id = $programs->first()->program_id;
                $student->enrollment = $this->createMockObject([
                    'learning_mode' => 'online',
                    'start_date' => now(),
                    'batch' => null
                ]);
                return $student;
            });
            
            view()->share('students', $students);
            view()->share('programs', $programs);
            view()->share('isPreviewMode', true);
            
            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin students preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Admin Students Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Students Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        } finally {
            // Clear session after render
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
