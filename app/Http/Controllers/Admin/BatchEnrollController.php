<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentBatch;
use App\Models\Program;
use App\Models\Enrollment;
use App\Models\Student;
use Carbon\Carbon;

class BatchEnrollController extends Controller
{
    public function index()
    {
        $batches = StudentBatch::with('program')->orderBy('created_at', 'desc')->get();
        $programs = Program::where('is_archived', 0)->get();
        
        return view('admin.admin-student-enrollment.batch-enroll', compact('batches', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,program_id',
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'required|date',
            'start_date' => 'required|date', // Removed after:registration_deadline for flexibility
            'description' => 'nullable|string'
        ]);

        StudentBatch::create([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'current_capacity' => 0,
            'batch_status' => 'available',
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Batch created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'batch_name' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1',
            'batch_status' => 'required|in:available,ongoing,closed',
            'registration_deadline' => 'required|date',
            'start_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $batch = StudentBatch::findOrFail($id);
        $batch->update($request->all());

        return redirect()->back()->with('success', 'Batch updated successfully!');
    }

    public function destroy($id)
    {
        $batch = StudentBatch::findOrFail($id);
        $batch->delete();

        return redirect()->back()->with('success', 'Batch deleted successfully!');
    }

    public function getBatchesByProgram(Request $request)
    {
        $programId = $request->input('program_id');
        $batches = StudentBatch::where('program_id', $programId)
                              ->where('batch_status', '!=', 'closed')
                              ->where('registration_deadline', '>=', now())
                              ->with('program')
                              ->get();

        return response()->json($batches->map(function($batch) {
            return [
                'batch_id' => $batch->batch_id,
                'batch_name' => $batch->batch_name,
                'current_capacity' => $batch->current_capacity,
                'max_capacity' => $batch->max_capacity,
                'batch_status' => $batch->batch_status,
                'registration_deadline' => $batch->registration_deadline->format('M d, Y'),
                'start_date' => $batch->start_date->format('M d, Y'),
                'capacity_text' => $batch->current_capacity . '/' . $batch->max_capacity . ' students',
                'is_available' => $batch->isAvailable(),
                'status_text' => ucfirst($batch->batch_status) . ($batch->batch_status === 'ongoing' ? ' and Available to Join' : '')
            ];
        }));
    }
}
