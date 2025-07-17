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
        $items = ContentItem::where('course_id', $courseId)
            ->where('is_active', 1)
            ->orderBy('content_order')
            ->get(['content_title','content_type','attachment_path']);

        return response()->json([
            'success'       => true,
            'content_items' => $items,
        ]);
    }
}