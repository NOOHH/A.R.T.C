<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseCompletion;
use App\Models\ContentCompletion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CompletionController extends Controller
{
    public function markCourseComplete(Request $request)
    {
        Log::info('DEBUG: markCourseComplete session user_id', ['user_id' => session('user_id')]);
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        Log::info('DEBUG: markCourseComplete student lookup', ['student' => $student]);
        $request->validate([
            'course_id' => 'required|integer',
            'module_id' => 'nullable|integer',
        ]);
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;
        $courseId = $request->input('course_id');
        $moduleId = $request->input('module_id');

        $completion = CourseCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'module_id' => $moduleId,
            ],
            [
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        return response()->json(['success' => true]);
    }

    public function markContentComplete(Request $request)
    {
        Log::info('DEBUG: markContentComplete session user_id', ['user_id' => session('user_id')]);
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        Log::info('DEBUG: markContentComplete student lookup', ['student' => $student]);
        $request->validate([
            'content_id' => 'required|integer',
            'course_id' => 'nullable|integer',
            'module_id' => 'nullable|integer',
        ]);
        $userId = session('user_id');
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 401);
        }
        $studentId = $student->student_id;
        $contentId = $request->input('content_id');
        $courseId = $request->input('course_id');
        $moduleId = $request->input('module_id');

        $completion = ContentCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'content_id' => $contentId,
                'course_id' => $courseId,
                'module_id' => $moduleId,
            ],
            [
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        return response()->json(['success' => true]);
    }

    public function markModuleComplete(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
        ]);
        $studentId = Auth::id();
        $moduleId = $request->input('module_id');
        // Assuming you have a ModuleCompletion model/table
        $completion = \App\Models\ModuleCompletion::firstOrCreate(
            [
                'student_id' => $studentId,
                'module_id' => $moduleId,
            ],
            [
                'completed_at' => Carbon::now(),
            ]
        );
        $completion->completed_at = Carbon::now();
        $completion->save();
        return response()->json(['success' => true]);
    }
} 