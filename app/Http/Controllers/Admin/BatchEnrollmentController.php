<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\User;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Package;
use App\Models\Module;
use App\Models\Course;
use App\Models\StudentBatch;

class BatchEnrollmentController extends Controller
{
    /**
     * Display the batch enrollment management interface
     */
    public function index()
    {
        try {
            // Get all necessary data for the batch enrollment interface
            $data = [
                'students' => Student::with('user')->get(),
                'programs' => Program::where('is_archived', false)->get(),
                'packages' => Package::all(),
                'modules' => Module::with('courses')->get(),
                'courses' => Course::where('is_active', true)->get(),
                'batches' => StudentBatch::whereIn('batch_status', ['available', 'ongoing', 'pending'])->get(),
                'enrollments' => Enrollment::with(['student', 'program', 'package'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20),
                'recentEnrollments' => Enrollment::with(['student', 'program'])
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
            ];

            return view('admin.batch-enrollment.index', $data);
        } catch (\Exception $e) {
            Log::error('Batch enrollment index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load batch enrollment data.');
        }
    }

    /**
     * Enroll multiple students into programs/courses/modules
     */
    public function batchEnroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:Full,Modular',
            'learning_mode' => 'required|in:Synchronous,Asynchronous',
            'batch_id' => 'nullable|exists:student_batches,batch_id',
            'selected_modules' => 'nullable|array',
            'selected_courses' => 'nullable|array',
            'start_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $enrolledStudents = [];
            $failedStudents = [];

            foreach ($request->student_ids as $studentId) {
                try {
                    $student = Student::where('student_id', $studentId)->first();
                    
                    if (!$student) {
                        $failedStudents[] = [
                            'student_id' => $studentId,
                            'reason' => 'Student not found'
                        ];
                        continue;
                    }

                    // Check if already enrolled in this program
                    $existingEnrollment = Enrollment::where('student_id', $studentId)
                        ->where('program_id', $request->program_id)
                        ->first();

                    if ($existingEnrollment) {
                        $failedStudents[] = [
                            'student_id' => $studentId,
                            'student_name' => $student->user->user_firstname . ' ' . $student->user->user_lastname,
                            'reason' => 'Already enrolled in this program'
                        ];
                        continue;
                    }

                    // Create enrollment
                    $enrollmentData = [
                        'student_id' => $studentId,
                        'user_id' => $student->user_id,
                        'program_id' => $request->program_id,
                        'package_id' => $request->package_id,
                        'enrollment_type' => $request->enrollment_type,
                        'learning_mode' => $request->learning_mode,
                        'enrollment_status' => 'approved', // Admin enrollments are auto-approved
                        'payment_status' => 'pending',
                        'batch_access_granted' => true,
                        'individual_start_date' => $request->start_date ? $request->start_date : now(),
                    ];

                    if ($request->batch_id) {
                        $enrollmentData['batch_id'] = $request->batch_id;
                    }

                    $enrollment = Enrollment::create($enrollmentData);

                    $enrolledStudents[] = [
                        'student_id' => $studentId,
                        'student_name' => $student->user->user_firstname . ' ' . $student->user->user_lastname,
                        'enrollment_id' => $enrollment->enrollment_id
                    ];

                    Log::info('Batch enrollment created', [
                        'student_id' => $studentId,
                        'program_id' => $request->program_id,
                        'enrollment_id' => $enrollment->enrollment_id
                    ]);

                } catch (\Exception $e) {
                    Log::error('Individual enrollment failed', [
                        'student_id' => $studentId,
                        'error' => $e->getMessage()
                    ]);
                    
                    $failedStudents[] = [
                        'student_id' => $studentId,
                        'reason' => 'Database error: ' . $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch enrollment completed',
                'enrolled_count' => count($enrolledStudents),
                'failed_count' => count($failedStudents),
                'enrolled_students' => $enrolledStudents,
                'failed_students' => $failedStudents
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch enrollment failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Batch enrollment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add additional enrollments to existing student
     */
    public function addEnrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:Full,Modular',
            'learning_mode' => 'required|in:Synchronous,Asynchronous',
            'batch_id' => 'nullable|exists:student_batches,batch_id',
            'selected_modules' => 'nullable|array',
            'selected_courses' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student = Student::where('student_id', $request->student_id)->first();

            // Check if already enrolled in this program
            $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                ->where('program_id', $request->program_id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is already enrolled in this program'
                ], 400);
            }

            $enrollmentData = [
                'student_id' => $request->student_id,
                'user_id' => $student->user_id,
                'program_id' => $request->program_id,
                'package_id' => $request->package_id,
                'enrollment_type' => $request->enrollment_type,
                'learning_mode' => $request->learning_mode,
                'enrollment_status' => 'approved',
                'payment_status' => 'pending',
                'batch_access_granted' => true,
                'individual_start_date' => now(),
            ];

            if ($request->batch_id) {
                $enrollmentData['batch_id'] = $request->batch_id;
            }

            $enrollment = Enrollment::create($enrollmentData);

            return response()->json([
                'success' => true,
                'message' => 'Additional enrollment created successfully',
                'enrollment' => $enrollment
            ]);

        } catch (\Exception $e) {
            Log::error('Add enrollment failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create enrollment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student enrollment details
     */
    public function getStudentEnrollments($studentId)
    {
        try {
            $student = Student::with('user')->where('student_id', $studentId)->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $enrollments = Enrollment::with(['program', 'package', 'batch'])
                ->where('student_id', $studentId)
                ->get();

            return response()->json([
                'success' => true,
                'student' => $student,
                'enrollments' => $enrollments
            ]);

        } catch (\Exception $e) {
            Log::error('Get student enrollments failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student enrollments'
            ], 500);
        }
    }

    /**
     * Export enrollment details
     */
    public function exportEnrollments(Request $request)
    {
        try {
            $query = Enrollment::with(['student.user', 'program', 'package', 'batch']);
            
            // Apply filters if provided
            if ($request->program_id) {
                $query->where('program_id', $request->program_id);
            }
            
            if ($request->enrollment_status) {
                $query->where('enrollment_status', $request->enrollment_status);
            }
            
            if ($request->enrollment_type) {
                $query->where('enrollment_type', $request->enrollment_type);
            }

            if ($request->date_from) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->where('created_at', '<=', $request->date_to);
            }

            $enrollments = $query->get();

            // Prepare CSV data
            $csvData = [];
            $csvData[] = [
                'Enrollment ID',
                'Student ID', 
                'Student Name',
                'Email',
                'Program Name',
                'Package Name',
                'Enrollment Type',
                'Learning Mode',
                'Enrollment Status',
                'Payment Status',
                'Batch Name',
                'Start Date',
                'Created At'
            ];

            foreach ($enrollments as $enrollment) {
                $csvData[] = [
                    $enrollment->enrollment_id,
                    $enrollment->student_id,
                    $enrollment->student && $enrollment->student->user ? 
                        $enrollment->student->user->user_firstname . ' ' . $enrollment->student->user->user_lastname : 'N/A',
                    $enrollment->student && $enrollment->student->user ? $enrollment->student->user->user_email : 'N/A',
                    $enrollment->program ? $enrollment->program->program_name : 'N/A',
                    $enrollment->package ? $enrollment->package->package_name : 'N/A',
                    $enrollment->enrollment_type,
                    $enrollment->learning_mode,
                    $enrollment->enrollment_status,
                    $enrollment->payment_status,
                    $enrollment->batch ? $enrollment->batch->batch_name : 'N/A',
                    $enrollment->individual_start_date,
                    $enrollment->created_at->format('Y-m-d H:i:s')
                ];
            }

            // Generate CSV
            $filename = 'enrollments_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            return response()->streamDownload(function () use ($csvData) {
                $file = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Export enrollments failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to export enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Get modules for a specific program
     */
    public function getProgramModules($programId)
    {
        try {
            $modules = Module::with('courses')
                ->where('program_id', $programId)
                ->get();

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch modules'
            ], 500);
        }
    }

    /**
     * Get courses for a specific module
     */
    public function getModuleCourses($moduleId)
    {
        try {
            $courses = Course::where('module_id', $moduleId)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch courses'
            ], 500);
        }
    }
}
