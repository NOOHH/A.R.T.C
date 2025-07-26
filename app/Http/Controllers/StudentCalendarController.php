<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\ClassMeeting;
use App\Models\Assignment;
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
        
        if (!$year || !$month) {
            return response()->json(['success' => false, 'message' => 'Year and month are required']);
        }

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
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

        // Get class meetings
        if ($enrolledBatches->isNotEmpty()) {
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereBetween('meeting_date', [$startDate, $endDate])
                ->get();

            foreach ($meetings as $meeting) {
                $events->push([
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->batch->program->program_name ?? 'Class Meeting',
                    'start' => $meeting->meeting_date->toISOString(),
                    'type' => 'meeting',
                    'description' => $meeting->description ?? '',
                    'program' => $meeting->batch->program->program_name ?? '',
                    'professor' => $meeting->professor->name ?? 'TBA',
                    'duration' => $meeting->duration ?? 60,
                    'status' => $meeting->status ?? 'scheduled',
                    'meeting_url' => $meeting->meeting_url ?? null,
                    'time' => $meeting->meeting_date->format('g:i A')
                ]);
            }
        }

        // Get assignments
        if ($enrolledPrograms->isNotEmpty()) {
            $assignments = Assignment::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->get();

            foreach ($assignments as $assignment) {
                $events->push([
                    'id' => 'assignment_' . $assignment->assignment_id,
                    'title' => $assignment->title,
                    'start' => $assignment->due_date->toISOString(),
                    'type' => 'assignment',
                    'description' => $assignment->description ?? '',
                    'program' => $assignment->program->program_name ?? '',
                    'professor' => $assignment->professor->name ?? 'TBA',
                    'max_points' => $assignment->max_points ?? null,
                    'instructions' => $assignment->instructions ?? '',
                    'time' => $assignment->due_date->format('g:i A')
                ]);
            }
        }

        // Get announcements
        if ($enrolledPrograms->isNotEmpty()) {
            $announcements = Announcement::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereBetween('announcement_date', [$startDate, $endDate])
                ->where('status', 'active')
                ->get();

            foreach ($announcements as $announcement) {
                $events->push([
                    'id' => 'announcement_' . $announcement->announcement_id,
                    'title' => $announcement->title,
                    'start' => $announcement->announcement_date->toISOString(),
                    'type' => 'announcement',
                    'description' => $announcement->content ?? '',
                    'program' => $announcement->program->program_name ?? '',
                    'professor' => $announcement->professor->name ?? 'System',
                    'announcement_type' => $announcement->announcement_type ?? 'general',
                    'content' => $announcement->content ?? '',
                    'expire_date' => $announcement->expire_date ?? null,
                    'video_link' => $announcement->video_link ?? null,
                    'time' => $announcement->announcement_date->format('g:i A')
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
        ];

        return response()->json([
            'success' => true,
            'events' => $sortedEvents,
            'meta' => $meta
        ]);
    }

    public function getTodaySchedule()
    {
        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
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
        $today = today();

        // Get today's class meetings
        if ($enrolledBatches->isNotEmpty()) {
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereDate('meeting_date', $today)
                ->orderBy('meeting_date', 'asc')
                ->get();

            foreach ($meetings as $meeting) {
                $events->push([
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->batch->program->program_name ?? 'Class Meeting',
                    'start' => $meeting->meeting_date->toISOString(),
                    'type' => 'meeting',
                    'description' => $meeting->description ?? '',
                    'program' => $meeting->batch->program->program_name ?? '',
                    'professor' => $meeting->professor->name ?? 'TBA',
                    'meeting_url' => $meeting->meeting_url ?? null,
                    'time' => $meeting->meeting_date->format('g:i A')
                ]);
            }
        }

        // Get today's assignment deadlines
        if ($enrolledPrograms->isNotEmpty()) {
            $assignments = Assignment::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereDate('due_date', $today)
                ->orderBy('due_date', 'asc')
                ->get();

            foreach ($assignments as $assignment) {
                $events->push([
                    'id' => 'assignment_' . $assignment->assignment_id,
                    'title' => $assignment->title,
                    'start' => $assignment->due_date->toISOString(),
                    'type' => 'assignment',
                    'description' => $assignment->description ?? '',
                    'program' => $assignment->program->program_name ?? '',
                    'professor' => $assignment->professor->name ?? 'TBA',
                    'time' => $assignment->due_date->format('g:i A')
                ]);
            }
        }

        // Get today's announcements
        if ($enrolledPrograms->isNotEmpty()) {
            $announcements = Announcement::with(['program', 'professor'])
                ->whereIn('program_id', $enrolledPrograms)
                ->whereDate('announcement_date', $today)
                ->where('status', 'active')
                ->orderBy('announcement_date', 'asc')
                ->get();

            foreach ($announcements as $announcement) {
                $events->push([
                    'id' => 'announcement_' . $announcement->announcement_id,
                    'title' => $announcement->title,
                    'start' => $announcement->announcement_date->toISOString(),
                    'type' => 'announcement',
                    'description' => $announcement->content ?? '',
                    'program' => $announcement->program->program_name ?? '',
                    'professor' => $announcement->professor->name ?? 'System',
                    'time' => $announcement->announcement_date->format('g:i A')
                ]);
            }
        }

        // Sort events by time
        $sortedEvents = $events->sortBy('start')->values();

        return response()->json([
            'success' => true,
            'events' => $sortedEvents
        ]);
    }

    public function getEventDetails($eventId)
    {
        // Parse event ID to get type and actual ID
        $parts = explode('_', $eventId, 2);
        if (count($parts) !== 2) {
            return response()->json(['success' => false, 'message' => 'Invalid event ID']);
        }

        $type = $parts[0];
        $id = $parts[1];

        $event = null;

        switch ($type) {
            case 'meeting':
                $meeting = ClassMeeting::with(['batch.program', 'professor'])->find($id);
                if ($meeting) {
                    $event = [
                        'id' => 'meeting_' . $meeting->meeting_id,
                        'title' => $meeting->batch->program->program_name ?? 'Class Meeting',
                        'start' => $meeting->meeting_date->toISOString(),
                        'type' => 'meeting',
                        'description' => $meeting->description ?? '',
                        'program' => $meeting->batch->program->program_name ?? '',
                        'professor' => $meeting->professor->name ?? 'TBA',
                        'duration' => $meeting->duration ?? 60,
                        'status' => $meeting->status ?? 'scheduled',
                        'meeting_url' => $meeting->meeting_url ?? null
                    ];
                }
                break;

            case 'assignment':
                $assignment = Assignment::with(['program', 'professor'])->find($id);
                if ($assignment) {
                    $event = [
                        'id' => 'assignment_' . $assignment->assignment_id,
                        'title' => $assignment->title,
                        'start' => $assignment->due_date->toISOString(),
                        'type' => 'assignment',
                        'description' => $assignment->description ?? '',
                        'program' => $assignment->program->program_name ?? '',
                        'professor' => $assignment->professor->name ?? 'TBA',
                        'max_points' => $assignment->max_points ?? null,
                        'instructions' => $assignment->instructions ?? ''
                    ];
                }
                break;

            case 'announcement':
                $announcement = Announcement::with(['program', 'professor'])->find($id);
                if ($announcement) {
                    $event = [
                        'id' => 'announcement_' . $announcement->announcement_id,
                        'title' => $announcement->title,
                        'start' => $announcement->announcement_date->toISOString(),
                        'type' => 'announcement',
                        'description' => $announcement->content ?? '',
                        'program' => $announcement->program->program_name ?? '',
                        'professor' => $announcement->professor->name ?? 'System',
                        'announcement_type' => $announcement->announcement_type ?? 'general',
                        'content' => $announcement->content ?? '',
                        'expire_date' => $announcement->expire_date ?? null,
                        'video_link' => $announcement->video_link ?? null
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