Route::get('/create-test-content', function() {
    try {
        echo "<h2>Creating Test Content</h2>";
        
        // First, let's see what modules exist
        $modules = \App\Models\Module::all();
        echo "<h3>Available Modules:</h3>";
        foreach ($modules as $module) {
            echo "<p>Module {$module->modules_id}: {$module->module_name} (Program: {$module->program_id})</p>";
        }
        
        // Pick module 45 or 46 for testing
        $moduleId = 45;
        $module = \App\Models\Module::find($moduleId);
        
        if (!$module) {
            echo "<p style='color: red;'>Module {$moduleId} not found. Using available module...</p>";
            $module = \App\Models\Module::first();
            $moduleId = $module->modules_id;
        }
        
        echo "<h3>Using Module: {$module->module_name} (ID: {$moduleId})</h3>";
        
        // Create or find a course for this module
        $course = \App\Models\Course::where('module_id', $moduleId)->first();
        if (!$course) {
            $course = \App\Models\Course::create([
                'subject_name' => 'Test Course for ' . $module->module_name,
                'subject_description' => 'A test course with sample content',
                'module_id' => $moduleId,
                'subject_price' => 100.00,
                'subject_order' => 1,
                'is_required' => true,
                'is_active' => true
            ]);
            echo "<p style='color: green;'>✅ Created course: {$course->subject_name} (ID: {$course->subject_id})</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Using existing course: {$course->subject_name} (ID: {$course->subject_id})</p>";
        }
        
        // Create or find lessons
        $lesson = \App\Models\Lesson::where('course_id', $course->subject_id)->first();
        if (!$lesson) {
            $lesson = \App\Models\Lesson::create([
                'lesson_name' => 'Introduction Lesson',
                'lesson_description' => 'An introductory lesson for the course',
                'course_id' => $course->subject_id,
                'lesson_price' => 0.00,
                'lesson_duration' => 60,
                'lesson_order' => 1,
                'is_required' => true,
                'is_active' => true,
                'learning_mode' => 'Both'
            ]);
            echo "<p style='color: green;'>✅ Created lesson: {$lesson->lesson_name} (ID: {$lesson->lesson_id})</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Using existing lesson: {$lesson->lesson_name} (ID: {$lesson->lesson_id})</p>";
        }
        
        // Create content items for the lesson
        $contentItems = [
            [
                'content_title' => 'Course Introduction PDF',
                'content_description' => 'Welcome to the course! This PDF contains an overview.',
                'content_type' => 'lesson',
                'attachment_path' => 'content/sample-intro.pdf'
            ],
            [
                'content_title' => 'Knowledge Check Quiz',
                'content_description' => 'Test your understanding of the introduction material.',
                'content_type' => 'quiz',
                'attachment_path' => null
            ],
            [
                'content_title' => 'Practice Assignment',
                'content_description' => 'Complete this assignment to practice what you learned.',
                'content_type' => 'assignment',
                'attachment_path' => 'content/assignment-template.docx'
            ]
        ];
        
        foreach ($contentItems as $itemData) {
            $existingItem = \App\Models\ContentItem::where('lesson_id', $lesson->lesson_id)
                ->where('content_title', $itemData['content_title'])
                ->first();
                
            if (!$existingItem) {
                $contentItem = \App\Models\ContentItem::create([
                    'content_title' => $itemData['content_title'],
                    'content_description' => $itemData['content_description'],
                    'lesson_id' => $lesson->lesson_id,
                    'course_id' => $course->subject_id,
                    'content_type' => $itemData['content_type'],
                    'content_data' => json_encode([]),
                    'attachment_path' => $itemData['attachment_path'],
                    'max_points' => $itemData['content_type'] === 'quiz' ? 100.00 : null,
                    'content_order' => 0,
                    'order' => 0,
                    'is_required' => true,
                    'is_active' => true
                ]);
                echo "<p style='color: green;'>✅ Created content item: {$contentItem->content_title} (Type: {$contentItem->content_type})</p>";
            } else {
                echo "<p style='color: blue;'>ℹ️ Content item already exists: {$itemData['content_title']}</p>";
            }
        }
        
        // Create direct course content (not linked to lessons)
        $directContentItems = [
            [
                'content_title' => 'Course Syllabus',
                'content_description' => 'Complete course syllabus and schedule',
                'content_type' => 'lesson',
                'attachment_path' => 'content/syllabus.pdf'
            ],
            [
                'content_title' => 'Reading Materials',
                'content_description' => 'Additional reading materials for the course',
                'content_type' => 'lesson',
                'attachment_path' => 'content/readings.pdf'
            ]
        ];
        
        foreach ($directContentItems as $itemData) {
            $existingItem = \App\Models\ContentItem::where('course_id', $course->subject_id)
                ->whereNull('lesson_id')
                ->where('content_title', $itemData['content_title'])
                ->first();
                
            if (!$existingItem) {
                $contentItem = \App\Models\ContentItem::create([
                    'content_title' => $itemData['content_title'],
                    'content_description' => $itemData['content_description'],
                    'lesson_id' => null,
                    'course_id' => $course->subject_id,
                    'content_type' => $itemData['content_type'],
                    'content_data' => json_encode([]),
                    'attachment_path' => $itemData['attachment_path'],
                    'max_points' => null,
                    'content_order' => 0,
                    'order' => 0,
                    'is_required' => false,
                    'is_active' => true
                ]);
                echo "<p style='color: green;'>✅ Created direct course content: {$contentItem->content_title}</p>";
            } else {
                echo "<p style='color: blue;'>ℹ️ Direct course content already exists: {$itemData['content_title']}</p>";
            }
        }
        
        echo "<h3>Test Content Creation Complete!</h3>";
        echo "<p><a href='/test-module-api/{$moduleId}' target='_blank'>Test the API endpoint</a></p>";
        echo "<p><a href='/student/module/{$moduleId}' target='_blank'>View the student module page</a></p>";
        
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});
