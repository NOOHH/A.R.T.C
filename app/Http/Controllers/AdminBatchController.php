<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Program;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminBatchController extends Controller
{
    /**
     * Display a listing of batches
     */
    public function index()
    {
        $batches = Batch::with(['program', 'creator'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.admin-student-enrollment.batch-enroll', compact('batches'));
    }

    /**
     * Show the form for creating a new batch
     */
    public function create()
    {
        $programs = Program::where('is_archived', false)->get();
        return view('admin.admin-student-enrollment.create-batch', compact('programs'));
    }

    /**
     * Store a newly created batch
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_name' => 'required|string|max:255',
            'batch_description' => 'nullable|string',
            'batch_capacity' => 'required|integer|min:1|max:100',
            'batch_status' => 'required|in:available,ongoing,closed,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'enrollment_deadline' => 'nullable|date'
        ]);

        $validated['created_by_admin_id'] = session('user_id');

        Batch::create($validated);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully!');
    }

    /**
     * Display the specified batch
     */
    public function show(Batch $batch)
    {
        $batch->load(['program', 'enrollments.student', 'creator']);
        
        return view('admin.admin-student-enrollment.show-batch', compact('batch'));
    }

    /**
     * Show the form for editing the specified batch
     */
    public function edit(Batch $batch)
    {
        $programs = Program::where('is_archived', false)->get();
        return view('admin.admin-student-enrollment.edit-batch', compact('batch', 'programs'));
    }

    /**
     * Update the specified batch
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_name' => 'required|string|max:255',
            'batch_description' => 'nullable|string',
            'batch_capacity' => 'required|integer|min:1|max:100',
            'batch_status' => 'required|in:available,ongoing,closed,completed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'enrollment_deadline' => 'nullable|date'
        ]);

        // Don't allow reducing capacity below current enrollment count
        $currentEnrollmentCount = $batch->getCurrentEnrollmentCount();
        if ($validated['batch_capacity'] < $currentEnrollmentCount) {
            return back()->withErrors([
                'batch_capacity' => "Cannot reduce capacity below current enrollment count ({$currentEnrollmentCount} students)"
            ]);
        }

        $batch->update($validated);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch updated successfully!');
    }

    /**
     * Remove the specified batch
     */
    public function destroy(Batch $batch)
    {
        // Check if there are any enrollments
        if ($batch->enrollments()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete batch with existing enrollments. Please move students to another batch first.'
            ]);
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully!');
    }

    /**
     * Get batches for a specific program (AJAX)
     */
    public function getBatchesForProgram(Request $request, $programId)
    {
        $batches = Batch::where('program_id', $programId)
            ->where('batch_status', '!=', 'completed')
            ->withCount('enrollments')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_name' => $batch->batch_name,
                    'batch_status' => $batch->batch_status,
                    'current_enrollment' => $batch->enrollments_count,
                    'batch_capacity' => $batch->batch_capacity,
                    'status_display' => $batch->status_display,
                    'is_available' => $batch->isAvailableForEnrollment(),
                    'enrollment_deadline' => $batch->enrollment_deadline ? $batch->enrollment_deadline->format('M d, Y') : null
                ];
            });

        return response()->json($batches);
    }

    /**
     * Move student to different batch
     */
    public function moveStudent(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,enrollment_id',
            'new_batch_id' => 'required|exists:batches,id'
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);
        $newBatch = Batch::findOrFail($validated['new_batch_id']);

        // Check if new batch has capacity
        if ($newBatch->isFull()) {
            return back()->withErrors(['error' => 'Target batch is full.']);
        }

        // Check if new batch is available
        if (!$newBatch->isAvailableForEnrollment()) {
            return back()->withErrors(['error' => 'Target batch is not available for enrollment.']);
        }

        $enrollment->update(['batch_id' => $validated['new_batch_id']]);

        return back()->with('success', 'Student moved to new batch successfully!');
    }

    /**
     * Toggle batch status
     */
    public function toggleStatus(Batch $batch)
    {
        $statusCycle = [
            'available' => 'ongoing',
            'ongoing' => 'closed',
            'closed' => 'completed',
            'completed' => 'available'
        ];

        $newStatus = $statusCycle[$batch->batch_status] ?? 'available';
        $batch->update(['batch_status' => $newStatus]);

        return back()->with('success', "Batch status changed to: " . ucfirst($newStatus));
    }

    /**
     * Get batch statistics (AJAX)
     */
    public function getStatistics()
    {
        $stats = [
            'total_batches' => Batch::count(),
            'available_batches' => Batch::where('batch_status', 'available')->count(),
            'ongoing_batches' => Batch::where('batch_status', 'ongoing')->count(),
            'completed_batches' => Batch::where('batch_status', 'completed')->count(),
            'total_enrolled_students' => DB::table('enrollments')
                ->whereNotNull('batch_id')
                ->count(),
        ];

        return response()->json($stats);
    }
}
