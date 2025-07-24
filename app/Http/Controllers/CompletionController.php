<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseCompletion;
use App\Models\ContentCompletion;
use Illuminate\Support\Carbon;

class CompletionController extends Controller
{
    public function markCourseComplete(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'module_id' => 'nullable|integer',
        ]);
        $studentId = Auth::id();
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
        $request->validate([
            'content_id' => 'required|integer',
            'course_id' => 'nullable|integer',
            'module_id' => 'nullable|integer',
        ]);
        $studentId = Auth::id();
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