<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('module')->orderBy('subject_order')->get();
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
        $course = Course::with(['module'])->findOrFail($id);
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
                ->with('contentItems')
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
        try {
            $course = Course::with(['contentItems' => function($query) {
                $query->ordered();
            }])->findOrFail($courseId);

            // Get content items directly from the course
            $contentItems = $course->contentItems;

            return response()->json([
                'success' => true,
                'course' => $course,
                'content' => $contentItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading course content: ' . $e->getMessage()
            ], 500);
        }
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

    public function moveCourse(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,subject_id',
                'module_id' => 'required|exists:modules,modules_id',
            ]);

            $course = Course::findOrFail($request->course_id);
            $oldModuleId = $course->module_id;
            
            // Update the course's module assignment
            $course->update([
                'module_id' => $request->module_id,
                'subject_order' => Course::where('module_id', $request->module_id)->max('subject_order') + 1
            ]);

            // Also move associated content items to the new module
            ContentItem::where('course_id', $request->course_id)
                      ->update(['module_id' => $request->module_id]);

            Log::info('Course moved successfully', [
                'course_id' => $request->course_id,
                'old_module_id' => $oldModuleId,
                'new_module_id' => $request->module_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course moved successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error moving course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error moving course: ' . $e->getMessage()
            ], 500);
        }
    }
}
