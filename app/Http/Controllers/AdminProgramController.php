<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Student;
use App\Models\StudentBatch;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AdminSetting;

class AdminProgramController extends Controller
{
    public function __construct()
    {
        $user = \Auth::user();
        $isDirector = $user && isset($user->role) && $user->role === 'director';
        \Log::info('AdminProgramController access', [
            'isDirector' => $isDirector,
            'manage_programs' => \App\Models\AdminSetting::getValue('director_manage_programs', 'false'),
            'auth_user' => $user,
            'auth_user_role' => $user && isset($user->role) ? $user->role : null
        ]);
        if ($isDirector) {
            $canManage = \App\Models\AdminSetting::getValue('director_manage_programs', 'false') === 'true' || \App\Models\AdminSetting::getValue('director_manage_programs', '0') === '1';
            if (!$canManage) {
                abort(403, 'Access denied: You do not have permission to manage programs.');
            }
        }
    }

    /**
     * Display a listing of active programs.
     */
    public function index()
    {
        try {
            $programs = Program::where('is_archived', false)
                ->withCount('enrollments')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalPrograms = $programs->count();
            $totalEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->count();

            $archivedPrograms = Program::where('is_archived', true)->count();
            $newProgramsThisMonth = Program::where('is_archived', false)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count();

            $newEnrollmentsThisWeek = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->where('enrollments.created_at', '>=', Carbon::now()->startOfWeek())
                ->count();

            $avgEnrollmentPerProgram = $totalPrograms > 0 ? $totalEnrollments / $totalPrograms : 0;
            $completionRate = 75;
            $avgProgramRating = 4.2;

            $mostPopularProgram = Program::where('is_archived', false)
                ->withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->first();

            $recentProgramsCount = Program::where('is_archived', false)
                ->where('created_at', '>=', Carbon::now()->startOfWeek())
                ->count();

            $chartLabels = [];
            $enrollmentData = [];
            $completionData = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $chartLabels[] = $month->format('M');

                $monthlyEnrollments = DB::table('enrollments')
                    ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                    ->where('programs.is_archived', false)
                    ->whereYear('enrollments.created_at', $month->year)
                    ->whereMonth('enrollments.created_at', $month->month)
                    ->count();

                $enrollmentData[] = $monthlyEnrollments;
                $completionData[] = (int)($monthlyEnrollments * 0.7);
            }

            $recentActivities = [
                [
                    'icon' => '📚',
                    'text' => 'New program "' . ($programs->first()->program_name ?? 'Advanced Programming') . '" created',
                    'time' => '2 hours ago'
                ],
                [
                    'icon' => '👥',
                    'text' => $newEnrollmentsThisWeek . ' students enrolled this week',
                    'time' => '5 hours ago'
                ],
                [
                    'icon' => '🎓',
                    'text' => 'Program analytics updated',
                    'time' => '1 day ago'
                ]
            ];

            $students = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->select('students.student_id', 'users.user_firstname as firstname', 'users.user_lastname as lastname', 'users.email')
                ->orderBy('users.user_firstname')
                ->get();

            return view('admin.admin-programs.admin-programs', compact(
                'programs',
                'totalPrograms',
                'totalEnrollments',
                'archivedPrograms',
                'newProgramsThisMonth',
                'newEnrollmentsThisWeek',
                'avgEnrollmentPerProgram',
                'completionRate',
                'mostPopularProgram',
                'recentProgramsCount',
                'avgProgramRating',
                'chartLabels',
                'enrollmentData',
                'completionData',
                'recentActivities',
                'students'
            ));
        } catch (\Exception $e) {
            Log::error('Programs index error: ' . $e->getMessage());

            return view('admin.admin-programs.admin-programs', [
                'programs' => collect(),
                'totalPrograms' => 0,
                'totalEnrollments' => 0,
                'archivedPrograms' => 0,
                'newProgramsThisMonth' => 0,
                'newEnrollmentsThisWeek' => 0,
                'avgEnrollmentPerProgram' => 0,
                'completionRate' => 0,
                'mostPopularProgram' => null,
                'recentProgramsCount' => 0,
                'avgProgramRating' => 0,
                'chartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'enrollmentData' => [0, 0, 0, 0, 0, 0],
                'completionData' => [0, 0, 0, 0, 0, 0],
                'recentActivities' => [],
                'students' => collect()
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:100',
            'program_description' => 'nullable|string|max:1000',
            'program_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'program_name' => $request->program_name,
            'program_description' => $request->program_description,
            'created_by_admin_id' => Auth::user()->admin_id ?? 1,
            'is_archived' => false,
            'is_active' => true,
        ];

        // Handle image upload
        if ($request->hasFile('program_image')) {
            $image = $request->file('program_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Store the image in storage/app/public/program-images
            $image->storeAs('public/program-images', $imageName);
            
            $data['program_image'] = $imageName;
        }

        Program::create($data);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program added successfully!');
    }

    public function batchStore(Request $request)
    {
        // Check if this is a CSV file upload or direct array data
        if ($request->hasFile('csv_file')) {
            // Handle CSV file upload
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            try {
                $file = $request->file('csv_file');
                $programs = [];
                
                // Read CSV file
                if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                    // Skip header row if it exists
                    $firstRow = fgetcsv($handle, 1000, ",");
                    
                    // Check if first row looks like a header (contains "name" or "description")
                    $isHeader = (stripos($firstRow[0] ?? '', 'name') !== false || 
                               stripos($firstRow[0] ?? '', 'program') !== false);
                    
                    if (!$isHeader) {
                        // First row is data, process it
                        if (!empty($firstRow[0])) {
                            $programs[] = [
                                'program_name' => trim($firstRow[0]),
                                'program_description' => !empty($firstRow[1]) ? trim($firstRow[1]) : null,
                            ];
                        }
                    }
                    
                    // Process remaining rows
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (!empty($data[0])) {
                            $programs[] = [
                                'program_name' => trim($data[0]),
                                'program_description' => !empty($data[1]) ? trim($data[1]) : null,
                            ];
                        }
                    }
                    fclose($handle);
                }

                if (empty($programs)) {
                    return redirect()
                        ->route('admin.programs.index')
                        ->with('error', 'No valid program data found in the CSV file.');
                }

            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', 'Error reading CSV file: ' . $e->getMessage());
            }
        } else {
            // Handle direct array data
            $request->validate([
                'programs' => 'required|array|min:1',
                'programs.*.program_name' => 'required|string|max:100',
                'programs.*.program_description' => 'nullable|string|max:500',
            ]);
            
            $programs = $request->programs;
        }

        // Validate program data
        foreach ($programs as $index => $programData) {
            if (empty($programData['program_name']) || strlen($programData['program_name']) > 100) {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', "Invalid program name at row " . ($index + 1));
            }
            
            if (!empty($programData['program_description']) && strlen($programData['program_description']) > 500) {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', "Program description too long at row " . ($index + 1));
            }
        }

        try {
            $createdCount = 0;
            $adminId = Auth::user()->admin_id ?? 1;

            foreach ($programs as $programData) {
                Program::create([
                    'program_name' => $programData['program_name'],
                    'program_description' => $programData['program_description'] ?? null,
                    'created_by_admin_id' => $adminId,
                    'is_archived' => false,
                    'is_active' => true,
                ]);
                $createdCount++;
            }

            return redirect()
                ->route('admin.programs.index')
                ->with('success', "{$createdCount} programs created successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.programs.index')
                ->with('error', 'Batch creation failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);

        try {
            $program->delete();

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', 'Cannot delete program: it is in use by one or more enrollments.');
            }
            throw $e;
        }
    }

    public function toggleArchive(Request $request, Program $program)
    {
        try {
            $program->is_archived = !$program->is_archived;
            $program->save();

            $status = $program->is_archived ? 'archived' : 'unarchived';
            $message = $program->is_archived ? 'Program archived successfully!' : 'Program unarchived successfully!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Toggle archive error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating program status. Please try again.');
        }
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'program_ids' => 'required|array|min:1',
            'program_ids.*' => 'exists:programs,program_id'
        ]);

        try {
            $deletedCount = Program::whereIn('program_id', $request->program_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} programs deleted successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archived()
    {
        try {
            $archivedPrograms = Program::where('is_archived', true)
                ->withCount('enrollments')
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('admin.admin-programs.admin-programs-archived', compact('archivedPrograms'));
        } catch (\Exception $e) {
            Log::error('Archived programs error: ' . $e->getMessage());
            return view('admin.admin-programs.admin-programs-archived', [
                'archivedPrograms' => collect(),
                'error' => 'Unable to load archived programs data.'
            ]);
        }
    }

    public function enrollments($id)
    {
        $program = Program::findOrFail($id);

        try {
            // Get all enrollments for this program with related data
            $enrollments = $program->enrollments()
                ->with(['user', 'student', 'package'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($enrollment) {
                    // Determine student info from either user or student relationship
                    $studentName = 'Unknown Student';
                    $email = 'No email available';
                    $studentId = null;
                    $startDate = null;

                    if ($enrollment->user) {
                        $firstName = $enrollment->user->user_firstname ?? '';
                        $lastName = $enrollment->user->user_lastname ?? '';
                        $studentName = trim($firstName . ' ' . $lastName) ?: 'Unknown Student';
                        $email = $enrollment->user->email ?? 'No email available';
                    } elseif ($enrollment->student) {
                        $firstName = $enrollment->student->firstname ?? '';
                        $lastName = $enrollment->student->lastname ?? '';
                        $studentName = trim($firstName . ' ' . $lastName) ?: 'Unknown Student';
                        $email = $enrollment->student->email ?? 'No email available';
                        $studentId = $enrollment->student->student_id;
                        $startDate = $enrollment->student->Start_Date;
                    }

                    return [
                        'student_name' => $studentName,
                        'email' => $email,
                        'student_id' => $studentId ?? $enrollment->enrollment_id,
                        'enrolled_at' => $enrollment->created_at ? $enrollment->created_at->format('M d, Y') : 'Unknown date',
                        'enrollment_status' => ucfirst($enrollment->enrollment_status ?? 'pending'),
                        'payment_status' => ucfirst($enrollment->payment_status ?? 'pending'),
                        'package_name' => $enrollment->package->package_name ?? 'N/A',
                        'start_date' => $startDate ? \Carbon\Carbon::parse($startDate)->format('M d, Y') : 'Not set',
                        'learning_mode' => $enrollment->learning_mode ?? 'N/A',
                    ];
                });

            return response()->json([
                'program_name' => $program->program_name,
                'total_enrollments' => $enrollments->count(),
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching enrollments for program ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'error' => 'Error loading enrollments: ' . $e->getMessage(),
                'total_enrollments' => 0,
                'enrollments' => [],
            ], 500);
        }
    }

    public function assignProgram(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $existingEnrollment = DB::table('enrollments')
                ->where('student_id', $request->student_id)
                ->where('program_id', $request->program_id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is already enrolled in this program.'
                ]);
            }

            DB::table('enrollments')->insert([
                'student_id' => $request->student_id,
                'program_id' => $request->program_id,
                'enrollment_date' => Carbon::now(),
                'status' => 'active',
                'notes' => $request->notes,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Program assigned successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Program assignment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning the program.'
            ]);
        }
    }

    public function enrollmentManagement()
    {
        try {
            // Debug log to check if method is being called
            Log::info('Enrollment management method called');
            
            $totalEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->count();

            $activeEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->where('enrollments.enrollment_status', 'enrolled')
                ->count();

            $pendingEnrollments = DB::table('registrations')
                ->where('status', 'pending')
                ->count();

            $completedCourses = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->where('enrollments.enrollment_status', 'completed')
                ->count();

            Log::info('Data loaded successfully', [
                'totalEnrollments' => $totalEnrollments,
                'activeEnrollments' => $activeEnrollments,
                'pendingEnrollments' => $pendingEnrollments,
                'completedCourses' => $completedCourses
            ]);

            return view('admin.admin-student-enrollment.admin-enrollments', compact(
                'totalEnrollments',
                'activeEnrollments', 
                'pendingEnrollments',
                'completedCourses'
            ) + [
                'students' => Student::where('is_archived', false)
                    ->whereNotNull('date_approved')
                    ->orderBy('firstname')
                    ->orderBy('lastname')
                    ->get(),
                'programs' => Program::where('is_archived', false)->orderBy('program_name')->get(),
                'batches' => StudentBatch::orderBy('batch_name')->get(),
                'courses' => Course::with('module')->where('is_archived', false)->orderBy('subject_name')->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Enrollment management error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin.admin-student-enrollment.admin-enrollments', [
                'dbError' => 'Unable to load enrollment data: ' . $e->getMessage(),
                'totalEnrollments' => 0,
                'activeEnrollments' => 0,
                'pendingEnrollments' => 0,
                'completedCourses' => 0,
                'students' => collect([]),
                'programs' => collect([]),
                'batches' => collect([]),
                'courses' => collect([])
            ]);
        }
    }

    public function archive($id)
    {
        try {
            $program = DB::table('programs')->where('program_id', $id)->first();
            
            if (!$program) {
                return redirect()->back()->with('error', 'Program not found.');
            }
            
            DB::table('programs')
                ->where('program_id', $id)
                ->update([
                    'is_archived' => true,
                    'updated_at' => Carbon::now()
                ]);
            
            return redirect()->back()->with('success', 'Program archived successfully!');
        } catch (\Exception $e) {
            Log::error('Program archive error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error archiving program. Please try again.');
        }
    }
}
