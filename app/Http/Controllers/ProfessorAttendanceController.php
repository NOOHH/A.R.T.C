<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Program;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProfessorAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor.auth');
    }

    public function index(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->with(['students'])->get();
        
        $selectedProgramId = $request->get('program_id');
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $students = collect();
        $attendanceRecords = collect();
        
        if ($selectedProgramId) {
            $selectedProgram = $assignedPrograms->where('program_id', $selectedProgramId)->first();
            if ($selectedProgram) {
                $students = Student::whereHas('enrollments', function ($query) use ($selectedProgramId) {
                    $query->where('program_id', $selectedProgramId);
                })->get();
                
                $attendanceRecords = Attendance::where('program_id', $selectedProgramId)
                    ->where('attendance_date', $selectedDate)
                    ->get()
                    ->keyBy('student_id');
            }
        }
        
        return view('professor.attendance.index', compact(
            'assignedPrograms', 
            'students', 
            'attendanceRecords', 
            'selectedProgramId', 
            'selectedDate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,student_id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.notes' => 'nullable|string|max:500'
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Verify professor has access to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return back()->withErrors(['error' => 'You do not have access to this program.']);
        }

        foreach ($request->attendance as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'program_id' => $request->program_id,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'professor_id' => $professor->professor_id,
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Attendance recorded successfully!');
    }

    public function reports(Request $request)
    {
        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        $selectedProgramId = $request->get('program_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $attendanceReport = [];
        
        if ($selectedProgramId) {
            $students = Student::whereHas('enrollments', function ($query) use ($selectedProgramId) {
                $query->where('program_id', $selectedProgramId);
            })->get();
            
            foreach ($students as $student) {
                $attendanceRecords = Attendance::where('student_id', $student->student_id)
                    ->where('program_id', $selectedProgramId)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->get();
                
                $totalDays = $attendanceRecords->count();
                $presentDays = $attendanceRecords->where('status', 'present')->count();
                $lateDays = $attendanceRecords->where('status', 'late')->count();
                $absentDays = $attendanceRecords->where('status', 'absent')->count();
                
                $attendanceReport[] = [
                    'student' => $student,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'absent_days' => $absentDays,
                    'attendance_percentage' => $totalDays > 0 ? round(($presentDays + $lateDays) / $totalDays * 100, 1) : 0
                ];
            }
        }
        
        return view('professor.attendance.reports', compact(
            'assignedPrograms',
            'attendanceReport',
            'selectedProgramId',
            'startDate',
            'endDate'
        ));
    }
}
