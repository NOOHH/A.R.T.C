<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminProgramController extends Controller
{
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
        ]);

        Program::create([
            'program_name' => $request->program_name,
            'created_by_admin_id' => Auth::user()->admin_id ?? 1,
            'is_archived' => false,
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program added successfully!');
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'programs' => 'required|array|min:1',
            'programs.*.program_name' => 'required|string|max:100',
            'programs.*.program_description' => 'nullable|string|max:500',
        ]);

        try {
            $createdCount = 0;
            $adminId = Auth::user()->admin_id ?? 1;

            foreach ($request->programs as $programData) {
                Program::create([
                    'program_name' => $programData['program_name'],
                    'program_description' => $programData['program_description'] ?? null,
                    'created_by_admin_id' => $adminId,
                    'is_archived' => false,
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
        $program->is_archived = !$program->is_archived;
        $program->save();

        $status = $program->is_archived ? 'archived' : 'unarchived';

        return response()->json([
            'success' => true,
            'message' => "Program {$status} successfully!"
        ]);
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
            $rows = DB::table('students')
                ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
                ->where('students.program_id', $id)
                ->select([
                    'students.firstname',
                    'students.lastname',
                    'students.email',
                    'users.email as user_email',
                    'students.created_at',
                    'students.student_id as id',
                    'students.Start_Date'
                ])
                ->get();

            $enrollments = $rows->map(function ($r) {
                return [
                    'student_name' => trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')) ?: 'Unknown Student',
                    'email' => $r->email ?: $r->user_email ?: 'No email available',
                    'student_id' => $r->id,
                    'enrolled_at' => $r->created_at ? Carbon::parse($r->created_at)->format('M d, Y') : 'Unknown date',
                    'status' => 'Enrolled',
                    'start_date' => $r->Start_Date ? Carbon::parse($r->Start_Date)->format('M d, Y') : 'Not set',
                ];
            });

            return response()->json([
                'program_name' => $program->program_name,
                'total_enrollments' => $enrollments->count(),
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching enrollments: ' . $e->getMessage());

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
            $totalEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->count();

            $activeEnrollments = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->where('enrollments.status', 'active')
                ->count();

            $pendingEnrollments = DB::table('registrations')
                ->where('status', 'pending')
                ->count();

            $completedCourses = DB::table('enrollments')
                ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->where('programs.is_archived', false)
                ->where('enrollments.status', 'completed')
                ->count();

            return view('admin.admin-enrollments', compact(
                'totalEnrollments',
                'activeEnrollments',
                'pendingEnrollments',
                'completedCourses'
            ));
        } catch (\Exception $e) {
            Log::error('Enrollment management error: ' . $e->getMessage());

            return view('admin.admin-enrollments', [
                'dbError' => 'Unable to load enrollment data.',
                'totalEnrollments' => 0,
                'activeEnrollments' => 0,
                'pendingEnrollments' => 0,
                'completedCourses' => 0
            ]);
        }
    }

    public function archive($id)
    {
        try {
            $program = DB::table('programs')->where('program_id', $id)->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program not found.'
                ]);
            }
            
            DB::table('programs')
                ->where('program_id', $id)
                ->update([
                    'is_archived' => true,
                    'updated_at' => Carbon::now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Program archived successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Program archive error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error archiving program. Please try again.'
            ]);
        }
    }
}
