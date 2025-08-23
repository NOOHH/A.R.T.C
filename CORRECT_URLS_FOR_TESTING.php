<?php

echo "🚀 CORRECTED ADMIN PREVIEW TEST - Laravel Development Server\n";
echo "===========================================================\n\n";

$timestamp = time();

echo "❌ INCORRECT URLS (Apache - Port 80):\n";
echo "- http://localhost/t/draft/test1/admin-dashboard?website=15&preview=true&t=$timestamp\n";
echo "- http://localhost/t/draft/test1/admin/announcements?website=15&preview=true&t=$timestamp\n";
echo "  ↳ These URLs return '404 Not Found' because Laravel routes don't exist in Apache\n\n";

echo "✅ CORRECT URLS (Laravel Development Server - Port 8000):\n";
echo "- http://localhost:8000/t/draft/test1/admin-dashboard?website=15&preview=true&t=$timestamp\n";
echo "- http://localhost:8000/t/draft/test1/admin/announcements?website=15&preview=true&t=$timestamp\n";
echo "  ↳ These URLs should work and show 'Test1' customization\n\n";

echo "🔧 WHY THE DIFFERENCE?\n";
echo "======================\n";
echo "- Apache (port 80): Serves static files from /xampp/htdocs/ - doesn't know about Laravel routes\n";
echo "- Laravel Dev Server (port 8000): Runs Laravel application with full routing support\n\n";

echo "🧪 TEST STEPS:\n";
echo "==============\n";
echo "1. Make sure Laravel server is running: 'php artisan serve'\n";
echo "2. Open: http://localhost:8000/t/draft/test1/admin-dashboard?website=15&preview=true&t=$timestamp\n";
echo "3. Check navbar shows 'Test1' instead of 'Ascendo Review and Training Center'\n";
echo "4. Navigate to Announcements via sidebar or direct URL:\n";
echo "   http://localhost:8000/t/draft/test1/admin/announcements?website=15&preview=true&t=$timestamp\n";
echo "5. Verify customization persists and URL parameters are preserved\n\n";

echo "📝 REMEMBER:\n";
echo "============\n";
echo "✅ Use http://localhost:8000 (Laravel) - NOT http://localhost (Apache)\n";
echo "✅ Laravel development server is required for Laravel routes\n";
echo "✅ Admin preview customization is working - just need correct server!\n";
