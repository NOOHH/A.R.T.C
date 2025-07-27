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
     * Only shows published quizzes to students.
     */
    public function getCourseContent($moduleId, $courseId)
    {
        try {
            // Fetch content items for the course
            $items = \App\Models\ContentItem::where('course_id', $courseId)
                ->where('is_active', 1)
                ->orderBy('content_order')
                ->get(['id', 'content_title', 'content_type', 'attachment_path', 'content_description', 'content_url']);

            // Filter out quiz content that's not published
            $filteredItems = $items->filter(function($item) {
                // If it's not a quiz, include it
                if ($item->content_type !== 'quiz') {
                    return true;
                }
                
                // If it's a quiz, check if there's a published quiz linked to this content
                $publishedQuiz = \App\Models\Quiz::where('content_id', $item->id)
                    ->where('status', 'published')
                    ->where('is_active', true)
                    ->first();
                
                return $publishedQuiz !== null;
            });

            return response()->json([
                'success' => true,
                'content_items' => $filteredItems->values(), // Reset array keys
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
