<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Registration;
use App\Models\Program;
use App\Models\Module;
use App\Models\Batch;
use App\Models\BoardPasser;
use App\Models\StudentBatch;
use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Writer;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Analytics is only available for admins and directors.');
        }

        try {
            return view('admin.admin-analytics.admin-analytics', [
                'isAdmin' => ($userType === 'admin'),
                'userType' => $userType
            ]);
        } catch (\Exception $e) {
            Log::error('Analytics index error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading analytics dashboard.');
        }
    }

    public function getData(Request $request)
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return response()->json(['error' => 'Access denied. Analytics is only available for admins and directors.'], 403);
        }

        try {
            $filters = $this->getFilters($request);
            
            $data = [
                'metrics' => $this->getMetrics($filters),
                'charts' => $this->getChartData($filters),
                'tables' => $this->getTableData($filters)
            ];

            // If user is director, exclude referral data
            if ($userType === 'director') {
                // Remove referral-related data from response
                if (isset($data['charts']['referrals'])) {
                    unset($data['charts']['referrals']);
                }
                if (isset($data['metrics']['referrals'])) {
                    unset($data['metrics']['referrals']);
                }
                if (isset($data['tables']['referrals'])) {
                    unset($data['tables']['referrals']);
                }
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Analytics data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load analytics data'], 500);
        }
    }

    public function getBatches()
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return response()->json(['error' => 'Access denied. Analytics is only available for admins and directors.'], 403);
        }

        try {
            $batches = StudentBatch::select('batch_id as id', 'batch_name as name')
                ->orderBy('batch_name')
                ->get();

            return response()->json($batches);
        } catch (\Exception $e) {
            Log::error('Get batches error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load batches'], 500);
        }
    }

    public function getSubjects()
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return response()->json(['error' => 'Access denied. Analytics is only available for admins and directors.'], 403);
        }

        try {
            // Get subjects from modules table
            $subjects = Module::select('modules_id as id', 'module_name as name')
                ->where('is_archived', false)
                ->orderBy('module_name')
                ->get();

            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('Get subjects error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load subjects'], 500);
        }
    }

    public function getPrograms()
    {
        try {
            // Get programs from programs table using DB facade for reliability
            $programs = DB::table('programs')
                ->select('program_id as id', 'program_name as name')
                ->where('is_archived', 0)
                ->orderBy('program_name')
                ->get();

            return response()->json($programs);
        } catch (\Exception $e) {
            Log::error('Get programs error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load programs'], 500);
        }
    }

    public function getStudentDetail($id)
    {
        try {
            $student = User::with(['registration'])->findOrFail($id);

            $html = view('admin.admin-analytics.partials.student-detail', compact('student'))->render();

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            Log::error('Student detail error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load student details'], 500);
        }
    }

    public function getSubjectDetail($id)
    {
        try {
            $subject = Module::with(['quizzes', 'activities'])->findOrFail($id);
            
            return redirect()->route('admin.analytics.index')
                ->with('subject_focus', $id);
        } catch (\Exception $e) {
            Log::error('Subject detail error: ' . $e->getMessage());
            return redirect()->route('admin.analytics.index')
                ->with('error', 'Failed to load subject details');
        }
    }

    public function export(Request $request)
    {
        // Check if user is admin only (not director)
        $userType = session('user_type');
        if (!$userType || $userType !== 'admin') {
            return response()->json(['error' => 'Access denied. Export functionality is restricted to admins only.'], 403);
        }

        try {
            $format = $request->get('format', 'pdf');
            $filters = $this->getFilters($request);
            
            // Get table data but exclude topPerformers
            $tableData = $this->getTableData($filters);
            unset($tableData['topPerformers']); // Remove top performers from export
            
            $data = [
                'metrics' => $this->getMetrics($filters),
                'charts' => $this->getChartData($filters),
                'tables' => $tableData,
                'filters' => $filters,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'exported_by' => session('user_name') ?? 'Admin',
                'export_format' => $format
            ];

            if ($format === 'excel') {
                return $this->exportToExcel($data);
            } elseif ($format === 'csv') {
                return $this->exportToCSV($data);
            } else {
                return $this->exportToPDF($data);
            }
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export data: ' . $e->getMessage()], 500);
        }
    }

    private function exportToExcel($data)
    {
        // Create Excel format data
        $excelData = [
            'summary' => [
                'Board Pass Rate' => $data['metrics']['boardPassRate'] . '%',
                'Total Students' => $data['metrics']['totalStudents'],
                'Average Quiz Score' => $data['metrics']['avgQuizScore'] . '%',
                'Completion Rate' => $data['metrics']['completionRate'] . '%',
            ],
            'top_performers' => $data['tables']['topPerformers'] ?? [],
            'subject_breakdown' => $data['tables']['subjectBreakdown'] ?? [],
            'recently_enrolled' => $data['tables']['recentlyEnrolled'] ?? [],
            'recently_completed' => $data['tables']['recentlyCompleted'] ?? [],
            'recent_payments' => $data['tables']['recentPayments'] ?? [],
            'board_passers' => $data['tables']['boardPassers'] ?? [],
            'batch_performance' => $data['tables']['batchPerformance'] ?? [],
            'metadata' => [
                'Generated At' => $data['generated_at'],
                'Exported By' => $data['exported_by'],
                'Filters Applied' => json_encode($data['filters'])
            ]
        ];

        return response()->json($excelData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="analytics-report-' . now()->format('Y-m-d-H-i-s') . '.json"'
        ]);
    }

    private function exportToCSV($data)
    {
        $filename = 'analytics-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write summary metrics
            fputcsv($file, ['ANALYTICS SUMMARY']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Board Pass Rate', $data['metrics']['boardPassRate'] . '%']);
            fputcsv($file, ['Total Students', $data['metrics']['totalStudents']]);
            fputcsv($file, ['Average Quiz Score', $data['metrics']['avgQuizScore'] . '%']);
            fputcsv($file, ['Completion Rate', $data['metrics']['completionRate'] . '%']);
            fputcsv($file, []); // Empty row

            // Write top performers
            if (!empty($data['tables']['topPerformers'])) {
                fputcsv($file, ['TOP PERFORMERS']);
                fputcsv($file, ['Rank', 'Name', 'Score', 'Program']);
                foreach ($data['tables']['topPerformers'] as $index => $student) {
                    fputcsv($file, [
                        $index + 1,
                        $student['name'] ?? 'N/A',
                        ($student['score'] ?? 0) . '%',
                        $student['program'] ?? 'N/A'
                    ]);
                }
                fputcsv($file, []); // Empty row
            }

            // Write recently enrolled students
            if (!empty($data['tables']['recentlyEnrolled'])) {
                fputcsv($file, ['RECENTLY ENROLLED STUDENTS']);
                fputcsv($file, ['Name', 'Email', 'Program', 'Plan', 'Enrollment Date', 'Status']);
                foreach ($data['tables']['recentlyEnrolled'] as $student) {
                    fputcsv($file, [
                        $student['name'] ?? 'N/A',
                        $student['email'] ?? 'N/A',
                        $student['program'] ?? 'N/A',
                        $student['plan'] ?? 'N/A',
                        $student['enrollment_date'] ?? 'N/A',
                        $student['status'] ?? 'N/A'
                    ]);
                }
                fputcsv($file, []); // Empty row
            }

            // Write recently completed students
            if (!empty($data['tables']['recentlyCompleted'])) {
                fputcsv($file, ['RECENTLY COMPLETED STUDENTS']);
                fputcsv($file, ['Name', 'Email', 'Program', 'Plan', 'Completion Date', 'Final Score']);
                foreach ($data['tables']['recentlyCompleted'] as $student) {
                    fputcsv($file, [
                        $student['name'] ?? 'N/A',
                        $student['email'] ?? 'N/A',
                        $student['program'] ?? 'N/A',
                        $student['plan'] ?? 'N/A',
                        $student['completion_date'] ?? 'N/A',
                        ($student['final_score'] ?? 0) . '%'
                    ]);
                }
                fputcsv($file, []); // Empty row
            }

            // Write board passers
            if (!empty($data['tables']['boardPassers'])) {
                fputcsv($file, ['BOARD EXAM PASSERS']);
                fputcsv($file, ['Student ID', 'Full Name', 'Program', 'Exam Date', 'Result', 'Rating']);
                foreach ($data['tables']['boardPassers'] as $passer) {
                    fputcsv($file, [
                        $passer['student_id'] ?? 'N/A',
                        $passer['full_name'] ?? 'N/A',
                        $passer['program'] ?? 'N/A',
                        $passer['exam_date'] ?? 'N/A',
                        $passer['result'] ?? 'N/A',
                        $passer['rating'] ? $passer['rating'] . '%' : 'N/A'
                    ]);
                }
                fputcsv($file, []); // Empty row
            }

            // Write batch performance
            if (!empty($data['tables']['batchPerformance'])) {
                fputcsv($file, ['BATCH PERFORMANCE ANALYSIS']);
                fputcsv($file, ['Batch', 'Number of Students', 'Average Score', 'Pass Rate', 'Completion Rate', 'Status']);
                foreach ($data['tables']['batchPerformance'] as $batch) {
                    fputcsv($file, [
                        $batch['batch_name'] ?? 'N/A',
                        $batch['student_count'] ?? 0,
                        ($batch['average_score'] ?? 0) . '%',
                        ($batch['pass_rate'] ?? 0) . '%',
                        ($batch['completion_rate'] ?? 0) . '%',
                        $batch['status'] ?? 'N/A'
                    ]);
                }
                fputcsv($file, []); // Empty row
            }

            // Write metadata
            fputcsv($file, ['EXPORT INFORMATION']);
            fputcsv($file, ['Generated At', $data['generated_at']]);
            fputcsv($file, ['Exported By', $data['exported_by']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPDF($data)
    {
        return view('admin.admin-analytics.exports.pdf-report', $data);
    }

    public function exportComplete(Request $request)
    {
        // Check if user is admin only (not director)
        $userType = session('user_type');
        if (!$userType || $userType !== 'admin') {
            return response()->json(['error' => 'Access denied. Complete export functionality is restricted to admins only.'], 403);
        }

        try {
            $format = $request->get('format', 'csv');
            
            // Get comprehensive data
            $data = [
                'students' => $this->getAllStudentsData(),
                'programs' => $this->getAllProgramsData(),
                'quiz_results' => $this->getAllQuizResults(),
                'enrollments' => $this->getAllEnrollmentsData(),
                'board_passers' => $this->getAllBoardPassersData(),
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'exported_by' => session('user_name') ?? 'Admin'
            ];

            if ($format === 'csv') {
                return $this->exportCompleteToCSV($data);
            } else {
                return response()->json($data, 200, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="complete-analytics-export-' . now()->format('Y-m-d-H-i-s') . '.json"'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Complete export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export complete data: ' . $e->getMessage()], 500);
        }
    }

    private function exportCompleteToCSV($data)
    {
        $filename = 'complete-analytics-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Export metadata
            fputcsv($file, ['COMPLETE ANALYTICS EXPORT']);
            fputcsv($file, ['Generated At', $data['generated_at']]);
            fputcsv($file, ['Exported By', $data['exported_by']]);
            fputcsv($file, []);

            // Export students data
            if (!empty($data['students'])) {
                fputcsv($file, ['STUDENTS DATA']);
                fputcsv($file, ['ID', 'Name', 'Email', 'Program', 'Status', 'Registration Date']);
                foreach ($data['students'] as $student) {
                    fputcsv($file, [
                        $student['id'] ?? '',
                        $student['name'] ?? '',
                        $student['email'] ?? '',
                        $student['program'] ?? '',
                        $student['status'] ?? '',
                        $student['registration_date'] ?? ''
                    ]);
                }
                fputcsv($file, []);
            }

            // Export quiz results
            if (!empty($data['quiz_results'])) {
                fputcsv($file, ['QUIZ RESULTS']);
                fputcsv($file, ['Student ID', 'Student Name', 'Quiz Title', 'Score', 'Date Taken', 'Status']);
                foreach ($data['quiz_results'] as $result) {
                    fputcsv($file, [
                        $result['student_id'] ?? '',
                        $result['student_name'] ?? '',
                        $result['quiz_title'] ?? '',
                        $result['score'] ?? '',
                        $result['date_taken'] ?? '',
                        $result['status'] ?? ''
                    ]);
                }
                fputcsv($file, []);
            }

            // Export enrollments
            if (!empty($data['enrollments'])) {
                fputcsv($file, ['ENROLLMENTS']);
                fputcsv($file, ['Student ID', 'Student Name', 'Program', 'Batch', 'Status', 'Enrollment Date']);
                foreach ($data['enrollments'] as $enrollment) {
                    fputcsv($file, [
                        $enrollment['student_id'] ?? '',
                        $enrollment['student_name'] ?? '',
                        $enrollment['program_name'] ?? '',
                        $enrollment['batch_name'] ?? '',
                        $enrollment['status'] ?? '',
                        $enrollment['enrollment_date'] ?? ''
                    ]);
                }
                fputcsv($file, []);
            }

            // Export board passers
            if (!empty($data['board_passers'])) {
                fputcsv($file, ['BOARD EXAM PASSERS']);
                fputcsv($file, ['Student Name', 'Exam Year', 'Result', 'Rating', 'Program']);
                foreach ($data['board_passers'] as $passer) {
                    fputcsv($file, [
                        $passer['student_name'] ?? '',
                        $passer['exam_year'] ?? '',
                        $passer['result'] ?? '',
                        $passer['rating'] ?? '',
                        $passer['program'] ?? ''
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getAllStudentsData()
    {
        try {
            return DB::table('users')
                ->leftJoin('registrations', 'users.user_id', '=', 'registrations.user_id')
                ->leftJoin('programs', 'registrations.program_id', '=', 'programs.program_id')
                ->where('users.role', 'student')
                ->select(
                    'users.user_id as id',
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as name"),
                    'users.email',
                    'programs.program_name as program',
                    'registrations.status',
                    'users.created_at as registration_date'
                )
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting students data: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllProgramsData()
    {
        try {
            return Program::with(['modules'])
                ->select('program_id', 'program_name', 'created_at')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting programs data: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllQuizResults()
    {
        try {
            return DB::table('quiz_results')
                ->join('users', 'quiz_results.user_id', '=', 'users.user_id')
                ->join('quizzes', 'quiz_results.quiz_id', '=', 'quizzes.quiz_id')
                ->select(
                    'quiz_results.user_id as student_id',
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as student_name"),
                    'quizzes.quiz_title',
                    'quiz_results.score',
                    'quiz_results.created_at as date_taken',
                    DB::raw("CASE WHEN quiz_results.score >= 70 THEN 'Passed' ELSE 'Failed' END as status")
                )
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting quiz results: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllEnrollmentsData()
    {
        try {
            return DB::table('enrollments')
                ->join('users', 'enrollments.user_id', '=', 'users.user_id')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->leftJoin('batches', 'enrollments.batch_id', '=', 'batches.batch_id')
                ->select(
                    'enrollments.user_id as student_id',
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as student_name"),
                    'programs.program_name',
                    'batches.batch_name',
                    'enrollments.status',
                    'enrollments.created_at as enrollment_date'
                )
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting enrollments data: ' . $e->getMessage());
            return [];
        }
    }

    private function getAllBoardPassersData()
    {
        try {
            return DB::table('board_passers')
                ->select('student_name', 'exam_year', 'result', 'rating', 'program')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting board passers data: ' . $e->getMessage());
            return [];
        }
    }

    public function generateSubjectReport(Request $request)
    {
        try {
            $filters = $this->getFilters($request);
            $subjectData = $this->getSubjectAnalytics($filters);
            
            return view('admin.admin-analytics.exports.subject-report', [
                'subjects' => $subjectData,
                'filters' => $filters,
                'generated_at' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Subject report error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate subject report'], 500);
        }
    }

    public function uploadBoardPassers(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'exam_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'board_exam' => 'required|string|max:50',
            'other_exam' => 'nullable|string|max:100'
        ]);
        
        try {
            $file = $request->file('csv_file');
            $examType = $request->board_exam === 'OTHER' ? $request->other_exam : $request->board_exam;
            
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($csv as $record) {
                try {
                    // Find student by ID or name
                    $student = User::where('student_id', $record['Student ID'])
                        ->orWhere('name', 'LIKE', '%' . $record['Student Name'] . '%')
                        ->first();
                    
                    if (!$student) {
                        $errorCount++;
                        $errors[] = "Student not found: {$record['Student ID']} - {$record['Student Name']}";
                        continue;
                    }
                    
                    // Create or update board passer record
                    BoardPasser::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'board_exam' => $examType,
                            'exam_year' => $request->exam_year
                        ],
                        [
                            'exam_date' => isset($record['Exam Date']) ? $record['Exam Date'] : null,
                            'result' => strtoupper($record['Result']),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error processing {$record['Student ID']}: " . $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Upload completed. {$successCount} records processed successfully, {$errorCount} errors.",
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function addBoardPasser(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'board_exam' => 'required|string|max:50',
            'exam_date' => 'required|date',
            'result' => 'required|in:PASS,FAIL',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            // Get student name and program from database with proper joins
            $student = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('students.student_id', $request->student_id)
                ->select([
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as name"),
                    'students.program_name as student_program_name',
                    'programs.program_name as enrolled_program_name'
                ])
                ->orderBy('enrollments.created_at', 'desc') // Get the most recent enrollment
                ->first();
            
            $studentName = $student ? $student->name : 'Unknown Student';
            
            // Get program name with priority: enrolled program > student's program_name > null
            $programName = '';
            if ($student) {
                if ($student->enrolled_program_name) {
                    $programName = $student->enrolled_program_name;
                } elseif ($student->student_program_name) {
                    $programName = $student->student_program_name;
                }
            }
            
            // If still no program found, try to get it from the most recent enrollment
            if (empty($programName)) {
                $recentEnrollment = DB::table('enrollments')
                    ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                    ->where('enrollments.student_id', $request->student_id)
                    ->orderBy('enrollments.created_at', 'desc')
                    ->select('programs.program_name')
                    ->first();
                
                if ($recentEnrollment) {
                    $programName = $recentEnrollment->program_name;
                }
            }
            
            BoardPasser::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'board_exam' => $request->board_exam,
                    'exam_year' => date('Y', strtotime($request->exam_date))
                ],
                [
                    'student_name' => $studentName,
                    'program' => $programName ?: null,
                    'exam_date' => $request->exam_date,
                    'result' => $request->result,
                    'notes' => $request->notes,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Board passer entry saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save entry: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function downloadTemplate()
    {
        $headers = [
            'Student ID',
            'Student Name',
            'Program',
            'Batch',
            'Board Exam',
            'Exam Date',
            'Result'
        ];
        
        $sampleData = [
            ['STU001', 'John Doe', 'BS Accountancy', 'Batch 2024-A', 'CPA', '2024-05-15', 'PASS'],
            ['STU002', 'Jane Smith', 'BS Education', 'Batch 2024-B', 'LET', '2024-09-30', 'FAIL'],
            ['STU003', 'Mike Johnson', 'BS Civil Engineering', 'Batch 2024-A', 'CE', '2024-11-20', 'PASS']
        ];
        
        $csv = Writer::createFromString('');
        $csv->insertOne($headers);
        $csv->insertAll($sampleData);
        
        return response($csv->getContent())
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="board_passer_template.csv"');
    }
    
    public function getBoardPasserStats()
    {
        try {
            $totalPassers = BoardPasser::where('result', 'PASS')->count();
            $totalNonPassers = BoardPasser::where('result', 'FAIL')->count();
            $totalRecords = $totalPassers + $totalNonPassers;
            
            $passRate = $totalRecords > 0 ? round(($totalPassers / $totalRecords) * 100, 2) : 0;
            
            $lastUpdated = BoardPasser::latest('updated_at')->first();
            $lastUpdatedDate = $lastUpdated ? $lastUpdated->updated_at->format('M d, Y H:i') : null;
            
            return response()->json([
                'total_passers' => $totalPassers,
                'total_non_passers' => $totalNonPassers,
                'pass_rate' => $passRate,
                'last_updated' => $lastUpdatedDate
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'total_passers' => 0,
                'total_non_passers' => 0,
                'pass_rate' => 0,
                'last_updated' => null
            ]);
        }
    }
    
    public function getStudentsList()
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return response()->json(['error' => 'Access denied. Analytics is only available for admins and directors.'], 403);
        }

        try {
            // Use the correct database structure with students and users tables
            $students = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select(
                    'students.student_id as id',
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as name"),
                    'students.student_id',
                    'programs.program_name as program'
                )
                ->where('users.role', 'student')
                ->groupBy('students.student_id', 'users.user_firstname', 'users.user_lastname', 'programs.program_name')
                ->orderBy('users.user_firstname')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'student_id' => $student->student_id,
                        'program' => $student->program ?: 'N/A'
                    ];
                });
            
            return response()->json($students);
            
        } catch (\Exception $e) {
            Log::error('Get students list error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load students list'], 500);
        }
    }

    public function getBoardExams()
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return response()->json(['error' => 'Access denied. Analytics is only available for admins and directors.'], 403);
        }

        try {
            // Get distinct board exams from the database
            $exams = DB::table('board_passers')
                ->select('board_exam')
                ->distinct()
                ->whereNotNull('board_exam')
                ->where('board_exam', '!=', '')
                ->orderBy('board_exam')
                ->pluck('board_exam')
                ->toArray();

            // If no exams in database, provide default options based on common programs
            if (empty($exams)) {
                $exams = [
                    'NURSE' => 'Nursing Board Exam',
                    'CPA' => 'CPA (Certified Public Accountant)',
                    'LET' => 'LET (Licensure Examination for Teachers)',
                    'CE' => 'CE (Civil Engineer)',
                    'ME' => 'ME (Mechanical Engineer)',
                    'EE' => 'EE (Electrical Engineer)',
                    'OTHER' => 'Other'
                ];
            } else {
                // Convert to associative array with display names
                $examDisplayNames = [
                    'NURSE' => 'Nursing Board Exam',
                    'CPA' => 'CPA (Certified Public Accountant)',
                    'LET' => 'LET (Licensure Examination for Teachers)',
                    'CE' => 'CE (Civil Engineer)',
                    'ME' => 'ME (Mechanical Engineer)',
                    'EE' => 'EE (Electrical Engineer)',
                    'OTHER' => 'Other'
                ];
                
                $formattedExams = [];
                foreach ($exams as $exam) {
                    $formattedExams[$exam] = $examDisplayNames[$exam] ?? $exam;
                }
                $exams = $formattedExams;
            }
            
            return response()->json($exams);
            
        } catch (\Exception $e) {
            Log::error('Get board exams error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load board exams'], 500);
        }
    }

    private function getFilters(Request $request)
    {
        return [
            'year' => $request->get('year'),
            'month' => $request->get('month'),
            'program' => $request->get('program'),
            'batch' => $request->get('batch'),
            'subject' => $request->get('subject'),
            'period' => $request->get('period', 'monthly')
        ];
    }

    private function getMetrics($filters)
    {
        try {
            // Build base query for students with filters
            $studentsQuery = $this->buildStudentsQuery($filters);
            
            // Board Pass Rate
            $boardPassRate = $this->calculateBoardPassRate($filters);
            $boardPassTrend = $this->calculateTrend('board_pass', $filters);
            
            // Total Students
            $totalStudents = $studentsQuery->count();
            $studentsTrend = $this->calculateTrend('students', $filters);
            
            // Completion Rate
            $completionRate = $this->calculateCompletionRate($filters);
            $completionTrend = $this->calculateTrend('completion', $filters);

            return [
                'boardPassRate' => round($boardPassRate, 1),
                'totalStudents' => $totalStudents,
                'completionRate' => round($completionRate, 1),
                'boardPassTrend' => $boardPassTrend,
                'studentsTrend' => $studentsTrend,
                'completionTrend' => $completionTrend
            ];
        } catch (\Exception $e) {
            Log::error('Metrics calculation error: ' . $e->getMessage());
            return [
                'boardPassRate' => 0,
                'totalStudents' => 0,
                'completionRate' => 0,
                'boardPassTrend' => ['value' => 0, 'period' => 'this period'],
                'studentsTrend' => ['value' => 0, 'period' => 'this period'],
                'completionTrend' => ['value' => 0, 'period' => 'this period']
            ];
            
        
        }
    }

    private function getChartData($filters)
    {
        try {
            return [
                'boardPass' => $this->getBoardPassChartData($filters),
                'programDistribution' => $this->getProgramDistributionData($filters),
                'subjectPerformance' => $this->getSubjectPerformanceData($filters),
                'progressDistribution' => $this->getProgressDistributionData($filters),
                'batchPerformance' => $this->getBatchPerformanceData($filters)
            ];
        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return [
                'boardPass' => ['labels' => [], 'data' => []],
                'programDistribution' => ['labels' => [], 'data' => []],
                'subjectPerformance' => ['labels' => [], 'data' => []],
                'progressDistribution' => ['labels' => [], 'data' => []],
                'batchPerformance' => ['labels' => [], 'data' => []]
            ];
        }
    }

    private function getTableData($filters)
    {
        try {
            return [
                'topPerformers' => $this->getTopPerformers($filters),
                'subjectBreakdown' => $this->getSubjectBreakdown($filters),
                'recentlyEnrolled' => $this->getRecentlyEnrolled($filters),
                'recentPayments' => $this->getRecentPayments($filters),
                'recentlyCompleted' => $this->getRecentlyCompleted($filters),
                'boardPassers' => $this->getBoardPassers($filters),
                'batchPerformance' => $this->getBatchPerformance($filters)
            ];
        } catch (\Exception $e) {
            Log::error('Table data error: ' . $e->getMessage());
            return [
                'topPerformers' => [],
                'subjectBreakdown' => [],
                'recentlyEnrolled' => [],
                'recentPayments' => [],
                'recentlyCompleted' => [],
                'boardPassers' => [],
                'batchPerformance' => []
            ];
        }
    }

    private function buildStudentsQuery($filters)
    {
        $query = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.user_id')
            ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->where('users.role', 'student');

        if (!empty($filters['year'])) {
            $query->whereYear('enrollments.created_at', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('enrollments.created_at', $filters['month']);
        }

        if (!empty($filters['program'])) {
            // Map program filter to the actual program names
            if ($filters['program'] === 'full') {
                $query->where('programs.program_name', 'LIKE', '%Full%');
            } elseif ($filters['program'] === 'modular') {
                $query->where('programs.program_name', 'LIKE', '%Modular%');
            }
        }

        if (!empty($filters['batch'])) {
            $query->where('enrollments.batch_id', $filters['batch']);
        }

        return $query;
    }

    private function calculateBoardPassRate($filters)
    {
        try {
            // Check if we have actual student data
            $studentsQuery = $this->buildStudentsQuery($filters);
            $totalStudents = $studentsQuery->count();
            
            // If no students, return 0 instead of mock data
            if ($totalStudents == 0) return 0;
            
            // Check for actual board pass data
            if (DB::getSchemaBuilder()->hasTable('board_passers')) {
                $passedStudents = DB::table('board_passers')
                    ->where('result', 'PASS')
                    ->count();
                    
                $totalBoardPassers = DB::table('board_passers')->count();
                
                if ($totalBoardPassers > 0) {
                    return ($passedStudents / $totalBoardPassers) * 100;
                }
            }
            
            // If no board pass data available, return 0
            return 0;
        } catch (\Exception $e) {
            Log::error('Board pass rate calculation error: ' . $e->getMessage());
            return 0; // Return 0 instead of fake data
        }
    }

    private function calculateAverageQuizScore($filters)
    {
        try {
            // Try to get real quiz scores from different possible tables
            $avgScore = null;
            
            // Check for quiz_results table
            if (DB::getSchemaBuilder()->hasTable('quiz_results')) {
                $query = DB::table('quiz_results')
                    ->join('users', '', '=', 'users.user_id')
                    ->where('users.role', 'student');

                if (!empty($filters['year'])) {
                    $query->whereYear('quiz_results.created_at', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $query->whereMonth('quiz_results.created_at', $filters['month']);
                }

                if (!empty($filters['subject'])) {
                    $query->where('quiz_results.module_id', $filters['subject']);
                }

                $avgScore = $query->avg('quiz_results.score');
            }
            
            // Check for module_completions table if no quiz_results
            if (!$avgScore && DB::getSchemaBuilder()->hasTable('module_completions')) {
                $query = DB::table('module_completions')
                    ->join('users', '', '=', 'users.user_id')
                    ->where('users.role', 'student');

                if (!empty($filters['year'])) {
                    $query->whereYear('module_completions.completed_at', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $query->whereMonth('module_completions.completed_at', $filters['month']);
                }

                if (!empty($filters['subject'])) {
                    $query->where('module_completions.modules_id', $filters['subject']);
                }

                $avgScore = $query->avg('module_completions.score');
            }
            
            // Check for quiz_answers table
            if (!$avgScore && DB::getSchemaBuilder()->hasTable('quiz_answers')) {
                $query = DB::table('quiz_answers')
                    ->join('users', '', '=', 'users.user_id')
                    ->where('users.role', 'student');

                if (!empty($filters['year'])) {
                    $query->whereYear('quiz_answers.created_at', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $query->whereMonth('quiz_answers.created_at', $filters['month']);
                }

                if (!empty($filters['subject'])) {
                    $query->where('quiz_answers.module_id', $filters['subject']);
                }

                // Calculate average score from quiz_answers
                $totalQuestions = $query->count();
                $correctAnswers = $query->where('is_correct', true)->count();
                
                if ($totalQuestions > 0) {
                    $avgScore = ($correctAnswers / $totalQuestions) * 100;
                }
            }
            
            return $avgScore ?? 0; // Return 0 when no data instead of fake score
        } catch (\Exception $e) {
            Log::error('Average quiz score calculation error: ' . $e->getMessage());
            return 0; // Return 0 instead of fake data
        }
    }

    private function calculateCompletionRate($filters)
    {
        try {
            // Get total number of active students
            $totalStudents = DB::table('users')
                ->where('role', 'student')
                ->count();
            
            if ($totalStudents == 0) return 0;
            
            // Get total number of active modules
            $totalModules = DB::table('modules')
                ->where('is_archived', false)
                ->count();
            
            if ($totalModules == 0) return 0;
            
            // Calculate expected completions
            $totalExpectedCompletions = $totalStudents * $totalModules;
            
            // Get actual completions from module_completions table
            $actualCompletions = 0;
            if (DB::getSchemaBuilder()->hasTable('module_completions')) {
                $query = DB::table('module_completions')
                    ->join('users', '', '=', 'users.user_id')
                    ->where('users.role', 'student');

                if (!empty($filters['year'])) {
                    $query->whereYear('module_completions.completed_at', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $query->whereMonth('module_completions.completed_at', $filters['month']);
                }

                if (!empty($filters['subject'])) {
                    $query->where('module_completions.modules_id', $filters['subject']);
                }

                $actualCompletions = $query->count();
            }
            
            // If no module_completions, check user_progress table
            if ($actualCompletions == 0 && DB::getSchemaBuilder()->hasTable('user_progress')) {
                $query = DB::table('user_progress')
                    ->join('users', '', '=', 'users.user_id')
                    ->where('users.role', 'student')
                    ->where('user_progress.is_completed', true);

                if (!empty($filters['year'])) {
                    $query->whereYear('user_progress.updated_at', $filters['year']);
                }

                if (!empty($filters['month'])) {
                    $query->whereMonth('user_progress.updated_at', $filters['month']);
                }

                if (!empty($filters['subject'])) {
                    $query->where('user_progress.module_id', $filters['subject']);
                }

                $actualCompletions = $query->count();
            }
            
            // Calculate completion rate
            $completionRate = ($actualCompletions / $totalExpectedCompletions) * 100;
            return min($completionRate, 100); // Cap at 100%
        } catch (\Exception $e) {
            Log::error('Completion rate calculation error: ' . $e->getMessage());
            return 0; // Default fallback
        }
    }

    private function calculateTrend($metric, $filters)
    {
        try {
            // First check if we have any students - if not, return zero trends
            $studentCount = DB::table('students')->count();
            if ($studentCount == 0) {
                return ['value' => 0, 'period' => 'no data available'];
            }
            
            // TODO: Implement actual trend calculation based on historical data
            // For now, return neutral trend when students exist but no historical data
            return ['value' => 0, 'period' => 'this period'];
        } catch (\Exception $e) {
            Log::error('Trend calculation error: ' . $e->getMessage());
            return ['value' => 0, 'period' => 'this period'];
        }
    }

    private function getBoardPassChartData($filters)
    {
        try {
            $labels = [];
            $data = [];
            
            // Get real board pass data from board_passers table
            if (DB::getSchemaBuilder()->hasTable('board_passers')) {
                // Get data for last 12 months
                $monthlyData = DB::table('board_passers')
                    ->select(
                        DB::raw('YEAR(exam_date) as year'),
                        DB::raw('MONTH(exam_date) as month'),
                        DB::raw('COUNT(*) as total_passers')
                    )
                    ->where('exam_date', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'asc')
                    ->orderBy('month', 'asc')
                    ->get();
                
                // Create array for last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    
                    // Find data for this month
                    $monthData = $monthlyData->where('year', $date->year)
                        ->where('month', $date->month)
                        ->first();
                    
                    $data[] = $monthData ? $monthData->total_passers : 0;
                }
            } else {
                // Generate mock data if no board_passers table
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    $data[] = rand(65, 95); // Mock pass rates between 65-95%
                }
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Board pass chart data error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getProgramDistributionData($filters)
    {
        try {
            $query = $this->buildStudentsQuery($filters);
            
            $distribution = $query->join('registrations', 'users.user_id', '=', 'registrations.user_id')
                ->select('registrations.enrollment_type', DB::raw('count(*) as count'))
                ->groupBy('registrations.enrollment_type')
                ->get();
            
            $labels = [];
            $data = [];
            
            foreach ($distribution as $item) {
                $labels[] = ucfirst($item->enrollment_type ?? 'Unknown');
                $data[] = $item->count;
            }
            
            // Return empty data if no results instead of fake data
            if (empty($labels)) {
                $labels = [];
                $data = [];
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Program distribution data error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getSubjectPerformanceData($filters)
    {
        try {
            $subjects = DB::table('modules')
                ->where('is_archived', false)
                ->take(10)
                ->get();
            
            $labels = [];
            $data = [];
            
            foreach ($subjects as $subject) {
                $labels[] = $subject->module_name;
                
                // Get real performance data for this module
                $performance = 0;
                
                // Check quiz_results table
                if (DB::getSchemaBuilder()->hasTable('quiz_results')) {
                    $avgScore = DB::table('quiz_results')
                        ->where('module_id', $subject->module_id)
                        ->avg('score');
                    if ($avgScore) {
                        $performance = round($avgScore, 1);
                    }
                }
                
                // Check module_completions table if no quiz_results
                if ($performance == 0 && DB::getSchemaBuilder()->hasTable('module_completions')) {
                    $avgScore = DB::table('module_completions')
                        ->where('modules_id', $subject->modules_id)
                        ->avg('score');
                    if ($avgScore) {
                        $performance = round($avgScore, 1);
                    }
                }
                
                // Don't use mock data - use 0 if no real data
                if ($performance == 0) {
                    $performance = 0;
                }
                
                $data[] = $performance;
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Subject performance data error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getProgressDistributionData($filters)
    {
        try {
            // Get actual student counts by program instead of fake progress data
            $studentCount = DB::table('students')->count();
            if ($studentCount == 0) {
                return [
                    'labels' => [],
                    'data' => []
                ];
            }
            
            // Get student distribution by program
            $query = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('users.role', 'student');
                
            // Apply filters
            if (!empty($filters['year'])) {
                $query->whereYear('enrollments.created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('enrollments.created_at', $filters['month']);
            }
            
            $distribution = $query->select('programs.program_name', DB::raw('count(DISTINCT students.student_id) as student_count'))
                ->groupBy('programs.program_name')
                ->get();
            
            $labels = [];
            $data = [];
            
            foreach ($distribution as $item) {
                $programName = $item->program_name ?? 'No Program';
                $labels[] = $programName;
                $data[] = $item->student_count;
            }
            
            // If no program-based distribution, show total student count
            if (empty($labels)) {
                $labels = ['Total Students'];
                $data = [$studentCount];
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Progress distribution data error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getBatchPerformanceData($filters)
    {
        try {
            // Check if batches table exists and if we have student data
            if (!DB::getSchemaBuilder()->hasTable('batches')) {
                return ['labels' => [], 'data' => []];
            }
            
            $studentCount = DB::table('students')->count();
            if ($studentCount == 0) {
                return ['labels' => [], 'data' => []];
            }
            
            $batches = DB::table('batches')->take(10)->get();
            
            $labels = [];
            $data = [];
            
            foreach ($batches as $batch) {
                $labels[] = $batch->batch_name ?? 'Unknown Batch';
                // TODO: Calculate actual performance data
                $data[] = 0; // Return 0 instead of fake data
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Batch performance data error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getTopPerformers($filters)
    {
        try {
            $students = $this->buildStudentsQuery($filters)
                ->with('registration')
                ->take(5)
                ->get();
            
            $performers = [];
            
            foreach ($students as $student) {
                $performers[] = [
                    'id' => $student->user_id,
                    'name' => ($student->user_firstname ?? '') . ' ' . ($student->user_lastname ?? ''),
                    'email' => $student->email,
                    'program' => $student->registration->enrollment_type ?? 'N/A',
                    'score' => rand(85, 98), // Mock score
                    'progress' => rand(80, 100) // Mock progress
                ];
            }
            
            return $performers;
        } catch (\Exception $e) {
            Log::error('Top performers data error: ' . $e->getMessage());
            return [];
        }
    }



    private function getSubjectBreakdown($filters)
    {
        try {
            // First check if we have any students
            $studentCount = DB::table('students')->count();
            if ($studentCount == 0) {
                return [];
            }
            
            $subjects = DB::table('modules')->take(10)->get();
            
            $breakdown = [];
            
            foreach ($subjects as $subject) {
                $breakdown[] = [
                    'id' => $subject->modules_id ?? $subject->module_id,
                    'name' => $subject->module_name,
                    'totalStudents' => 0, // TODO: Calculate actual student count per module
                    'avgScore' => 0,     // TODO: Calculate actual average score
                    'passRate' => 0,     // TODO: Calculate actual pass rate  
                    'difficulty' => 'Unknown', // TODO: Calculate difficulty from data
                    'trend' => 0         // TODO: Calculate actual trend
                ];
            }
            
            return $breakdown;
        } catch (\Exception $e) {
            Log::error('Subject breakdown data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getSubjectAnalytics($filters)
    {
        try {
            // Implementation for detailed subject analytics
            return $this->getSubjectBreakdown($filters);
        } catch (\Exception $e) {
            Log::error('Subject analytics error: ' . $e->getMessage());
            return [];
        }
    }

    private function getRecentlyEnrolled($filters)
    {
        try {
            // Get recently enrolled students from enrollments table
            $query = DB::table('enrollments')
                ->join('students', 'enrollments.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname', 
                    'users.email',
                    'students.student_id',
                    'programs.program_name',
                    'enrollments.enrollment_type',
                    'enrollments.created_at as enrollment_date',
                    'enrollments.enrollment_status'
                ])
                ->where('enrollments.enrollment_status', '!=', 'rejected')
                ->orderBy('enrollments.created_at', 'desc')
                ->limit(10);

            // Apply filters
            if (!empty($filters['year'])) {
                $query->whereYear('enrollments.created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('enrollments.created_at', $filters['month']);
            }
            if (!empty($filters['program'])) {
                if ($filters['program'] === 'full') {
                    $query->where('enrollments.enrollment_type', 'Full');
                } elseif ($filters['program'] === 'modular') {
                    $query->where('enrollments.enrollment_type', 'Modular');
                }
            }

            $enrollments = $query->get();

            $result = [];
            foreach ($enrollments as $enrollment) {
                $result[] = [
                    'name' => trim(($enrollment->user_firstname ?? '') . ' ' . ($enrollment->user_lastname ?? '')),
                    'email' => $enrollment->email ?? 'N/A',
                    'student_id' => $enrollment->student_id,
                    'program' => $enrollment->program_name ?? 'Unknown Program',
                    'plan' => $enrollment->enrollment_type ?? 'N/A',
                    'enrollment_date' => $enrollment->enrollment_date ? 
                        Carbon::parse($enrollment->enrollment_date)->format('M d, Y') : 'N/A',
                    'status' => ucfirst($enrollment->enrollment_status ?? 'pending')
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Recently enrolled data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getRecentPayments($filters)
    {
        try {
            // Use raw query to avoid collation issues
            $sql = "
                SELECT p.student_id, p.amount, p.created_at as payment_date,
                       p.payment_status, p.payment_method,
                       u.user_firstname, u.user_lastname,
                       pr.program_name
                FROM payments p
                LEFT JOIN students s ON CAST(p.student_id AS CHAR) = CAST(s.student_id AS CHAR)
                LEFT JOIN users u ON s.user_id = u.user_id
                LEFT JOIN enrollments e ON CAST(s.student_id AS CHAR) = CAST(e.student_id AS CHAR)
                LEFT JOIN programs pr ON e.program_id = pr.program_id
                WHERE p.payment_status IN ('paid', 'approved', 'verified')
            ";
            
            // Apply filters
            $params = [];
            if (!empty($filters['year'])) {
                $sql .= " AND YEAR(p.created_at) = ?";
                $params[] = $filters['year'];
            }
            if (!empty($filters['month'])) {
                $sql .= " AND MONTH(p.created_at) = ?";
                $params[] = $filters['month'];
            }
            
            $sql .= " ORDER BY p.created_at DESC LIMIT 10";
            
            $payments = DB::select($sql, $params);

            $result = [];
            foreach ($payments as $payment) {
                $studentName = trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? ''));
                $programName = $payment->program_name ?? 'Unknown Program';
                
                // Clean up weird names or use student ID if name is missing/invalid
                if (empty($studentName) || preg_match('/^\d+\s+\d+$/', $studentName)) {
                    $studentName = 'Student ' . $payment->student_id;
                }
                
                $result[] = [
                    'student_name' => $studentName,
                    'student_id' => $payment->student_id,
                    'program' => $programName,
                    'amount' => number_format($payment->amount ?? 0, 2),
                    'payment_date' => $payment->payment_date ? 
                        Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A',
                    'status' => ucfirst($payment->payment_status ?? 'pending'),
                    'payment_method' => ucfirst($payment->payment_method ?? 'N/A')
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Recent payments data error: ' . $e->getMessage());
            
            // Simple fallback - just get payments without student info
            try {
                $payments = DB::table('payments')
                    ->select(['student_id', 'amount', 'created_at as payment_date', 'payment_status'])
                    ->whereIn('payment_status', ['paid', 'approved', 'verified'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                
                $result = [];
                foreach ($payments as $payment) {
                    $result[] = [
                        'student_name' => 'Student ' . $payment->student_id,
                        'student_id' => $payment->student_id,
                        'program' => 'N/A',
                        'amount' => number_format($payment->amount ?? 0, 2),
                        'payment_date' => $payment->payment_date ? 
                            Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A',
                        'status' => ucfirst($payment->payment_status ?? 'pending'),
                        'payment_method' => 'N/A'
                    ];
                }
                return $result;
            } catch (\Exception $fallbackE) {
                Log::error('Payment fallback error: ' . $fallbackE->getMessage());
            }
            
            return [];
        }
    }

    private function getRecentlyCompleted($filters)
    {
        try {
            $studentCompletions = [];
            
            // Get recent module completions with simplified joins
            $moduleCompletions = DB::table('module_completions')
                ->join('students', 'module_completions.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('modules', 'module_completions.modules_id', '=', 'modules.modules_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname',
                    'users.email',
                    'students.student_id',
                    'students.program_name',
                    'students.enrollment_type',
                    'modules.module_name',
                    'module_completions.completed_at as completion_date'
                ])
                ->whereNotNull('module_completions.completed_at');

            // Apply filters
            if (!empty($filters['year'])) {
                $moduleCompletions->whereYear('module_completions.completed_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $moduleCompletions->whereMonth('module_completions.completed_at', $filters['month']);
            }
            if (!empty($filters['program'])) {
                $moduleCompletions->where('students.enrollment_type', $filters['program']);
            }

            $moduleCompletions = $moduleCompletions->orderBy('module_completions.completed_at', 'desc')
                ->get();

            // Group module completions by student
            foreach ($moduleCompletions as $completion) {
                $studentId = $completion->student_id;
                $studentName = trim(($completion->user_firstname ?? '') . ' ' . ($completion->user_lastname ?? ''));
                
                if (!isset($studentCompletions[$studentId])) {
                    $studentCompletions[$studentId] = [
                        'name' => $studentName ?: $studentId,
                        'email' => $completion->email ?? 'N/A',
                        'student_id' => $studentId,
                        'program' => $completion->program_name ?? 'N/A',
                        'plan' => $completion->enrollment_type ?? 'N/A',
                        'completion_date' => $completion->completion_date ? 
                            Carbon::parse($completion->completion_date)->format('M d, Y') : 'N/A',
                        'modules' => [],
                        'courses' => [],
                        'last_completion' => $completion->completion_date
                    ];
                }
                
                if ($completion->module_name) {
                    $studentCompletions[$studentId]['modules'][] = $completion->module_name;
                }
                
                // Update last completion date
                if ($completion->completion_date > $studentCompletions[$studentId]['last_completion']) {
                    $studentCompletions[$studentId]['last_completion'] = $completion->completion_date;
                    $studentCompletions[$studentId]['completion_date'] = Carbon::parse($completion->completion_date)->format('M d, Y');
                }
            }

            // Get recent course completions with simplified joins
            $courseCompletions = DB::table('course_completions')
                ->join('students', 'course_completions.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('courses', 'course_completions.course_id', '=', 'courses.subject_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname',
                    'users.email',
                    'students.student_id',
                    'students.program_name',
                    'students.enrollment_type',
                    'courses.subject_name as course_name',
                    'course_completions.completed_at as completion_date'
                ])
                ->whereNotNull('course_completions.completed_at');

            // Apply filters
            if (!empty($filters['year'])) {
                $courseCompletions->whereYear('course_completions.completed_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $courseCompletions->whereMonth('course_completions.completed_at', $filters['month']);
            }
            if (!empty($filters['program'])) {
                $courseCompletions->where('students.enrollment_type', $filters['program']);
            }

            $courseCompletions = $courseCompletions->orderBy('course_completions.completed_at', 'desc')
                ->get();

            // Group course completions by student
            foreach ($courseCompletions as $completion) {
                $studentId = $completion->student_id;
                $studentName = trim(($completion->user_firstname ?? '') . ' ' . ($completion->user_lastname ?? ''));
                
                if (!isset($studentCompletions[$studentId])) {
                    $studentCompletions[$studentId] = [
                        'name' => $studentName ?: $studentId,
                        'email' => $completion->email ?? 'N/A',
                        'student_id' => $studentId,
                        'program' => $completion->program_name ?? 'N/A',
                        'plan' => $completion->enrollment_type ?? 'N/A',
                        'completion_date' => $completion->completion_date ? 
                            Carbon::parse($completion->completion_date)->format('M d, Y') : 'N/A',
                        'modules' => [],
                        'courses' => [],
                        'last_completion' => $completion->completion_date
                    ];
                }
                
                if ($completion->course_name) {
                    $studentCompletions[$studentId]['courses'][] = $completion->course_name;
                }
                
                // Update last completion date
                if ($completion->completion_date > $studentCompletions[$studentId]['last_completion']) {
                    $studentCompletions[$studentId]['last_completion'] = $completion->completion_date;
                    $studentCompletions[$studentId]['completion_date'] = Carbon::parse($completion->completion_date)->format('M d, Y');
                }
            }

            // Convert to final format
            $result = [];
            foreach ($studentCompletions as $student) {
                $completedItems = [];
                
                // Add modules
                if (!empty($student['modules'])) {
                    $modules = array_unique($student['modules']);
                    $completedItems[] = 'Modules: ' . implode(', ', $modules);
                }
                
                // Add courses
                if (!empty($student['courses'])) {
                    $courses = array_unique($student['courses']);
                    $completedItems[] = 'Courses: ' . implode(', ', $courses);
                }
                
                $result[] = [
                    'name' => $student['name'],
                    'email' => $student['email'],
                    'student_id' => $student['student_id'],
                    'program' => $student['program'],
                    'plan' => $student['plan'],
                    'completion_date' => $student['completion_date'],
                    'final_score' => !empty($completedItems) ? implode(' | ', $completedItems) : 'No completions'
                ];
            }

            // Sort by completion date and limit to 10 most recent
            usort($result, function($a, $b) {
                $dateA = $a['completion_date'] !== 'N/A' ? strtotime($a['completion_date']) : 0;
                $dateB = $b['completion_date'] !== 'N/A' ? strtotime($b['completion_date']) : 0;
                return $dateB - $dateA;
            });

            return array_slice($result, 0, 10);
            
        } catch (\Exception $e) {
            Log::error('Recently completed data error: ' . $e->getMessage());
            return [];
            return array_slice($result, 0, 10);
            
        } catch (\Exception $e) {
            Log::error('Recently completed data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getBoardPassers($filters)
    {
        try {
            // Query board_passers table directly since it contains all the data we need
            $query = DB::table('board_passers')
                ->select([
                    'student_id',
                    'student_name',
                    'program',
                    'board_exam',
                    'exam_date',
                    'exam_year',
                    'result',
                    'rating'
                ])
                ->orderBy('exam_date', 'desc')
                ->limit(20);

            // Apply filters only if they exist
            if (!empty($filters['year'])) {
                $query->whereYear('exam_date', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('exam_date', $filters['month']);
            }

            $passers = $query->get();

            $result = [];
            foreach ($passers as $passer) {
                // Get student name
                $studentName = $passer->student_name ?: $passer->student_id;
                
                // Get program name
                $programName = $passer->program ?: 'N/A';
                
                // Get exam year
                $examYear = $passer->exam_year ?: 'N/A';

                $result[] = [
                    'student_id' => $passer->student_id,
                    'full_name' => $studentName,
                    'program_name' => $programName,
                    'board_exam' => $passer->board_exam,
                    'exam_date' => $passer->exam_date ? 
                        Carbon::parse($passer->exam_date)->format('M d, Y') : 'N/A',
                    'exam_year' => $examYear,
                    'result' => $passer->result,
                    'rating' => $passer->rating ? number_format($passer->rating, 1) : 'N/A'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Board passers data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getBatchPerformance($filters)
    {
        try {
            // First check if we have actual student data
            $studentCount = DB::table('students')->count();
            $enrollmentCount = DB::table('enrollments')->count();
            
            // If no students or enrollments, return empty data
            if ($studentCount == 0 || $enrollmentCount == 0) {
                return [];
            }
            
            // Check if batches table exists
            if (!DB::getSchemaBuilder()->hasTable('batches')) {
                // No batches table and no students - return empty
                return [];
            }

            $query = DB::table('batches')
                ->leftJoin('enrollments', 'batches.batch_id', '=', 'enrollments.batch_id')
                ->leftJoin('students', 'enrollments.student_id', '=', 'students.student_id')
                ->select([
                    'batches.batch_name',
                    'batches.batch_id',
                    DB::raw('COUNT(DISTINCT enrollments.student_id) as student_count'),
                    DB::raw('AVG(enrollments.progress_percentage) as average_score'),
                    DB::raw('COUNT(CASE WHEN enrollments.enrollment_status = "completed" THEN 1 END) * 100.0 / COUNT(DISTINCT enrollments.student_id) as completion_rate'),
                    DB::raw('COUNT(CASE WHEN enrollments.progress_percentage >= 75 THEN 1 END) * 100.0 / COUNT(DISTINCT enrollments.student_id) as pass_rate'),
                    'batches.status'
                ])
                ->groupBy('batches.batch_id', 'batches.batch_name', 'batches.status')
                ->orderBy('batches.batch_name')
                ->limit(15);

            // Apply filters
            if (!empty($filters['year'])) {
                $query->whereYear('enrollments.created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('enrollments.created_at', $filters['month']);
            }

            $batches = $query->get();

            $result = [];
            foreach ($batches as $batch) {
                $result[] = [
                    'batch_name' => $batch->batch_name ?? 'Unknown Batch',
                    'student_count' => $batch->student_count ?? 0,
                    'average_score' => round($batch->average_score ?? 0, 1),
                    'pass_rate' => round($batch->pass_rate ?? 0, 1),
                    'completion_rate' => round($batch->completion_rate ?? 0, 1),
                    'status' => $batch->status ?? 'Active'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Batch performance data error: ' . $e->getMessage());
            return [];
        }
    }
}
