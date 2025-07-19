<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use Illuminate\Http\Request;

class StudentModuleController extends Controller
{
    // …existing methods…

    /**
     * Return JSON list of active content items for a course.
     */
    public function getCourseContent($moduleId, $courseId)
    {
        try {
            $items = ContentItem::where('course_id', $courseId)
                ->where('is_active', 1)
                ->orderBy('content_order')
                ->get(['id', 'content_title', 'content_type', 'attachment_path', 'content_description', 'content_url']);

            return response()->json([
                'success' => true,
                'content_items' => $items,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting course content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading content: ' . $e->getMessage()
            ], 500);
        }
    }
}
