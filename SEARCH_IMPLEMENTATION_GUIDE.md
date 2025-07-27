# A.R.T.C Search System Implementation Guide

## Quick Start

### 1. Include the Search Component in Your Layout

Add this to your main layout files (admin, professor, student layouts):

```blade
<!-- Include the universal search component -->
@include('components.universal-search')
```

### 2. Required Dependencies

Ensure these are included in your layout:

```html
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
```

### 3. Routes Already Set Up

The following routes are already configured in your `routes/web.php`:

```php
// Main search endpoints
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/advanced', [SearchController::class, 'advancedSearch'])->name('search.advanced');
Route::get('/search/profile', [SearchController::class, 'getProfile'])->name('search.profile');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// API endpoints
Route::prefix('api/search')->group(function () {
    Route::get('/', [SearchController::class, 'universalSearch'])->name('api.search');
    Route::get('/profile', [SearchController::class, 'getProfile'])->name('api.search.profile');
    Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
});
```

## Integration Examples

### In Admin Dashboard Layout

```blade
<!-- resources/views/admin/admin-dashboard-layout.blade.php -->
<nav class="navbar">
    <div class="container-fluid">
        <!-- Your existing navbar content -->
        
        <!-- Add the search component -->
        <div class="navbar-nav ms-auto">
            @include('components.universal-search')
        </div>
    </div>
</nav>
```

### In Professor Layout

```blade
<!-- resources/views/professor/layout.blade.php -->
<header class="main-header">
    <nav class="navbar">
        <!-- Your existing content -->
        
        <!-- Replace existing search input with the universal search -->
        <div class="search-section">
            @include('components.universal-search')
        </div>
    </nav>
</header>
```

### In Student Layout

```blade
<!-- resources/views/components/student-navbar.blade.php -->
<nav class="navbar">
    <div class="navbar-brand">A.R.T.C</div>
    
    <!-- Universal search -->
    <div class="navbar-search">
        @include('components.universal-search')
    </div>
    
    <!-- Your existing navbar content -->
</nav>
```

## Customization

### Custom Styling

Add this CSS to customize the search appearance:

```css
/* Custom search styling */
.search-container {
    min-width: 300px;
    max-width: 500px;
}

/* Adjust dropdown positioning if needed */
.search-dropdown {
    margin-top: 5px;
}

/* Custom result item styling */
.search-result-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
```

### Role-Based Placeholder Text

The component automatically adjusts placeholder text based on user role:
- **Admin/Director**: "Search students, professors, and programs..."
- **Professor**: "Search students and programs..."  
- **Student**: "Search professors and admins..."

### Custom Search Types

To add custom search types, modify the dropdown in `universal-search.blade.php`:

```blade
<ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#" onclick="setSearchType('all')">All</a></li>
    <li><a class="dropdown-item" href="#" onclick="setSearchType('students')">Students Only</a></li>
    <li><a class="dropdown-item" href="#" onclick="setSearchType('professors')">Professors Only</a></li>
    <li><a class="dropdown-item" href="#" onclick="setSearchType('programs')">Programs Only</a></li>
    <!-- Add custom types here -->
    <li><a class="dropdown-item" href="#" onclick="setSearchType('courses')">Courses Only</a></li>
</ul>
```

## Testing the Implementation

### 1. Test Search Functionality

Visit: `http://localhost:8000/search-test.html`

This test page allows you to:
- Test the search interface
- Verify search types work correctly
- Check result display formatting
- Test modal interactions

### 2. Test API Endpoints

```bash
# Test basic search
curl "http://localhost:8000/search?query=john&type=all&limit=5"

# Test advanced search
curl "http://localhost:8000/search/advanced?query=john&role=student&status=active"

# Test profile retrieval
curl "http://localhost:8000/search/profile?user_id=1&type=user"
```

### 3. Browser Testing

1. Open your main application
2. Look for the search box in the navbar
3. Type at least 2 characters
4. Verify dropdown appears with results
5. Test different search types
6. Click on results to test modals

## Common Integration Issues

### Issue 1: Search Box Not Appearing
**Solution:** Ensure the component is included in your layout and Bootstrap CSS is loaded.

### Issue 2: No Search Results
**Possible Causes:**
- Database connection issues
- No matching data in database
- Permission restrictions
- Missing model relationships

**Debug Steps:**
1. Check Laravel logs for errors
2. Verify database has test data
3. Test API endpoint directly
4. Check user authentication

### Issue 3: JavaScript Errors
**Possible Causes:**
- Bootstrap JS not loaded
- Conflicting JavaScript libraries
- Missing DOM elements

**Solution:**
1. Check browser console for errors
2. Ensure Bootstrap JS is loaded after jQuery (if using)
3. Verify component HTML structure

### Issue 4: Permission Denied
**Possible Causes:**
- User not authenticated
- Middleware blocking requests
- Role-based access restrictions

**Solution:**
1. Verify user is logged in
2. Check middleware configuration
3. Review role permissions in SearchController

## Performance Optimization

### Database Indexing

Add these indexes for optimal search performance:

```sql
-- User search indexes
CREATE INDEX idx_users_firstname ON users(user_firstname);
CREATE INDEX idx_users_lastname ON users(user_lastname);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Program search indexes
CREATE INDEX idx_programs_name ON programs(program_name);
CREATE INDEX idx_programs_description ON programs(program_description);

-- Enrollment indexes for relationship queries
CREATE INDEX idx_enrollments_student_id ON enrollments(student_id);
CREATE INDEX idx_enrollments_program_id ON enrollments(program_id);
```

### Caching (Optional)

For high-traffic applications, consider adding Redis caching:

```php
// In SearchController
public function search(Request $request)
{
    $cacheKey = 'search:' . md5($request->getQueryString());
    
    return Cache::remember($cacheKey, 300, function () use ($request) {
        // Your existing search logic
    });
}
```

## Support and Maintenance

### Logging Search Activities

Add logging to track search usage:

```php
// In SearchController
Log::info('Search performed', [
    'user_id' => Auth::id(),
    'query' => $query,
    'type' => $type,
    'results_count' => count($results)
]);
```

### Monitoring Performance

Track search performance with metrics:

```php
// Track search response time
$start = microtime(true);
// ... search logic ...
$duration = microtime(true) - $start;

Log::info('Search performance', [
    'duration' => $duration,
    'query' => $query,
    'results' => count($results)
]);
```

---

**Need Help?** 
- Check the main documentation: `SEARCH_SYSTEM_DOCUMENTATION.md`
- Review the test page: `search-test.html`
- Examine the SearchController: `app/Http/Controllers/SearchController.php`
- Inspect the component: `resources/views/components/universal-search.blade.php`
