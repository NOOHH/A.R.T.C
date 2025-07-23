<?php

namespace App\Http\Controllers;

use App\Models\ClassMeeting;
use App\Models\Professor;
use App\Models\Program;
use App\Models\StudentBatch;
use App\Models\AdminSetting;
use App\Models\MeetingAttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfessorMeetingController extends Controller
{
    /**
     * Check if professor can create meetings
     */
    private function canCreateMeetings($professorId)
    {
        // Check whitelist first: whitelisted professors always can create
        $whitelist = AdminSetting::getValue('meeting_whitelist_professors', '');
        if (!empty($whitelist)) {
            $ids = array_filter(explode(',', $whitelist));
            return in_array($professorId, $ids);
        }
        // Otherwise use global toggle
        return AdminSetting::getValue('meeting_creation_enabled', '1') === '1';
    }

    public function index()
    {
        $professorId = session('professor_id');
        $professor = Professor::with(['programs'])->findOrFail($professorId);

        // Get batches assigned to this professor
        $professorBatches = StudentBatch::with(['program'])
            ->where('professor_id', $professorId)
            ->get();

        // Get all programs that have batches assigned to this professor
        $programsWithBatches = $professorBatches->pluck('program')->unique('program_id');
        
        // Combine professor's assigned programs with programs that have his batches
        $allRelevantPrograms = $professor->programs()->get()->merge($programsWithBatches)->unique('program_id');

        $professorPrograms = collect();
        
        // Organize batches by program
        foreach ($allRelevantPrograms as $program) {
            $programBatches = $professorBatches->where('program_id', $program->program_id);
            
            if ($programBatches->count() > 0) {
                $program->batches = $programBatches;
                
                foreach ($program->batches as $batch) {
                    // Get all meetings for this batch
                    $allMeetings = ClassMeeting::with(['batch.program'])
                        ->where('professor_id', $professorId)
                        ->where('batch_id', $batch->batch_id)
                        ->orderBy('meeting_date', 'asc')
                        ->get();

                    // Categorize meetings
                    $now = now();
                    $today = $now->startOfDay();
                    $endOfToday = $now->copy()->endOfDay();

                    $batch->currentMeetings = $allMeetings->filter(function ($meeting) {
                        return $meeting->status == 'ongoing';
                    });

                    $batch->todayMeetings = $allMeetings->filter(function ($meeting) use ($today, $endOfToday) {
                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        return $meetingDate->between($today, $endOfToday) && $meeting->status != 'completed';
                    });

                    $batch->upcomingMeetings = $allMeetings->filter(function ($meeting) use ($endOfToday) {
                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        return $meetingDate->gt($endOfToday) && $meeting->status == 'scheduled';
                    });

                    $batch->finishedMeetings = $allMeetings->filter(function ($meeting) {
                        return $meeting->status == 'completed';
                    });
                }
                
                $professorPrograms->push($program);
            }
        }

        // Get professor's programs with meeting links for the create form
        $programs = $allRelevantPrograms;

        // Get professor's batches for the create form
        $batches = $professorBatches;

        // Get all meetings for statistics
        $meetings = ClassMeeting::where('professor_id', $professorId)
            ->orderBy('meeting_date', 'desc')
            ->get();

        // Check if professor can create meetings
        $canCreateMeetings = $this->canCreateMeetings($professorId);

        return view('professor.meetings.index', compact('professorPrograms', 'programs', 'batches', 'professor', 'canCreateMeetings', 'meetings'));
    }

    public function store(Request $request)
    {
        $professorId = session('professor_id');
        
        // Check if professor can create meetings
        if (!$this->canCreateMeetings($professorId)) {
            return back()->withErrors(['general' => 'You do not have permission to create meetings. Please contact your administrator.']);
        }

        $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date|after:now',
            'program_ids' => 'required|array|min:1',
            'program_ids.*' => 'exists:programs,program_id',
            'batch_ids' => 'required|array|min:1',
            'batch_ids.*' => 'exists:student_batches,batch_id',
            'meeting_link' => 'required|url',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean'
        ]);

        $professor = Professor::findOrFail($professorId);
        
        // Verify professor has access to all selected batches
        $accessibleBatchIds = StudentBatch::where('professor_id', $professorId)->pluck('batch_id')->toArray();
        $requestedBatchIds = $request->batch_ids;
        
        $invalidBatchIds = array_diff($requestedBatchIds, $accessibleBatchIds);
        if (!empty($invalidBatchIds)) {
            return back()->withErrors(['batch_ids' => 'You do not have access to some of the selected batches.']);
        }

        // Verify that selected batches belong to selected programs
        $selectedBatches = StudentBatch::whereIn('batch_id', $requestedBatchIds)->get();
        $batchProgramIds = $selectedBatches->pluck('program_id')->unique()->toArray();
        $requestedProgramIds = $request->program_ids;
        
        $invalidProgramBatchCombination = array_diff($batchProgramIds, $requestedProgramIds);
        if (!empty($invalidProgramBatchCombination)) {
            return back()->withErrors(['batch_ids' => 'Some selected batches do not belong to the selected programs.']);
        }

        // Create or update meetings for each selected batch
        $createdMeetings = [];
        
        DB::transaction(function () use ($request, $professorId, $requestedBatchIds, &$createdMeetings) {
            foreach ($requestedBatchIds as $batchId) {
                $meeting = \App\Models\ClassMeeting::where('professor_id', $professorId)
                    ->where('batch_id', $batchId)
                    ->where('meeting_date', $request->meeting_date)
                    ->where('title', $request->meeting_title)
                    ->first();
                if ($meeting) {
                    $meeting->update([
                        'meeting_url' => $request->meeting_link,
                        'description' => $request->description,
                        'status' => 'scheduled',
                        'created_by' => auth()->id() ?? 1
                    ]);
                } else {
                    $meeting = \App\Models\ClassMeeting::create([
                        'professor_id' => $professorId,
                        'batch_id' => $batchId,
                        'title' => $request->meeting_title,
                        'meeting_date' => $request->meeting_date,
                        'meeting_url' => $request->meeting_link,
                        'description' => $request->description,
                        'status' => 'scheduled',
                        'created_by' => auth()->id() ?? 1
                    ]);
                }
                $createdMeetings[] = $meeting;
            }
        });

        $batchCount = count($requestedBatchIds);
        $message = $batchCount === 1 
            ? 'Meeting created successfully!' 
            : "Meeting created successfully for {$batchCount} batches!";

        return redirect()->route('professor.meetings')
            ->with('success', $message);
    }

    public function show(ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $meeting->load(['batch.program', 'professor', 'attendanceLogs.student']);

        return view('professor.meetings.show', compact('meeting'));
    }

    public function update(Request $request, ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_link' => 'required|url',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled'
        ]);

        $meeting->update($request->only([
            'meeting_title', 'meeting_date', 'meeting_link', 
            'description', 'status'
        ]));

        return redirect()->route('professor.meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully!');
    }

    public function destroy(ClassMeeting $meeting)
    {
        // Verify professor owns this meeting
        if ($meeting->professor_id !== session('professor_id')) {
            abort(403, 'Unauthorized access to meeting.');
        }

        $meeting->delete();

        return redirect()->route('professor.meetings')
            ->with('success', 'Meeting deleted successfully!');
    }

    public function reports()
    {
        $professorId = session('professor_id');

        // Get meeting statistics
        $totalMeetings = ClassMeeting::where('professor_id', $professorId)->count();
        $completedMeetings = ClassMeeting::where('professor_id', $professorId)
            ->where('status', 'completed')->count();
        $upcomingMeetings = ClassMeeting::where('professor_id', $professorId)
            ->where('meeting_date', '>', now())->count();

        // Get recent meetings with attendance data
        $recentMeetings = ClassMeeting::with(['batch.program', 'attendanceLogs'])
            ->where('professor_id', $professorId)
            ->orderBy('meeting_date', 'desc')
            ->limit(10)
            ->get();

        return view('professor.meetings.reports', compact(
            'totalMeetings', 'completedMeetings', 'upcomingMeetings', 'recentMeetings'
        ));
    }

    public function start(Request $request, ClassMeeting $meeting)
    {
        $professorId = session('professor_id');

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Update meeting status and start time
            $meeting->update([
                'status' => 'ongoing',
                'actual_start_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting started successfully',
                'meeting' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finish(Request $request, ClassMeeting $meeting)
    {
        $professorId = session('professor_id');

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Update meeting status and end time
            $meeting->update([
                'status' => 'completed',
                'actual_end_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting finished successfully',
                'meeting' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to finish meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats(ClassMeeting $meeting)
    {
        $professorId = session('professor_id'); // Consistent with rest of controller

        // Verify professor owns this meeting
        if ($meeting->professor_id != $professorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Get total students in the batch via enrollments table
            $totalStudents = DB::table('enrollments')
                ->where('batch_id', $meeting->batch_id)
                ->where('enrollment_status', 'approved')
                ->count();
            
            // Get students who have joined the meeting
            $joinedStudents = MeetingAttendanceLog::where('meeting_id', $meeting->meeting_id)
                ->whereNotNull('joined_at')
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get meeting stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
