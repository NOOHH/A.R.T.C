<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StudentModuleController extends Controller
{
    // …existing methods…

    /**
     * Return JSON list of active content items for a course.
     */
    public function getCourseContent($moduleId, $courseId)
    {
        try {
            // Fetch content items directly by course_id and module_id (if needed)
            $items = \App\Models\ContentItem::where('course_id', $courseId)
                ->where('is_active', 1)
                ->whereNotExists(function($query) {
                    // Exclude content items that are linked to draft quizzes
                    $query->select(DB::raw(1))
                          ->from('quizzes')
                          ->whereColumn('quizzes.content_id', 'content_items.id')
                          ->where('quizzes.is_draft', true);
                })
                ->orderBy('content_order')
                ->get(['id', 'content_title', 'content_type', 'attachment_path', 'content_description', 'content_url']);

            return response()->json([
                'success' => true,
                'content_items' => $items,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting course content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading content: ' . $e->getMessage()
            ], 500);
        }
    }
}
