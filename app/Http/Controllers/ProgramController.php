<?php

// app/Http/Controllers/ProgramController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{
    public function getPrograms()
    {
        try {
            // Get only active (non-archived) programs
            $programs = Program::where('is_archived', false)
                ->orderBy('program_name')
                ->get();

            Log::info('Programs fetched successfully', [
                'count' => $programs->count(),
                'programs' => $programs->pluck('program_name', 'program_id')->toArray()
            ]);

            return response()->json([
                'success' => true,
                'programs' => $programs,
                'count' => $programs->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching programs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

