<?php

namespace App\Http\Controllers;

use App\Models\BoardPasser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BoardPassersController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin or director
        $userType = session('user_type');
        if (!$userType || ($userType !== 'admin' && $userType !== 'director')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Access denied. Board passers management is only available for admins and directors.');
        }

        try {
            $query = BoardPasser::query();

            // Apply filters
            if ($request->filled('exam')) {
                $query->where('board_exam', $request->exam);
            }
            if ($request->filled('year')) {
                $query->where('exam_year', $request->year);
            }
            if ($request->filled('result')) {
                $query->where('result', $request->result);
            }
            if ($request->filled('program')) {
                $query->where('program', 'LIKE', '%' . $request->program . '%');
            }

            $passers = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Get statistics
            $stats = $this->getStats();

            return view('admin.board-passers.index', compact('passers', 'stats'));
        } catch (\Exception $e) {
            Log::error('Board passers index error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading board passers management.');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'program' => 'nullable|string|max:255',
            'board_exam' => 'required|string|max:50',
            'exam_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'exam_date' => 'nullable|date',
            'result' => 'required|in:PASS,FAIL',
            'rating' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            BoardPasser::create([
                'student_id' => $request->student_id,
                'student_name' => $request->student_name,
                'program' => $request->program,
                'board_exam' => $request->board_exam,
                'exam_year' => $request->exam_year,
                'exam_date' => $request->exam_date,
                'result' => $request->result,
                'rating' => $request->rating,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Board passer entry created successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Board passer creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $passer = BoardPasser::findOrFail($id);
            return response()->json($passer);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Board passer not found.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'program' => 'nullable|string|max:255',
            'board_exam' => 'required|string|max:50',
            'exam_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'exam_date' => 'nullable|date',
            'result' => 'required|in:PASS,FAIL',
            'rating' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $passer = BoardPasser::findOrFail($id);
            $passer->update([
                'student_id' => $request->student_id,
                'student_name' => $request->student_name,
                'program' => $request->program,
                'board_exam' => $request->board_exam,
                'exam_year' => $request->exam_year,
                'exam_date' => $request->exam_date,
                'result' => $request->result,
                'rating' => $request->rating,
                'notes' => $request->notes,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Board passer entry updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Board passer update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $passer = BoardPasser::findOrFail($id);
            $passer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Board passer entry deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Board passer deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $totalPassers = BoardPasser::where('result', 'PASS')->count();
            $totalNonPassers = BoardPasser::where('result', 'FAIL')->count();
            $totalRecords = $totalPassers + $totalNonPassers;
            
            $passRate = $totalRecords > 0 ? round(($totalPassers / $totalRecords) * 100, 2) : 0;
            
            $lastUpdated = BoardPasser::latest('updated_at')->first();
            $lastUpdatedDate = $lastUpdated ? $lastUpdated->updated_at->format('M d, Y H:i') : null;
            
            return [
                'total_passers' => $totalPassers,
                'total_non_passers' => $totalNonPassers,
                'pass_rate' => $passRate,
                'last_updated' => $lastUpdatedDate
            ];
        } catch (\Exception $e) {
            Log::error('Board passers stats error: ' . $e->getMessage());
            return [
                'total_passers' => 0,
                'total_non_passers' => 0,
                'pass_rate' => 0,
                'last_updated' => null
            ];
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Student ID',
            'Student Name',
            'Program',
            'Board Exam',
            'Exam Year',
            'Exam Date',
            'Result',
            'Rating',
            'Notes'
        ];
        
        $sampleData = [
            ['STU001', 'John Doe', 'BS Accountancy', 'CPA', '2024', '2024-05-15', 'PASS', '85.50', 'Excellent performance'],
            ['STU002', 'Jane Smith', 'BS Education', 'LET', '2024', '2024-09-30', 'FAIL', '68.25', 'Needs improvement'],
            ['STU003', 'Mike Johnson', 'BS Civil Engineering', 'CE', '2024', '2024-11-20', 'PASS', '92.75', 'Outstanding result']
        ];
        
        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne($headers);
        $csv->insertAll($sampleData);
        
        return response($csv->getContent())
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="board_passers_template.csv"');
    }

    public function getStudentsList()
    {
        try {
            $students = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->leftJoin('enrollments', 'students.student_id', '=', 'enrollments.student_id')
                ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
                ->select(
                    'students.student_id',
                    DB::raw("CONCAT(users.user_firstname, ' ', users.user_lastname) as name"),
                    'programs.program_name as program'
                )
                ->where('users.role', 'student')
                ->groupBy('students.student_id', 'users.user_firstname', 'users.user_lastname', 'programs.program_name')
                ->orderBy('users.user_firstname')
                ->get()
                ->map(function($student) {
                    return [
                        'student_id' => $student->student_id,
                        'name' => $student->name,
                        'program' => $student->program ?: 'Unknown Program'
                    ];
                });
            
            return response()->json($students);
        } catch (\Exception $e) {
            Log::error('Get students list error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load students list'], 500);
        }
    }
}
