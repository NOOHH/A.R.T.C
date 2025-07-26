<?php

namespace App\Http\Controllers;

use App\Models\ClassMeeting;
use App\Models\MeetingAttendanceLog;
use App\Models\StudentBatch;
use App\Models\Professor;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClassMeetingController extends Controller
{
    public function index()
    {
        $meetings = ClassMeeting::with(['batch.program', 'professor', 'creator'])
            ->orderBy('meeting_date', 'desc')
            ->paginate(15);

        // Get all programs, batches, and professors for the create modal
        $professorPrograms = Program::where('status', 'active')->get();
        $professorBatches = StudentBatch::with('program')
            ->where('batch_status', '!=', 'closed')
            ->orderBy('start_date', 'asc')
            ->get();
        $professors = Professor::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('admin.meetings.index', compact('meetings', 'professorPrograms', 'professorBatches', 'professors'));
    }

    public function create()
    {
        $batches = StudentBatch::with(['program', 'professors'])
            ->where('batch_status', '!=', 'closed')
            ->orderBy('start_date', 'asc')
            ->get();

        return view('admin.meetings.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'programs' => 'required|array|min:1',
            'programs.*' => 'exists:programs,program_id',
            'batches' => 'required|array|min:1',
            'batches.*' => 'exists:student_batches,batch_id',
            'professor_id' => 'required|exists:professors,professor_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'meeting_url' => 'nullable|url',
            'url_visible_before_meeting' => 'boolean',
            'url_visibility_minutes_before' => 'nullable|integer|min:0|max:1440',
            'attached_files.*' => 'nullable|file|max:10240' // 10MB max per file
        ]);

        // Handle file uploads
        $attachedFiles = [];
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                $path = $file->store('meeting-files', 'public');
                $attachedFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        // Create a meeting for each selected batch
        $createdMeetings = [];
        foreach ($request->batches as $batchId) {
            $meeting = ClassMeeting::create([
                'batch_id' => $batchId,
                'professor_id' => $request->professor_id,
                'title' => $request->title,
                'description' => $request->description,
                'meeting_date' => $request->meeting_date,
                'duration_minutes' => $request->duration_minutes,
                'meeting_url' => $request->meeting_url,
                'attached_files' => $attachedFiles,
                'url_visible_before_meeting' => $request->boolean('url_visible_before_meeting'),
                'url_visibility_minutes_before' => $request->url_visibility_minutes_before ?? 0,
                'created_by' => session('admin_id') ?? session('user_id')
            ]);
            $createdMeetings[] = $meeting;
        }

        $batchCount = count($request->batches);
        $message = $batchCount === 1 
            ? 'Class meeting created successfully!' 
            : "Class meeting created successfully for {$batchCount} batches!";

        return redirect()->route('admin.meetings.index')->with('success', $message);
    }

    public function show(ClassMeeting $meeting)
    {
        $meeting->load([
            'batch.program', 
            'professor', 
            'creator',
            'attendanceLogs.student'
        ]);
        return view('admin.meetings.show', compact('meeting'));
    }

    public function edit(ClassMeeting $meeting)
    {
        $batches = StudentBatch::with(['program', 'professors'])
            ->where('batch_status', '!=', 'closed')
            ->orderBy('start_date', 'asc')
            ->get();

        return view('admin.meetings.edit', compact('meeting', 'batches'));
    }

    public function update(Request $request, ClassMeeting $meeting)
    {
        $request->validate([
            'batch_id' => 'required|exists:student_batches,batch_id',
            'professor_id' => 'required|exists:professors,professor_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'meeting_url' => 'nullable|url',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'url_visible_before_meeting' => 'boolean',
            'url_visibility_minutes_before' => 'nullable|integer|min:0|max:1440',
            'attached_files.*' => 'nullable|file|max:10240'
        ]);

        // Handle file uploads
        $attachedFiles = $meeting->attached_files ?? [];
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                $path = $file->store('meeting-files', 'public');
                $attachedFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        $meeting->update([
            'batch_id' => $request->batch_id,
            'professor_id' => $request->professor_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_date' => $request->meeting_date,
            'duration_minutes' => $request->duration_minutes,
            'meeting_url' => $request->meeting_url,
            'status' => $request->status,
            'attached_files' => $attachedFiles,
            'url_visible_before_meeting' => $request->boolean('url_visible_before_meeting'),
            'url_visibility_minutes_before' => $request->url_visibility_minutes_before ?? 0,
        ]);

        return redirect()->route('admin.meetings.index')->with('success', 'Class meeting updated successfully!');
    }

    public function destroy(ClassMeeting $meeting)
    {
        // Delete associated files
        if ($meeting->attached_files) {
            foreach ($meeting->attached_files as $file) {
                Storage::disk('public')->delete($file['path']);
            }
        }

        $meeting->delete();

        return redirect()->route('admin.meetings.index')->with('success', 'Class meeting deleted successfully!');
    }

    public function getBatchProfessors(Request $request)
    {
        $batch = StudentBatch::with('professors')->find($request->batch_id);
        
        if (!$batch) {
            return response()->json(['professors' => []]);
        }

        $professors = $batch->professors->map(function ($professor) {
            return [
                'professor_id' => $professor->professor_id,
                'professor_name' => $professor->professor_name
            ];
        });

        return response()->json(['professors' => $professors]);
    }

    public function downloadFile(ClassMeeting $meeting, $fileIndex)
    {
        if (!$meeting->attached_files || !isset($meeting->attached_files[$fileIndex])) {
            abort(404);
        }

        $file = $meeting->attached_files[$fileIndex];
        $filePath = storage_path('app/public/' . $file['path']);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $file['original_name']);
    }

    /**
     * Update attendance status via drag and drop
     */
    public function updateAttendance(Request $request, $meetingId)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500'
        ]);

        $meeting = ClassMeeting::findOrFail($meetingId);
        
        $log = MeetingAttendanceLog::firstOrCreate([
            'meeting_id' => $meetingId,
            'student_id' => $request->student_id
        ]);

        $log->markAttendance(
            $request->status, 
            session('user_id'), // Can be admin or professor
            $request->notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'student_name' => $log->student->firstname . ' ' . $log->student->lastname,
            'status' => $log->getStatusInfo()
        ]);
    }

    /**
     * Log when student clicks meeting link
     */
    public function logLinkClick($meetingId)
    {
        $studentId = session('student_id');
        if (!$studentId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $meeting = ClassMeeting::findOrFail($meetingId);
        
        // Check if URL should be visible
        if (!$meeting->isUrlVisible()) {
            return response()->json([
                'error' => 'Meeting link is not yet available',
                'minutes_until_visible' => $meeting->getTimeUntilUrlVisible()
            ], 403);
        }

        // Log the click
        $log = MeetingAttendanceLog::logLinkClick($meetingId, $studentId, request());

        return response()->json([
            'success' => true,
            'meeting_url' => $meeting->meeting_url,
            'message' => 'Link access logged'
        ]);
    }

    /**
     * Get meeting details for student
     */
    public function getStudentMeeting($meetingId)
    {
        $studentId = session('student_id');
        if (!$studentId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $meeting = ClassMeeting::with(['batch.program', 'professor'])
            ->findOrFail($meetingId);

        // Check if student is in this batch
        $student = Student::find($studentId);
        $isInBatch = $student->batches()->where('batch_id', $meeting->batch_id)->exists();
        
        if (!$isInBatch) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $attendanceLog = MeetingAttendanceLog::where('meeting_id', $meetingId)
            ->where('student_id', $studentId)
            ->first();

        return response()->json([
            'meeting' => [
                'title' => $meeting->title,
                'description' => $meeting->description,
                'meeting_date' => $meeting->meeting_date->format('Y-m-d H:i:s'),
                'duration_minutes' => $meeting->duration_minutes,
                'professor_name' => $meeting->professor->professor_name,
                'batch_name' => $meeting->batch->batch_name,
                'program_name' => $meeting->batch->program->program_name,
                'url_visible' => $meeting->isUrlVisible(),
                'minutes_until_visible' => $meeting->getTimeUntilUrlVisible(),
                'attached_files' => $meeting->attached_files,
                'status' => $meeting->status
            ],
            'attendance' => $attendanceLog ? [
                'status' => $attendanceLog->attendance_status,
                'clicked_link' => $attendanceLog->hasClickedLink(),
                'click_time' => $attendanceLog->link_clicked_at,
                'notes' => $attendanceLog->notes
            ] : null
        ]);
    }

    /**
     * Get meetings for professor attendance management
     */
    public function professorMeetings()
    {
        $professorId = session('user_id');
        
        $meetings = ClassMeeting::with(['batch.program', 'attendanceLogs.student'])
            ->where('professor_id', $professorId)
            ->orderBy('meeting_date', 'desc')
            ->get();

        return view('professor.meetings.index', compact('meetings'));
    }

    /**
     * Professor creates a new meeting
     */
    public function professorStore(Request $request)
    {
        $professorId = session('user_id');
        
        // Check if professor has permission to create meetings
        if (!$this->canProfessorCreateMeetings($professorId)) {
            return back()->with('error', 'You do not have permission to create meetings.');
        }

        $request->validate([
            'batch_id' => 'required|exists:student_batches,batch_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'meeting_url' => 'nullable|url',
            'url_visible_before_meeting' => 'boolean',
            'url_visibility_minutes_before' => 'nullable|integer|min:0|max:1440',
        ]);

        // Verify professor is assigned to this batch
        $batch = StudentBatch::whereHas('professors', function($query) use ($professorId) {
            $query->where('professor_id', $professorId);
        })->findOrFail($request->batch_id);

        $meeting = new ClassMeeting();
        $meeting->batch_id = $request->batch_id;
        $meeting->professor_id = $professorId;
        $meeting->title = $request->title;
        $meeting->description = $request->description;
        $meeting->meeting_date = $request->meeting_date;
        $meeting->duration_minutes = $request->duration_minutes;
        $meeting->meeting_url = $request->meeting_url;
        $meeting->url_visible_before_meeting = $request->has('url_visible_before_meeting');
        $meeting->url_visibility_minutes_before = $request->url_visibility_minutes_before ?? 0;
        $meeting->created_by = $professorId; // Professor created it

        $meeting->save();

        // Create attendance logs
        $this->createAttendanceLogsForMeeting($meeting);

        return redirect()->route('professor.meetings.index')
            ->with('success', 'Meeting created successfully!');
    }

    /**
     * Check if professor can create meetings
     */
    private function canProfessorCreateMeetings($professorId)
    {
        // Check global setting
        $meetingCreationEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_meeting_creation_enabled')
            ->value('setting_value') === '1';

        if (!$meetingCreationEnabled) {
            // Check if professor is whitelisted
            $whitelist = \App\Models\AdminSetting::where('setting_key', 'professor_meeting_whitelist')
                ->value('setting_value');
            
            if ($whitelist) {
                $whitelistedIds = json_decode($whitelist, true) ?? [];
                return in_array($professorId, $whitelistedIds);
            }
            
            return false;
        }

        return true;
    }

    /**
     * Get upcoming meetings for student dashboard
     */
    public function studentUpcomingMeetings()
    {
        $studentId = null;

        // Get student ID from session
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
            if ($student) {
                $studentId = $student->student_id;
            }
        }

        if (!$studentId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        // Get student's batch IDs through enrollments
        $studentBatches = DB::table('enrollments')
            ->join('student_batches', 'enrollments.batch_id', '=', 'student_batches.batch_id')
            ->where('enrollments.student_id', $studentId)
            ->where('enrollments.enrollment_status', 'approved')
            ->pluck('student_batches.batch_id');

        $meetings = ClassMeeting::with(['batch.program', 'professor'])
            ->whereIn('batch_id', $studentBatches)
            ->where('meeting_date', '>=', now()->subHours(3)) // Include meetings from 3 hours ago
            ->where('status', '!=', 'cancelled')
            ->orderBy('meeting_date', 'asc')
            ->limit(10)
            ->get();

        return response()->json($meetings->map(function($meeting) use ($studentId) {
            $attendanceLog = MeetingAttendanceLog::where('meeting_id', $meeting->meeting_id)
                ->where('student_id', $studentId)
                ->first();

            return [
                'meeting_id' => $meeting->meeting_id,
                'title' => $meeting->title,
                'description' => $meeting->description,
                'meeting_date' => $meeting->meeting_date,
                'duration_minutes' => $meeting->duration_minutes,
                'professor_name' => $meeting->professor->professor_name,
                'batch_name' => $meeting->batch->batch_name,
                'program_name' => $meeting->batch->program->program_name,
                'meeting_url' => $meeting->meeting_url,
                'status' => $meeting->status,
                'attendance_status' => $attendanceLog ? $attendanceLog->attendance_status : 'absent',
                'clicked_link' => $attendanceLog ? $attendanceLog->hasClickedLink() : false
            ];
        }));
    }

    /**
     * Create attendance log entries for all students in a batch
     */
    private function createAttendanceLogsForMeeting(ClassMeeting $meeting)
    {
        $students = Student::whereHas('enrollments', function($query) use ($meeting) {
            $query->where('batch_id', $meeting->batch_id);
        })->get();

        foreach ($students as $student) {
            MeetingAttendanceLog::firstOrCreate([
                'meeting_id' => $meeting->meeting_id,
                'student_id' => $student->student_id
            ], [
                'attendance_status' => 'absent'
            ]);
        }
    }

    /**
     * Get batches for a program (for meeting creation form)
     */
    public function getBatchesByProgram($programId)
    {
        $batches = StudentBatch::where('program_id', $programId)
            ->where('status', 'active')
            ->select('id', 'batch_name')
            ->get();

        return response()->json(['batches' => $batches]);
    }

    /**
     * Student meetings view
     */
    public function studentMeetings()
    {
        $studentId = null;
        $student = null;

        // Get student ID from session or Auth
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
            if ($student) {
                $studentId = $student->student_id;
            }
        }

        if (!$studentId) {
            return redirect()->route('student.dashboard')->with('error', 'Student record not found.');
        }

        // Get student's batch IDs through enrollments
        $studentBatches = DB::table('enrollments')
            ->join('student_batches', 'enrollments.batch_id', '=', 'student_batches.batch_id')
            ->where('enrollments.student_id', $studentId)
            ->where('enrollments.enrollment_status', 'approved')
            ->pluck('student_batches.batch_id');

        // Get all meetings for student's batches
        $allMeetings = ClassMeeting::with(['batch.program', 'professor', 'attendanceLogs'])
            ->whereIn('batch_id', $studentBatches)
            ->orderBy('meeting_date', 'asc')
            ->get();

        // Categorize meetings
        $currentMeetings = $allMeetings->filter(function($meeting) {
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
            return $meetingDate->isToday() && $meeting->actual_start_time && !$meeting->actual_end_time && $meeting->status !== 'completed';
        });

        $nonCurrentMeetings = $allMeetings->reject(function($meeting) use ($currentMeetings) {
            return $currentMeetings->contains('meeting_id', $meeting->meeting_id);
        });

        $todaysMeetings = $nonCurrentMeetings->filter(function($meeting) {
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
            return $meetingDate->isToday() && !$meeting->actual_start_time && $meeting->status !== 'completed';
        });

        $upcomingMeetings = $nonCurrentMeetings->filter(function($meeting) {
            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
            return $meetingDate->isAfter(\Carbon\Carbon::today()) && !$meeting->actual_start_time && $meeting->status !== 'completed';
        });

        $finishedMeetings = $allMeetings->filter(function($meeting) {
            return $meeting->status === 'completed' || ($meeting->actual_end_time && \Carbon\Carbon::parse($meeting->actual_end_time)->isPast());
        });

        // Optionally, keep pastMeetings for attendance stats if needed
        $pastMeetings = collect();

        // Calculate attendance statistics
        $totalMeetings = $finishedMeetings->count();
        $attendedMeetings = 0;
        
        foreach ($finishedMeetings as $meeting) {
            $attendanceLog = $meeting->attendanceLogs->where('student_id', $studentId)->first();
            if ($attendanceLog && $attendanceLog->attendance_status === 'present') {
                $attendedMeetings++;
            }
        }
        
        $attendanceRate = $totalMeetings > 0 ? round(($attendedMeetings / $totalMeetings) * 100) : 0;

        // Get student programs for sidebar (to fix the error)
        $studentPrograms = [];
        if ($student) {
            $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
                ->orWhere('user_id', $student->user_id)
                ->with(['program', 'package'])
                ->where('enrollment_status', '!=', 'rejected')
                ->get();

            foreach ($enrollments as $enrollment) {
                if ($enrollment->program) {
                    $studentPrograms[] = [
                        'program_id' => $enrollment->program->program_id,
                        'program_name' => $enrollment->program->program_name,
                        'package_name' => $enrollment->package->package_name ?? 'Standard Package'
                    ];
                }
            }
        }

        // Create user object for sidebar
        $user = (object) [
            'id' => session('user_id'),
            'name' => session('user_name') ?? $student->firstname . ' ' . $student->lastname,
            'role' => 'student'
        ];

        return view('student.meetings', compact(
            'upcomingMeetings',
            'currentMeetings',
            'todaysMeetings',
            'finishedMeetings',
            'pastMeetings',
            'studentId',
            'totalMeetings',
            'attendedMeetings', 
            'attendanceRate',
            'user',
            'studentPrograms'
        ));
    }
    public function logStudentAccess(Request $request, $meetingId)
    {
        $studentId = null;

        // Get student ID from session
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
            if ($student) {
                $studentId = $student->student_id;
            }
        }

        if (!$studentId) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        // Log the access
        MeetingAttendanceLog::updateOrCreate(
            [
                'meeting_id' => $meetingId,
                'student_id' => $studentId
            ],
            [
                'status' => 'present',
                'accessed_at' => now(),
                'access_method' => 'link_click'
            ]
        );

        return response()->json(['success' => true, 'message' => 'Meeting access logged']);
    }

    /**
     * Get meeting stats for professor modal
     */
    public function getMeetingStats($meetingId)
    {
        $meeting = ClassMeeting::with(['batch', 'attendanceLogs.student'])->findOrFail($meetingId);
        
        // Count total students in the batch
        $totalStudents = DB::table('enrollments')
            ->where('batch_id', $meeting->batch_id)
            ->where('enrollment_status', 'approved')
            ->count();
        
        // Count students who have joined/accessed the meeting
        $joinedStudents = $meeting->attendanceLogs()
            ->whereNotNull('accessed_at')
            ->count();
        
        return response()->json([
            'success' => true,
            'total_students' => $totalStudents,
            'joined_students' => $joinedStudents,
            'meeting' => [
                'status' => $meeting->status,
                'actual_start_time' => $meeting->actual_start_time,
                'actual_end_time' => $meeting->actual_end_time
            ]
        ]);
    }
}
