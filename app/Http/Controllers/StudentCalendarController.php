<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassMeeting;
use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\Quiz;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StudentCalendarController extends Controller
{
    public function index()
    {
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => session('user_firstname'),
            'user_lastname' => session('user_lastname'),
            'user_email' => session('user_email')
        ];

        return view('student.student-calendar.student-calendar', compact('user'));
    }

    /**
     * Get calendar events for a specific month
     */
    public function getEvents(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }

            $year = $request->get('year', date('Y'));
            $month = $request->get('month', date('n'));

            // Get the first and last day of the month
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            Log::info('Getting calendar events', [
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            // Get user's enrolled batches
            $enrolledBatches = Enrollment::where('user_id', $userId)
                ->where('enrollment_status', 'approved')
                ->pluck('batch_id')
                ->toArray();

            if (empty($enrolledBatches)) {
                return response()->json([
                    'success' => true,
                    'events' => []
                ]);
            }

            $events = [];

            // 1. Get Class Meetings (Zoom sessions)
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereBetween('meeting_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->get();

            foreach ($meetings as $meeting) {
                $events[] = [
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->title,
                    'start' => Carbon::parse($meeting->meeting_date)->toISOString(),
                    'type' => 'meeting',
                    'description' => $meeting->description,
                    'program' => $meeting->batch->program->program_name ?? 'N/A',
                    'batch' => $meeting->batch->batch_name ?? 'N/A',
                    'professor' => $meeting->professor->professor_name ?? 'N/A',
                    'meeting_url' => $meeting->meeting_url,
                    'status' => $meeting->status,
                    'duration' => $meeting->duration_minutes ?? 60,
                    'color' => $this->getEventColor('meeting', $meeting->status)
                ];
            }

            // 2. Get Assignment Due Dates
            $programIds = Enrollment::where('user_id', $userId)
                ->where('enrollment_status', 'approved')
                ->join('student_batches', 'enrollments.batch_id', '=', 'student_batches.batch_id')
                ->pluck('student_batches.program_id')
                ->unique()
                ->toArray();

            if (!empty($programIds)) {
                $assignments = Assignment::with(['professor', 'program'])
                    ->whereIn('program_id', $programIds)
                    ->whereBetween('due_date', [$startDate, $endDate])
                    ->where('is_active', true)
                    ->get();

                foreach ($assignments as $assignment) {
                    $events[] = [
                        'id' => 'assignment_' . $assignment->assignment_id,
                        'title' => 'Due: ' . $assignment->title,
                        'start' => Carbon::parse($assignment->due_date)->toISOString(),
                        'type' => 'assignment',
                        'description' => $assignment->description,
                        'instructions' => $assignment->instructions,
                        'max_points' => $assignment->max_points,
                        'program' => $assignment->program->program_name ?? 'N/A',
                        'professor' => $assignment->professor->professor_name ?? 'N/A',
                        'color' => $this->getEventColor('assignment')
                    ];
                }
            }

            // 3. Get Announcements with specific dates
            if (!empty($programIds)) {
                $announcements = Announcement::with(['professor'])
                    ->where(function($query) use ($programIds) {
                        $query->whereIn('program_id', $programIds)
                              ->orWhereJsonContains('target_programs', $programIds)
                              ->orWhere('target_scope', 'all');
                    })
                    ->where('is_published', true)
                    ->where('is_active', true)
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('publish_date', [$startDate, $endDate])
                              ->orWhereBetween('expire_date', [$startDate, $endDate]);
                    })
                    ->get();

                foreach ($announcements as $announcement) {
                    // Use publish_date as the event date
                    $eventDate = $announcement->publish_date ?? $announcement->created_at;
                    
                    $events[] = [
                        'id' => 'announcement_' . $announcement->announcement_id,
                        'title' => '📢 ' . $announcement->title,
                        'start' => Carbon::parse($eventDate)->toISOString(),
                        'type' => 'announcement',
                        'description' => $announcement->description,
                        'content' => $announcement->content,
                        'announcement_type' => $announcement->type,
                        'video_link' => $announcement->video_link,
                        'professor' => $announcement->professor->professor_name ?? 'Admin',
                        'expire_date' => $announcement->expire_date,
                        'color' => $this->getEventColor('announcement', $announcement->type)
                    ];
                }
            }

            // Sort events by date
            usort($events, function($a, $b) {
                return strcmp($a['start'], $b['start']);
            });

            return response()->json([
                'success' => true,
                'events' => $events,
                'meta' => [
                    'total_events' => count($events),
                    'meetings' => count(array_filter($events, fn($e) => $e['type'] === 'meeting')),
                    'assignments' => count(array_filter($events, fn($e) => $e['type'] === 'assignment')),
                    'announcements' => count(array_filter($events, fn($e) => $e['type'] === 'announcement')),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting calendar events', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading calendar events'
            ], 500);
        }
    }

    /**
     * Get today's schedule
     */
    public function getTodaySchedule()
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }

            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();

            // Get user's enrolled batches
            $enrolledBatches = Enrollment::where('user_id', $userId)
                ->where('enrollment_status', 'approved')
                ->pluck('batch_id')
                ->toArray();

            if (empty($enrolledBatches)) {
                return response()->json([
                    'success' => true,
                    'events' => []
                ]);
            }

            $events = [];

            // Get today's meetings
            $meetings = ClassMeeting::with(['batch.program', 'professor'])
                ->whereIn('batch_id', $enrolledBatches)
                ->whereBetween('meeting_date', [$today, $tomorrow])
                ->where('status', '!=', 'cancelled')
                ->orderBy('meeting_date')
                ->get();

            foreach ($meetings as $meeting) {
                $events[] = [
                    'id' => 'meeting_' . $meeting->meeting_id,
                    'title' => $meeting->title,
                    'time' => Carbon::parse($meeting->meeting_date)->format('H:i A'),
                    'type' => 'meeting',
                    'description' => $meeting->description,
                    'program' => $meeting->batch->program->program_name ?? 'N/A',
                    'professor' => $meeting->professor->professor_name ?? 'N/A',
                    'meeting_url' => $meeting->meeting_url,
                    'status' => $meeting->status
                ];
            }

            // Get today's assignment due dates
            $programIds = Enrollment::where('user_id', $userId)
                ->where('enrollment_status', 'approved')
                ->join('student_batches', 'enrollments.batch_id', '=', 'student_batches.batch_id')
                ->pluck('student_batches.program_id')
                ->unique()
                ->toArray();

            if (!empty($programIds)) {
                $assignments = Assignment::with(['professor', 'program'])
                    ->whereIn('program_id', $programIds)
                    ->whereBetween('due_date', [$today, $tomorrow])
                    ->where('is_active', true)
                    ->get();

                foreach ($assignments as $assignment) {
                    $events[] = [
                        'id' => 'assignment_' . $assignment->assignment_id,
                        'title' => 'Due: ' . $assignment->title,
                        'time' => Carbon::parse($assignment->due_date)->format('H:i A'),
                        'type' => 'assignment',
                        'description' => $assignment->description,
                        'program' => $assignment->program->program_name ?? 'N/A',
                        'professor' => $assignment->professor->professor_name ?? 'N/A'
                    ];
                }
            }

            // Sort by time
            usort($events, function($a, $b) {
                return strcmp($a['time'], $b['time']);
            });

            return response()->json([
                'success' => true,
                'events' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting today schedule', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading today\'s schedule'
            ], 500);
        }
    }

    /**
     * Get event details
     */
    public function getEventDetails($type, $id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }

            switch ($type) {
                case 'meeting':
                    $event = ClassMeeting::with(['batch.program', 'professor'])
                        ->where('meeting_id', $id)
                        ->first();
                    break;
                case 'assignment':
                    $event = Assignment::with(['professor', 'program'])
                        ->where('assignment_id', $id)
                        ->first();
                    break;
                case 'announcement':
                    $event = Announcement::with(['professor'])
                        ->where('announcement_id', $id)
                        ->first();
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid event type']);
            }

            if (!$event) {
                return response()->json(['success' => false, 'message' => 'Event not found']);
            }

            return response()->json([
                'success' => true,
                'event' => $event
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting event details', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading event details'
            ], 500);
        }
    }

    /**
     * Get color for different event types
     */
    private function getEventColor($type, $status = null)
    {
        switch ($type) {
            case 'meeting':
                switch ($status) {
                    case 'ongoing':
                        return '#dc3545'; // Red for ongoing
                    case 'completed':
                        return '#28a745'; // Green for completed
                    case 'cancelled':
                        return '#6c757d'; // Gray for cancelled
                    default:
                        return '#007bff'; // Blue for scheduled
                }
            case 'assignment':
                return '#fd7e14'; // Orange for assignments
            case 'announcement':
                switch ($status) {
                    case 'urgent':
                        return '#dc3545'; // Red for urgent
                    case 'video':
                        return '#6f42c1'; // Purple for video announcements
                    default:
                        return '#20c997'; // Teal for general announcements
                }
            default:
                return '#6c757d'; // Gray for unknown
        }
    }
}
