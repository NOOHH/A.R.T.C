@echo off
echo Fixing file upload and PDF viewer issues...

REM Create backup
copy "resources\views\admin\admin-modules\admin-modules.blade.php" "resources\views\admin\admin-modules\admin-modules.blade.php.backup"

REM Fix file_path references to attachment_path
powershell -Command "(Get-Content 'resources\views\admin\admin-modules\admin-modules.blade.php') -replace 'data\.content\.file_path', 'data.content.attachment_path' | Set-Content 'resources\views\admin\admin-modules\admin-modules.blade.php'"

echo Fixed file_path references to attachment_path
echo Done!
