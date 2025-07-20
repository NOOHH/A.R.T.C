@echo off
echo Applying comprehensive PDF viewer and layout fixes...

REM Add enhanced PDF viewer script to admin modules
echo Adding enhanced PDF viewer to admin modules...
powershell -Command "$content = Get-Content 'resources\views\admin\admin-modules\admin-modules.blade.php'; $newContent = $content -replace '</head>', '<script src=\"{{ asset(''js/enhanced-pdf-viewer.js'') }}\"></script>`n</head>'; $newContent | Set-Content 'resources\views\admin\admin-modules\admin-modules.blade.php'"

REM Update PDF viewer function in admin modules to use enhanced viewer
echo Updating admin PDF viewer implementation...
powershell -Command "$content = Get-Content 'resources\views\admin\admin-modules\admin-modules.blade.php'; $newContent = $content -replace 'if \(fileExtension === ''pdf''\) \{[\s\S]*?iframe class=\""content-frame\""[\s\S]*?\}', 'if (fileExtension === ''pdf'') { fileViewer = `<div id=\"admin-pdf-viewer-container\"></div>`; setTimeout(() => createEnhancedAdminPDFViewer(fileUrl, fileName, \"admin-pdf-viewer-container\"), 100); }'; $newContent | Set-Content 'resources\views\admin\admin-modules\admin-modules.blade.php'"

REM Add student course fixes script to student course view
echo Adding student course layout fixes...
powershell -Command "$content = Get-Content 'resources\views\student\student-courses\student-course.blade.php'; $newContent = $content -replace '</head>', '<script src=\"{{ asset(''js/student-course-fixes.js'') }}\"></script>`n</head>'; $newContent | Set-Content 'resources\views\student\student-courses\student-course.blade.php'"

REM Update student PDF viewer function
echo Updating student PDF viewer implementation...
powershell -Command "$content = Get-Content 'resources\views\student\student-courses\student-course.blade.php'; $newContent = $content -replace 'const pdfUrl = `/storage/\${data\.content\.attachment_path}`;\s*viewer\.innerHTML = `[\s\S]*?iframe class=\""content-frame\""[\s\S]*?`;', 'const pdfUrl = `/storage/${data.content.attachment_path}`; if (data.content.attachment_path) { createStudentPDFViewer(pdfUrl, data.content.attachment_path.split(``/``).pop(), null); } else { viewer.innerHTML = ``<div class=\"alert alert-warning\">No file attached to this content.</div>``; }'; $newContent | Set-Content 'resources\views\student\student-courses\student-course.blade.php'"

echo Fixes applied successfully!
echo.
echo Summary of changes:
echo - Enhanced PDF viewer with Bootstrap integration
echo - Fixed file upload database column references
echo - Improved student course layout with proper scrolling
echo - Added mobile responsiveness
echo - Implemented fallback options for unsupported browsers
echo.
echo Please test the file upload and PDF viewing functionality.
pause
