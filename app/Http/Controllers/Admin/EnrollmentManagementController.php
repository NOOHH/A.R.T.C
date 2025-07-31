<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Program;
use App\Models\BatchEnrollment;
use Carbon\Carbon;

class EnrollmentManagementController extends Controller
{
    public function index()
    {
        $data = [
            'totalEnrollments' => Enrollment::count(),
            'activeStudents' => Student::where('status', 'active')->count(),
            'pendingRegistrations' => Enrollment::where('enrollment_status', 'pending')->count(),
            'completedCourses' => Enrollment::where('enrollment_status', 'completed')->count(),
            'batches' => BatchEnrollment::with('program')->get(),
            'students' => Student::all(),
            'programs' => Program::active()->get()
        ];

        return view('admin.enrollments.index', $data);
    }

    public function assignCourse(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'program_id' => 'required|exists:programs,program_id',
            'notes' => 'nullable|string'
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $validated['student_id'],
            'program_id' => $validated['program_id'],
            'notes' => $validated['notes'],
            'status' => 'active',
            'enrollment_date' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course assigned successfully',
            'data' => $enrollment
        ]);
    }
}
