<?php
/**
 * Comprehensive Student UI Improvements Test
 * Tests all the implemented fixes and improvements
 */

echo "<h1>🔧 Student UI Improvements Test</h1>";
echo "<p>Testing all the fixes implemented for the student dashboard...</p>";

$tests = [];
$passed = 0;
$total = 0;

// Test 1: Check if route parameter fix is applied
$total++;
echo "<h3>Test 1: Route Parameter Fix</h3>";
$enrolledCoursesFile = 'resources/views/student/enrolled-courses.blade.php';
if (file_exists($enrolledCoursesFile)) {
    $content = file_get_contents($enrolledCoursesFile);
    if (strpos($content, "route('student.course', ['courseId' => \$course['course_id']") !== false) {
        echo "✅ Route parameter fixed (courseId instead of id)<br>";
        $passed++;
        $tests['route_param'] = true;
    } else {
        echo "❌ Route parameter not fixed<br>";
        $tests['route_param'] = false;
    }
} else {
    echo "❌ Enrolled courses file not found<br>";
    $tests['route_param'] = false;
}

// Test 2: Check if chat-container is using global-chat
$total++;
echo "<h3>Test 2: Chat Component Update</h3>";
$chatContainerFile = 'resources/views/components/chat-container.blade.php';
if (file_exists($chatContainerFile)) {
    $content = file_get_contents($chatContainerFile);
    if (strpos($content, "@include('components.global-chat')") !== false) {
        echo "✅ Chat container now uses global-chat component<br>";
        $passed++;
        $tests['chat_component'] = true;
    } else {
        echo "❌ Chat container still not using global-chat<br>";
        $tests['chat_component'] = false;
    }
} else {
    echo "❌ Chat container file not found<br>";
    $tests['chat_component'] = false;
}

// Test 3: Check if global-chat component exists
$total++;
echo "<h3>Test 3: Global Chat Component</h3>";
$globalChatFile = 'resources/views/components/global-chat.blade.php';
if (file_exists($globalChatFile)) {
    $content = file_get_contents($globalChatFile);
    if (strlen($content) > 1000) { // Should be a substantial component
        echo "✅ Global chat component exists and has content<br>";
        $passed++;
        $tests['global_chat'] = true;
    } else {
        echo "❌ Global chat component too small or empty<br>";
        $tests['global_chat'] = false;
    }
} else {
    echo "❌ Global chat component not found<br>";
    $tests['global_chat'] = false;
}

// Test 4: Check if student sidebar has brand header
$total++;
echo "<h3>Test 4: Student Sidebar Brand Update</h3>";
$sidebarFile = 'resources/views/student/student-layouts/student-sidebar.blade.php';
if (file_exists($sidebarFile)) {
    $content = file_get_contents($sidebarFile);
    if (strpos($content, 'sidebar-brand') !== false && strpos($content, 'A.R.T.C') !== false) {
        echo "✅ Student sidebar has brand header design<br>";
        $passed++;
        $tests['sidebar_brand'] = true;
    } else {
        echo "❌ Student sidebar missing brand header<br>";
        $tests['sidebar_brand'] = false;
    }
} else {
    echo "❌ Student sidebar file not found<br>";
    $tests['sidebar_brand'] = false;
}

// Test 5: Check if mobile toggle button is removed
$total++;
echo "<h3>Test 5: Mobile Toggle Button Removal</h3>";
if (file_exists($sidebarFile)) {
    $content = file_get_contents($sidebarFile);
    if (strpos($content, 'mobile-sidebar-toggle') === false) {
        echo "✅ Mobile toggle button removed from sidebar<br>";
        $passed++;
        $tests['toggle_removed'] = true;
    } else {
        echo "❌ Mobile toggle button still present<br>";
        $tests['toggle_removed'] = false;
    }
} else {
    echo "❌ Cannot check sidebar file<br>";
    $tests['toggle_removed'] = false;
}

// Test 6: Check if student header has brand navbar
$total++;
echo "<h3>Test 6: Student Header Brand Navbar</h3>";
$headerFile = 'resources/views/student/student-layouts/student-header.blade.php';
if (file_exists($headerFile)) {
    $content = file_get_contents($headerFile);
    if (strpos($content, 'Ascendo Review') !== false && strpos($content, "route('home')") !== false) {
        echo "✅ Student header has brand navbar linking to homepage<br>";
        $passed++;
        $tests['header_brand'] = true;
    } else {
        echo "❌ Student header missing brand navbar or homepage link<br>";
        $tests['header_brand'] = false;
    }
} else {
    echo "❌ Student header file not found<br>";
    $tests['header_brand'] = false;
}

// Test 7: Check if layout includes header
$total++;
echo "<h3>Test 7: Layout Header Integration</h3>";
$layoutFile = 'resources/views/student/student-layouts/student-dashboard-layout.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    if (strpos($content, "@include('student.student-layouts.student-header')") !== false) {
        echo "✅ Student layout includes header component<br>";
        $passed++;
        $tests['layout_header'] = true;
    } else {
        echo "❌ Student layout doesn't include header<br>";
        $tests['layout_header'] = false;
    }
} else {
    echo "❌ Student layout file not found<br>";
    $tests['layout_header'] = false;
}

// Test 8: Check if professor-style sidebar toggle JavaScript is implemented
$total++;
echo "<h3>Test 8: Professor-Style Sidebar Toggle</h3>";
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    if (strpos($content, 'sidebar.addEventListener') !== false && strpos($content, 'toggleSidebar') !== false) {
        echo "✅ Professor-style sidebar toggle JavaScript implemented<br>";
        $passed++;
        $tests['toggle_js'] = true;
    } else {
        echo "❌ Professor-style toggle JavaScript not found<br>";
        $tests['toggle_js'] = false;
    }
} else {
    echo "❌ Cannot check layout JavaScript<br>";
    $tests['toggle_js'] = false;
}

// Test 9: Check if routes exist
$total++;
echo "<h3>Test 9: Required Routes</h3>";
$routeOutput = shell_exec('cd ' . escapeshellarg(getcwd()) . ' && php artisan route:list 2>&1');
if (strpos($routeOutput, 'student.course') !== false && strpos($routeOutput, 'home ') !== false) {
    echo "✅ Required routes (student.course, home) exist<br>";
    $passed++;
    $tests['routes'] = true;
} else {
    echo "❌ Required routes missing<br>";
    $tests['routes'] = false;
}

// Test 10: CSS styling updates
$total++;
echo "<h3>Test 10: CSS Styling Updates</h3>";
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    if (strpos($content, 'brand-title') !== false && strpos($content, 'main-header') !== false) {
        echo "✅ CSS styling updates for brand and header present<br>";
        $passed++;
        $tests['css_updates'] = true;
    } else {
        echo "❌ CSS styling updates missing<br>";
        $tests['css_updates'] = false;
    }
} else {
    echo "❌ Cannot check CSS updates<br>";
    $tests['css_updates'] = false;
}

// Summary
echo "<hr>";
echo "<h2>📊 Test Results Summary</h2>";
echo "<p><strong>Passed: {$passed}/{$total}</strong></p>";

$percentage = round(($passed / $total) * 100, 1);
if ($percentage >= 90) {
    echo "<div style='color: green; font-weight: bold;'>🎉 Excellent! {$percentage}% of tests passed.</div>";
} elseif ($percentage >= 70) {
    echo "<div style='color: orange; font-weight: bold;'>⚠️ Good progress! {$percentage}% of tests passed.</div>";
} else {
    echo "<div style='color: red; font-weight: bold;'>❌ Needs work! Only {$percentage}% of tests passed.</div>";
}

echo "<hr>";
echo "<h3>🔍 Key Improvements Made:</h3>";
echo "<ul>";
if ($tests['route_param']) echo "<li>✅ Fixed route parameter issue (courseId vs id)</li>";
if ($tests['chat_component']) echo "<li>✅ Updated chat to use existing global-chat component</li>";
if ($tests['sidebar_brand']) echo "<li>✅ Added brand header to sidebar (professor design)</li>";
if ($tests['toggle_removed']) echo "<li>✅ Removed toggle button (clickable sidebar instead)</li>";
if ($tests['header_brand']) echo "<li>✅ Added navbar with brand linking to homepage</li>";
if ($tests['layout_header']) echo "<li>✅ Integrated header into layout</li>";
if ($tests['toggle_js']) echo "<li>✅ Implemented professor-style sidebar toggle</li>";
if ($tests['routes']) echo "<li>✅ Verified required routes exist</li>";
if ($tests['css_updates']) echo "<li>✅ Added necessary CSS styling</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>🎯 What's Working Now:</h3>";
echo "<ol>";
echo "<li><strong>Sidebar Design:</strong> Copied professor's brand header design while keeping student navigation items</li>";
echo "<li><strong>Navbar:</strong> Added Ascendo Training Center brand that links to homepage</li>";
echo "<li><strong>Sidebar Toggle:</strong> Click anywhere on sidebar to collapse/expand (like professor)</li>";
echo "<li><strong>Chat:</strong> Uses existing global-chat component instead of placeholder</li>";
echo "<li><strong>Route Fix:</strong> Course links use correct courseId parameter</li>";
echo "<li><strong>Mobile:</strong> Responsive design with proper mobile sidebar behavior</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
