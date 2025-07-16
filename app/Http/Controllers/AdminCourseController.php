<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('module', 'lessons')->orderBy('subject_order')->get();
        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            Log::info('Course creation request data:', $request->all());
            
            $validatedData = $request->validate([
                'subject_name' => 'required|string|max:255',
                'subject_description' => 'nullable|string',
                'module_id' => 'required|exists:modules,modules_id',
                'subject_price' => 'required|numeric|min:0',
                'is_required' => 'nullable|boolean',
            ]);
            
            $course = Course::create([
                'subject_name' => $validatedData['subject_name'],
                'subject_description' => $validatedData['subject_description'],
                'module_id' => $validatedData['module_id'],
                'subject_price' => $validatedData['subject_price'],
                'is_required' => $request->has('is_required') ? true : false,
                'is_active' => true,
                'subject_order' => Course::where('module_id', $validatedData['module_id'])->max('subject_order') + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully!',
                'course' => $course->load('module'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Course validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'debug' => [
                    'request_data' => $request->all(),
                    'validation_rules' => [
                        'subject_name' => 'required|string|max:255',
                        'subject_description' => 'nullable|string',
                        'module_id' => 'required|exists:modules,modules_id',
                        'subject_price' => 'required|numeric|min:0',
                        'is_required' => 'nullable|boolean',
                    ]
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Course creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating course: ' . $e->getMessage(),
                'debug' => [
                    'request_data' => $request->all()
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        $course = Course::with(['module', 'lessons'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'course' => $course,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'subject_description' => 'nullable|string',
            'subject_price' => 'required|numeric|min:0',
            'is_required' => 'boolean',
        ]);

        $course = Course::findOrFail($id);
        $course->update([
            'subject_name' => $request->subject_name,
            'subject_description' => $request->subject_description,
            'subject_price' => $request->subject_price,
            'is_required' => $request->boolean('is_required', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully!',
            'course' => $course->load('module'),
        ]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully!',
        ]);
    }

    public function getModuleCourses($moduleId)
    {
        try {
            $courses = Course::where('module_id', $moduleId)
                ->with('lessons.contentItems')
                ->orderBy('subject_order')
                ->get();

            return response()->json([
                'success' => true,
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCourseContent($courseId)
    {
        $course = Course::with(['lessons' => function($query) {
            $query->ordered()->with(['contentItems' => function($query) {
                $query->ordered();
            }]);
        }])->findOrFail($courseId);

        return response()->json([
            'success' => true,
            'course' => $course,
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,subject_id',
        ]);

        DB::transaction(function() use ($request) {
            foreach ($request->course_ids as $index => $courseId) {
                Course::where('subject_id', $courseId)->update(['subject_order' => $index + 1]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Course order updated successfully!',
        ]);
    }
}
