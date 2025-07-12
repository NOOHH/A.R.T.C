<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Batch;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\BatchResource;
use Illuminate\Http\Request;

class ProgramApiController extends Controller
{
    /**
     * Get all programs for chat filtering
     */
    public function index()
    {
        $programs = Program::where('is_archived', false)
            ->withCount(['modules', 'students'])
            ->orderBy('program_name')
            ->get();
            
        return ProgramResource::collection($programs);
    }

    /**
     * Get batches for a specific program
     */
    public function batches(Request $request)
    {
        $request->validate([
            'program' => 'required|exists:programs,program_id'
        ]);

        $batches = Batch::where('program_id', $request->program)
            ->where('is_archived', false)
            ->orderBy('batch_name')
            ->get();

        return BatchResource::collection($batches);
    }
}
