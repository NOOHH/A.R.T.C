<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\ClassMeeting;
use App\Models\ContentItem;
use App\Models\Announcement;
use Carbon\Carbon;

class StudentCalendarController extends Controller
{
    public function index()
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        $upcomingMeetings = collect();
        $todaysMeetings = collect();
        $allMeetings = collect();
        $studentPrograms = collect();
        
        if ($student) {
            // Get student's enrolled programs for sidebar
            $studentPrograms = $student->enrollments()
                ->with(['program', 'package'])
                ->whereNotNull('program_id')
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'program_id' => $enrollment->program_id,
                        'program_name' => $enrollment->program->program_name ?? 'Unknown Program',
                        'package_name' => $enrollment->package->package_name ?? 'Unknown Package',
                        'enrollment_type' => $enrollment->enrollment_type,
                        'enrollment_status' => $enrollment->enrollment_status
                    ];
                })
                ->unique('program_id')
                ->values();
            
            // Get student's enrolled batches
            $enrolledBatches = $student->enrollments()
                ->with('batch')
                ->whereNotNull('batch_id')
                ->pluck('batch_id')
                ->unique();
            
            if ($enrolledBatches->isNotEmpty()) {
                // Get meetings for enrolled batches
                $upcomingMeetings = ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->where('meeting_date', '>=', now())
                    ->orderBy('meeting_date', 'asc')
                    ->get();
                
                $todaysMeetings = ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->whereDate('meeting_date', today())
                    ->orderBy('meeting_date', 'asc')
                    ->get();
                
                // Get all meetings for calendar display (next 3 months)
                $allMeetings = ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->where('meeting_date', '>=', now())
                    ->where('meeting_date', '<=', now()->addMonths(3))
                    ->orderBy('meeting_date', 'asc')
                    ->get();
            }
        }

        return view('student.student-calendar.student-calendar', compact('user', 'upcomingMeetings', 'todaysMeetings', 'allMeetings', 'studentPrograms'));
    }

    public function getEvents(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        
        Log::info('📅 getEvents called', [
            'year' => $year,
            'month' => $month,
            'user_id' => session('user_id')
        ]);
        
        if (!$year || !$month) {
            return response()->json(['success' => false, 'message' => 'Year and month are required']);
        }

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            Log::warning('❌ Student not found for getEvents');
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        // Get student's enrolled batches and programs
        $enrolledBatches = $student->enrollments()
            ->whereNotNull('batch_id')
            ->pluck('batch_id')
            ->unique();
            
        $enrolledPrograms = $student->enrollments()
            ->whereNotNull('program_id')
            ->pluck('program_id')
            ->unique();

        $events = collect();
        
        // Create start and end dates for the month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get class meetings from class_meetings table
        if ($enrolledBatches->isNotEmpty()) {
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereBetween('meeting_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->get();

            foreach ($meetings as $meeting) {
                $events->push([
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->title ?? ($meeting->batch->program->program_name ?? 'Class Meeting'),
                    'start' => $meeting->meeting_date->toISOString(),
                    'type' => 'meeting',
                    'description' => $meeting->description ?? '',
                    'program' => $meeting->batch->program->program_name ?? '',
                    'professor' => $meeting->professor->professor_name ?? 'TBA',
                    'duration' => $meeting->duration_minutes ?? 60,
                    'status' => $meeting->status ?? 'scheduled',
                    'meeting_url' => $meeting->meeting_url ?? null,
                    'time' => $meeting->meeting_date->format('g:i A')
                ]);
            }
        }

        // Get assignments from content_items table
        if ($enrolledPrograms->isNotEmpty()) {
            // Get course IDs for enrolled programs
            $enrolledCourseIds = DB::table('courses')
                ->whereIn('module_id', function($query) use ($enrolledPrograms) {
                    $query->select('modules_id')
                          ->from('modules')
                          ->whereIn('program_id', $enrolledPrograms);
                })
                ->pluck('subject_id');

            if ($enrolledCourseIds->isNotEmpty()) {
                $assignments = ContentItem::with(['course.module.program'])
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->where('content_type', 'assignment')
                    ->whereNotNull('due_date')
                    ->whereBetween('due_date', [$startDate, $endDate])
                    ->where('is_active', true)
                    ->get();

                foreach ($assignments as $assignment) {
                    $events->push([
                        'id' => 'assignment_' . $assignment->id,
                        'title' => $assignment->content_title,
                        'start' => $assignment->due_date->toISOString(),
                        'type' => 'assignment',
                        'description' => $assignment->content_description ?? '',
                        'program' => $assignment->course->module->program->program_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'max_points' => $assignment->max_points ?? null,
                        'instructions' => $assignment->content_data['assignment_instructions'] ?? '',
                        'time' => $assignment->due_date->format('g:i A'),
                        'course_id' => $assignment->course_id ?? '',
                        'program_id' => $assignment->course->module->program->program_id ?? '',
                        'module_id' => $assignment->course->module->modules_id ?? ''
                    ]);
                }
                
                // Get lessons from content_items table
                $lessons = ContentItem::with(['course.module.program'])
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->where('content_type', 'lesson')
                    ->where('is_active', true)
                    ->get();

                foreach ($lessons as $lesson) {
                    $events->push([
                        'id' => 'lesson_' . $lesson->id,
                        'title' => $lesson->content_title,
                        'start' => $lesson->created_at->toISOString(),
                        'type' => 'lesson',
                        'description' => $lesson->content_description ?? '',
                        'program' => $lesson->course->module->program->program_name ?? '',
                        'module' => $lesson->course->module->module_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'program_id' => $lesson->course->module->program->program_id ?? '',
                        'module_id' => $lesson->course->module->modules_id ?? '',
                        'course_id' => $lesson->course_id ?? '',
                        'time' => $lesson->created_at->format('g:i A')
                    ]);
                }
            }
        }

        // Get announcements from announcements table
        if ($enrolledPrograms->isNotEmpty()) {
            $announcements = Announcement::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('is_active', true)
                ->get();

            foreach ($announcements as $announcement) {
                $events->push([
                    'id' => 'announcement_' . $announcement->announcement_id,
                    'title' => $announcement->title,
                    'start' => $announcement->created_at->toISOString(),
                    'type' => 'announcement',
                    'description' => $announcement->content ?? '',
                    'program' => $announcement->program->program_name ?? '',
                    'professor' => $announcement->professor->professor_name ?? 'System',
                    'announcement_type' => $announcement->type ?? 'general',
                    'content' => $announcement->content ?? '',
                    'video_link' => $announcement->video_link ?? null,
                    'time' => $announcement->created_at->format('g:i A')
                ]);
            }
        }

        // Sort events by date
        $sortedEvents = $events->sortBy('start')->values();

        // Calculate statistics
        $meta = [
            'meetings' => $events->where('type', 'meeting')->count(),
            'assignments' => $events->where('type', 'assignment')->count(),
            'announcements' => $events->where('type', 'announcement')->count(),
            'lessons' => $events->where('type', 'lesson')->count(),
        ];

        return response()->json([
            'success' => true,
            'events' => $sortedEvents,
            'meta' => $meta
        ]);
    }

    public function getTodaySchedule()
    {
        Log::info('📅 getTodaySchedule called', [
            'user_id' => session('user_id'),
            'session_data' => session()->all()
        ]);
        
        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            Log::warning('❌ Student not found for getTodaySchedule', [
                'user_id' => session('user_id')
            ]);
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        Log::info('✅ Student found for getTodaySchedule', [
            'student_id' => $student->student_id,
            'user_id' => $student->user_id
        ]);

        // Get student's enrolled batches and programs
        $enrolledBatches = $student->enrollments()
            ->whereNotNull('batch_id')
            ->pluck('batch_id')
            ->unique();
            
        $enrolledPrograms = $student->enrollments()
            ->whereNotNull('program_id')
            ->pluck('program_id')
            ->unique();

        $events = collect();
        $today = today();

        // Get today's class meetings
        if ($enrolledBatches->isNotEmpty()) {
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereDate('meeting_date', $today)
                ->where('status', '!=', 'cancelled')
                ->orderBy('meeting_date', 'asc')
                ->get();

            foreach ($meetings as $meeting) {
                $events->push([
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->title ?? ($meeting->batch->program->program_name ?? 'Class Meeting'),
                    'start' => $meeting->meeting_date->toISOString(),
                    'type' => 'meeting',
                    'description' => $meeting->description ?? '',
                    'program' => $meeting->batch->program->program_name ?? '',
                    'professor' => $meeting->professor->professor_name ?? 'TBA',
                    'meeting_url' => $meeting->meeting_url ?? null,
                    'time' => $meeting->meeting_date->format('g:i A')
                ]);
            }
        }

        // Get today's assignment deadlines from content_items table
        if ($enrolledPrograms->isNotEmpty()) {
            // Get course IDs for enrolled programs
            $enrolledCourseIds = DB::table('courses')
                ->whereIn('module_id', function($query) use ($enrolledPrograms) {
                    $query->select('modules_id')
                          ->from('modules')
                          ->whereIn('program_id', $enrolledPrograms);
                })
                ->pluck('subject_id');

            if ($enrolledCourseIds->isNotEmpty()) {
                $assignments = ContentItem::with(['course.module.program'])
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->where('content_type', 'assignment')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', $today)
                    ->where('is_active', true)
                    ->orderBy('due_date', 'asc')
                    ->get();

                foreach ($assignments as $assignment) {
                    $events->push([
                        'id' => 'assignment_' . $assignment->id,
                        'title' => $assignment->content_title,
                        'start' => $assignment->due_date->toISOString(),
                        'type' => 'assignment',
                        'description' => $assignment->content_description ?? '',
                        'program' => $assignment->course->module->program->program_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'time' => $assignment->due_date->format('g:i A'),
                        'course_id' => $assignment->course_id ?? '',
                        'program_id' => $assignment->course->module->program->program_id ?? '',
                        'module_id' => $assignment->course->module->modules_id ?? ''
                    ]);
                }
                
                // Get today's lessons from content_items table
                $lessons = ContentItem::with(['course.module.program'])
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->where('content_type', 'lesson')
                    ->whereDate('created_at', $today)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($lessons as $lesson) {
                    $events->push([
                        'id' => 'lesson_' . $lesson->id,
                        'title' => $lesson->content_title,
                        'start' => $lesson->created_at->toISOString(),
                        'type' => 'lesson',
                        'description' => $lesson->content_description ?? '',
                        'program' => $lesson->course->module->program->program_name ?? '',
                        'module' => $lesson->course->module->module_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'program_id' => $lesson->course->module->program->program_id ?? '',
                        'module_id' => $lesson->course->module->modules_id ?? '',
                        'course_id' => $lesson->course_id ?? '',
                        'time' => $lesson->created_at->format('g:i A')
                    ]);
                }
            }
        }

        // Get today's announcements
        if ($enrolledPrograms->isNotEmpty()) {
            $announcements = Announcement::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereDate('created_at', $today)
                ->where('is_active', true)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($announcements as $announcement) {
                $events->push([
                    'id' => 'announcement_' . $announcement->announcement_id,
                    'title' => $announcement->title,
                    'start' => $announcement->created_at->toISOString(),
                    'type' => 'announcement',
                    'description' => $announcement->content ?? '',
                    'program' => $announcement->program->program_name ?? '',
                    'professor' => $announcement->professor->professor_name ?? 'System',
                    'time' => $announcement->created_at->format('g:i A')
                ]);
            }
        }

        // Sort events by time
        $sortedEvents = $events->sortBy('start')->values();

        Log::info('📋 Returning today schedule', [
            'events_count' => $sortedEvents->count(),
            'events' => $sortedEvents->toArray()
        ]);

        return response()->json([
            'success' => true,
            'events' => $sortedEvents
        ]);
    }

    public function getEventDetails($type, $id)
    {
        $event = null;

        switch ($type) {
            case 'meeting':
                $meeting = ClassMeeting::with(['batch.program', 'professor'])->find($id);
                if ($meeting) {
                    $event = [
                        'id' => 'meeting_' . $meeting->meeting_id,
                        'title' => $meeting->title ?? ($meeting->batch->program->program_name ?? 'Class Meeting'),
                        'start' => $meeting->meeting_date->toISOString(),
                        'type' => 'meeting',
                        'description' => $meeting->description ?? '',
                        'program' => $meeting->batch->program->program_name ?? '',
                        'professor' => $meeting->professor->professor_name ?? 'TBA',
                        'duration' => $meeting->duration_minutes ?? 60,
                        'status' => $meeting->status ?? 'scheduled',
                        'meeting_url' => $meeting->meeting_url ?? null
                    ];
                }
                break;

            case 'assignment':
                $assignment = ContentItem::with(['course.module.program'])->find($id);
                if ($assignment && $assignment->content_type === 'assignment') {
                    $event = [
                        'id' => 'assignment_' . $assignment->id,
                        'title' => $assignment->content_title,
                        'start' => $assignment->due_date->toISOString(),
                        'type' => 'assignment',
                        'description' => $assignment->content_description ?? '',
                        'program' => $assignment->course->module->program->program_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'max_points' => $assignment->max_points ?? null,
                        'instructions' => $assignment->content_data['assignment_instructions'] ?? '',
                        'course_id' => $assignment->course_id ?? '',
                        'program_id' => $assignment->course->module->program->program_id ?? '',
                        'module_id' => $assignment->course->module->modules_id ?? ''
                    ];
                }
                break;

            case 'announcement':
                $announcement = Announcement::with(['program', 'professor'])->find($id);
                if ($announcement) {
                    $event = [
                        'id' => 'announcement_' . $announcement->announcement_id,
                        'title' => $announcement->title,
                        'start' => $announcement->created_at->toISOString(),
                        'type' => 'announcement',
                        'description' => $announcement->content ?? '',
                        'program' => $announcement->program->program_name ?? '',
                        'professor' => $announcement->professor->professor_name ?? 'System',
                        'announcement_type' => $announcement->type ?? 'general',
                        'content' => $announcement->content ?? '',
                        'video_link' => $announcement->video_link ?? null
                    ];
                }
                break;

            case 'lesson':
                $lesson = ContentItem::with(['course.module.program'])->find($id);
                if ($lesson && $lesson->content_type === 'lesson') {
                    $event = [
                        'id' => 'lesson_' . $lesson->id,
                        'title' => $lesson->content_title,
                        'start' => $lesson->created_at->toISOString(),
                        'type' => 'lesson',
                        'description' => $lesson->content_description ?? '',
                        'program' => $lesson->course->module->program->program_name ?? '',
                        'module' => $lesson->course->module->module_name ?? '',
                        'professor' => 'TBA', // Content items don't have direct professor association
                        'program_id' => $lesson->course->module->program->program_id ?? '',
                        'module_id' => $lesson->course->module->modules_id ?? '',
                        'course_id' => $lesson->course_id ?? '',
                        'content_data' => $lesson->content_data ?? []
                    ];
                }
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Unknown event type']);
        }

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found']);
        }

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }
}