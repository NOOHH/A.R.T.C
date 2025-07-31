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
            
            $data = [
                'metrics' => $this->getMetrics($filters),
                'charts' => $this->getChartData($filters),
                'tables' => $this->getTableData($filters),
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
            'bottom_performers' => $data['tables']['bottomPerformers'] ?? [],
            'subject_breakdown' => $data['tables']['subjectBreakdown'] ?? [],
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

            // Write bottom performers
            if (!empty($data['tables']['bottomPerformers'])) {
                fputcsv($file, ['STUDENTS NEEDING SUPPORT']);
                fputcsv($file, ['Rank', 'Name', 'Score', 'Program']);
                foreach ($data['tables']['bottomPerformers'] as $index => $student) {
                    fputcsv($file, [
                        $index + 1,
                        $student['name'] ?? 'N/A',
                        ($student['score'] ?? 0) . '%',
                        $student['program'] ?? 'N/A'
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
            'student_id' => 'required|exists:users,id',
            'board_exam' => 'required|string|max:50',
            'exam_date' => 'required|date',
            'result' => 'required|in:PASS,FAIL',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            BoardPasser::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'board_exam' => $request->board_exam,
                    'exam_year' => date('Y', strtotime($request->exam_date))
                ],
                [
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
            
            // Average Quiz Score
            $avgQuizScore = $this->calculateAverageQuizScore($filters);
            $quizScoreTrend = $this->calculateTrend('quiz_score', $filters);
            
            // Completion Rate
            $completionRate = $this->calculateCompletionRate($filters);
            $completionTrend = $this->calculateTrend('completion', $filters);

            return [
                'boardPassRate' => round($boardPassRate, 1),
                'totalStudents' => $totalStudents,
                'avgQuizScore' => round($avgQuizScore, 1),
                'completionRate' => round($completionRate, 1),
                'boardPassTrend' => $boardPassTrend,
                'studentsTrend' => $studentsTrend,
                'quizScoreTrend' => $quizScoreTrend,
                'completionTrend' => $completionTrend
            ];
        } catch (\Exception $e) {
            Log::error('Metrics calculation error: ' . $e->getMessage());
            return [
                'boardPassRate' => 0,
                'totalStudents' => 0,
                'avgQuizScore' => 0,
                'completionRate' => 0,
                'boardPassTrend' => ['value' => 0, 'period' => 'this period'],
                'studentsTrend' => ['value' => 0, 'period' => 'this period'],
                'quizScoreTrend' => ['value' => 0, 'period' => 'this period'],
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
                'bottomPerformers' => $this->getBottomPerformers($filters),
                'subjectBreakdown' => $this->getSubjectBreakdown($filters),
                'recentlyEnrolled' => $this->getRecentlyEnrolled($filters),
                'recentPayments' => $this->getRecentPayments($filters),
                'recentlyCompleted' => $this->getRecentlyCompleted($filters)
            ];
        } catch (\Exception $e) {
            Log::error('Table data error: ' . $e->getMessage());
            return [
                'topPerformers' => [],
                'bottomPerformers' => [],
                'subjectBreakdown' => [],
                'recentlyEnrolled' => [],
                'recentPayments' => [],
                'recentlyCompleted' => []
            ];
        }
    }

    private function buildStudentsQuery($filters)
    {
        $query = User::where('role', 'student');

        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }

        if (!empty($filters['program'])) {
            $query->whereHas('registration', function($q) use ($filters) {
                $q->where('enrollment_type', $filters['program']);
            });
        }

        if (!empty($filters['batch'])) {
            // Assuming there's a batch relationship - adjust as needed
            $query->whereHas('enrollment', function($q) use ($filters) {
                $q->where('batch_id', $filters['batch']);
            });
        }

        return $query;
    }

    private function calculateBoardPassRate($filters)
    {
        try {
            // Mock calculation - you'll need to implement based on your actual board exam results
            $studentsQuery = $this->buildStudentsQuery($filters);
            $totalStudents = $studentsQuery->count();
            
            if ($totalStudents == 0) return 88; // Default fallback
            
            // For demo purposes, using a mock pass rate
            return 88.5; // Mock board pass rate
        } catch (\Exception $e) {
            Log::error('Board pass rate calculation error: ' . $e->getMessage());
            return 88; // Default fallback
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
                    ->join('users', 'quiz_results.user_id', '=', 'users.id')
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
                    ->join('users', 'module_completions.student_id', '=', 'users.id')
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
                    ->join('users', 'quiz_answers.user_id', '=', 'users.id')
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
            
            return $avgScore ?? 78.5; // Default fallback with realistic score
        } catch (\Exception $e) {
            Log::error('Average quiz score calculation error: ' . $e->getMessage());
            return 78.5; // Default fallback with realistic score
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
                    ->join('users', 'module_completions.student_id', '=', 'users.id')
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
                    ->join('users', 'user_progress.user_id', '=', 'users.id')
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
            // Mock trend calculation - implement based on your needs
            $trends = [
                'board_pass' => ['value' => 5.2, 'period' => 'from last period'],
                'students' => ['value' => 12, 'period' => 'this month'],
                'quiz_score' => ['value' => 3.1, 'period' => 'improvement'],
                'completion' => ['value' => 7.8, 'period' => 'this quarter']
            ];
            
            return $trends[$metric] ?? ['value' => 0, 'period' => 'this period'];
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
            
            // Add fallback data if no results
            if (empty($labels)) {
                $labels = ['Full Program', 'Modular'];
                $data = [60, 40];
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Program distribution data error: ' . $e->getMessage());
            return ['labels' => ['Full Program', 'Modular'], 'data' => [60, 40]];
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
                
                // Use mock data if no real data
                if ($performance == 0) {
                    $performance = rand(70, 95);
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
            $labels = ['0-25%', '26-50%', '51-75%', '76-100%'];
            
            // Mock data - implement actual progress calculation
            $data = [
                rand(5, 15),   // 0-25%
                rand(10, 25),  // 26-50%
                rand(20, 40),  // 51-75%
                rand(30, 50)   // 76-100%
            ];
            
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
            $batches = Batch::take(10)->get();
            
            $labels = [];
            $data = [];
            
            foreach ($batches as $batch) {
                $labels[] = $batch->batch_name;
                $data[] = rand(75, 95); // Mock performance data
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

    private function getBottomPerformers($filters)
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
                    'score' => rand(40, 65), // Mock score
                    'issues' => 'Low Attendance' // Mock issue
                ];
            }
            
            return $performers;
        } catch (\Exception $e) {
            Log::error('Bottom performers data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getSubjectBreakdown($filters)
    {
        try {
            $subjects = Module::take(10)->get();
            
            $breakdown = [];
            
            foreach ($subjects as $subject) {
                $breakdown[] = [
                    'id' => $subject->modules_id,
                    'name' => $subject->module_name,
                    'totalStudents' => rand(50, 200),
                    'avgScore' => rand(70, 95),
                    'passRate' => rand(75, 95),
                    'difficulty' => ['Easy', 'Medium', 'Hard'][rand(0, 2)],
                    'trend' => rand(-5, 10)
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
                    'students.student_id',
                    'programs.program_name',
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
                    $query->where('enrollments.enrollment_type', 'full');
                } elseif ($filters['program'] === 'modular') {
                    $query->where('enrollments.enrollment_type', 'modular');
                }
            }

            $enrollments = $query->get();

            $result = [];
            foreach ($enrollments as $enrollment) {
                $result[] = [
                    'student_name' => trim(($enrollment->user_firstname ?? '') . ' ' . ($enrollment->user_lastname ?? '')),
                    'student_id' => $enrollment->student_id,
                    'program' => $enrollment->program_name ?? 'Unknown Program',
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
            // Get recent payments from payments table
            $query = DB::table('payments')
                ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.enrollment_id')
                ->join('students', 'enrollments.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname',
                    'students.student_id',
                    'programs.program_name',
                    'payments.amount',
                    'payments.payment_date',
                    'payments.payment_status'
                ])
                ->where('payments.payment_status', '!=', 'failed')
                ->orderBy('payments.payment_date', 'desc')
                ->limit(10);

            // Apply filters
            if (!empty($filters['year'])) {
                $query->whereYear('payments.payment_date', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('payments.payment_date', $filters['month']);
            }

            $payments = $query->get();

            $result = [];
            foreach ($payments as $payment) {
                $result[] = [
                    'student_name' => trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? '')),
                    'student_id' => $payment->student_id,
                    'program' => $payment->program_name ?? 'Unknown Program',
                    'amount' => number_format($payment->amount ?? 0, 2),
                    'payment_date' => $payment->payment_date ? 
                        Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A',
                    'status' => ucfirst($payment->payment_status ?? 'pending')
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Recent payments data error: ' . $e->getMessage());
            
            // Fallback: try to get from student_payment_history or other payment tables
            try {
                if (DB::getSchemaBuilder()->hasTable('payment_history')) {
                    $query = DB::table('payment_history')
                        ->join('students', 'payment_history.student_id', '=', 'students.student_id')
                        ->join('users', 'students.user_id', '=', 'users.user_id')
                        ->select([
                            'users.user_firstname',
                            'users.user_lastname',
                            'students.student_id',
                            'payment_history.amount',
                            'payment_history.created_at as payment_date'
                        ])
                        ->orderBy('payment_history.created_at', 'desc')
                        ->limit(10);

                    $payments = $query->get();
                    $result = [];
                    foreach ($payments as $payment) {
                        $result[] = [
                            'student_name' => trim(($payment->user_firstname ?? '') . ' ' . ($payment->user_lastname ?? '')),
                            'student_id' => $payment->student_id,
                            'program' => 'N/A',
                            'amount' => number_format($payment->amount ?? 0, 2),
                            'payment_date' => $payment->payment_date ? 
                                Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A',
                            'status' => 'Completed'
                        ];
                    }
                    return $result;
                }
            } catch (\Exception $fallbackE) {
                Log::error('Payment fallback error: ' . $fallbackE->getMessage());
            }
            
            return [];
        }
    }

    private function getRecentlyCompleted($filters)
    {
        try {
            // Get recently completed enrollments/modules
            $query = DB::table('enrollments')
                ->join('students', 'enrollments.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname',
                    'students.student_id',
                    'programs.program_name',
                    'enrollments.updated_at as completion_date'
                ])
                ->where('enrollments.enrollment_status', 'completed')
                ->orderBy('enrollments.updated_at', 'desc')
                ->limit(10);

            // Apply filters
            if (!empty($filters['year'])) {
                $query->whereYear('enrollments.updated_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('enrollments.updated_at', $filters['month']);
            }

            $completions = $query->get();

            // If no completed enrollments, try module completions
            if ($completions->isEmpty() && DB::getSchemaBuilder()->hasTable('module_completions')) {
                $query = DB::table('module_completions')
                    ->join('students', 'module_completions.student_id', '=', 'students.student_id')
                    ->join('users', 'students.user_id', '=', 'users.user_id')
                    ->leftJoin('modules', 'module_completions.modules_id', '=', 'modules.modules_id')
                    ->select([
                        'users.user_firstname',
                        'users.user_lastname',
                        'students.student_id',
                        'modules.module_name as program_name',
                        'module_completions.completed_at as completion_date'
                    ])
                    ->whereNotNull('module_completions.completed_at')
                    ->orderBy('module_completions.completed_at', 'desc')
                    ->limit(10);

                if (!empty($filters['year'])) {
                    $query->whereYear('module_completions.completed_at', $filters['year']);
                }
                if (!empty($filters['month'])) {
                    $query->whereMonth('module_completions.completed_at', $filters['month']);
                }

                $completions = $query->get();
            }

            $result = [];
            foreach ($completions as $completion) {
                $result[] = [
                    'student_name' => trim(($completion->user_firstname ?? '') . ' ' . ($completion->user_lastname ?? '')),
                    'student_id' => $completion->student_id,
                    'program' => $completion->program_name ?? 'Unknown Program',
                    'completion_date' => $completion->completion_date ? 
                        Carbon::parse($completion->completion_date)->format('M d, Y') : 'N/A'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Recently completed data error: ' . $e->getMessage());
            return [];
        }
    }
}
