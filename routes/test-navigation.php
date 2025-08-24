
<?php
// Temporary test route for navigation debugging
Route::get('/test-navigation', function () {
    $selectedWebsite = (object) ['id' => 15, 'name' => 'Test Website', 'slug' => 'test1'];
    $settings = [
        'general' => ['brand_name' => 'Test'],
        'navbar' => ['brand_name' => 'Test']
    ];
    $previewUrl = 'http://127.0.0.1:8000/t/test1';
    
    return view('smartprep.dashboard.customize-website', compact('selectedWebsite', 'settings', 'previewUrl'));
})->name('test.navigation');
