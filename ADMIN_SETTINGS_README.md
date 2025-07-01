# Admin Settings Feature Documentation

## Overview
The Admin Settings feature allows administrators to customize the appearance of the homepage and enrollment pages through a user-friendly interface. This includes the ability to change colors, text, and background images.

## Features Added

### 1. Admin Settings Page
- **Location**: `/admin/settings`
- **Access**: Click the Settings button in the admin dashboard sidebar
- **Functionality**: 
  - Edit homepage title text
  - Change background and text colors for homepage
  - Change background, text, and accent colors for enrollment pages
  - Upload/remove background images
  - Real-time preview of changes

### 2. Homepage Customization
- **Title Text**: Change the main call-to-action text (default: "ENROLL NOW")
- **Background Color**: Set the background color using a color picker
- **Text Color**: Set the text color for the main title
- **Background Image**: Upload a background image (with color overlay support)

### 3. Enrollment Page Customization
- **Background Color**: Set the overall background color
- **Text Color**: Set the primary text color
- **Accent Color**: Set the color for buttons, links, and interactive elements
- **Background Image**: Upload a background image for enrollment pages

### 4. Pages Affected by Customization
- Homepage (`/`)
- Enrollment Selection (`/enrollment`)
- Full Enrollment Form (`/enrollment/full`)
- Modular Enrollment Form (`/enrollment/modular`)

## Technical Implementation

### Files Created/Modified

#### New Files:
1. **Controller**: `app/Http/Controllers/AdminSettingsController.php`
   - Handles settings CRUD operations
   - Manages file uploads for background images
   - Stores settings in JSON format

2. **Helper Class**: `app/Helpers/SettingsHelper.php`
   - Provides CSS generation for customizations
   - Handles settings retrieval and defaults
   - Generates dynamic styles for pages

3. **View**: `resources/views/admin/admin-settings/admin-settings.blade.php`
   - Complete settings interface
   - Color pickers with real-time preview
   - File upload handling
   - Form validation

#### Modified Files:
1. **Routes**: `routes/web.php` - Added admin settings routes
2. **Admin Layout**: `resources/views/admin/admin-dashboard-layout.blade.php` - Made settings button functional
3. **Homepage**: `resources/views/homepage.blade.php` - Added dynamic styling support
4. **Enrollment Views**: All enrollment-related views - Added dynamic styling support

### Routes Added
```php
GET  /admin/settings                    - Settings page
POST /admin/settings/homepage           - Update homepage settings
POST /admin/settings/enrollment         - Update enrollment settings
POST /admin/settings/remove-image       - Remove background images
```

### Settings Storage
- Settings are stored in `storage/app/settings.json`
- Background images are stored in `storage/app/public/settings/`
- Symbolic link created for public access via `storage:link`

### Default Settings
```json
{
    "homepage": {
        "background_color": "#667eea",
        "text_color": "#ffffff",
        "title": "ENROLL NOW",
        "background_image": null
    },
    "enrollment": {
        "background_color": "#f8f9fa",
        "text_color": "#333333",
        "accent_color": "#667eea",
        "background_image": null
    }
}
```

## Usage Instructions

### For Administrators:

1. **Access Settings**:
   - Go to the admin dashboard
   - Click the "Settings" button in the sidebar
   - You'll be redirected to the settings page

2. **Customize Homepage**:
   - Change the homepage title text
   - Select background and text colors using color pickers
   - Upload a background image (optional)
   - Preview changes in real-time
   - Click "Update Homepage" to save

3. **Customize Enrollment Pages**:
   - Select background, text, and accent colors
   - Upload a background image (optional)
   - Preview changes in real-time
   - Click "Update Enrollment Page" to save

4. **Remove Images**:
   - If you've uploaded a background image, a "Remove Image" button will appear
   - Click it to remove the current background image

### Color Guidelines:
- **Background Colors**: Should provide good contrast with text
- **Text Colors**: Should be readable against the background
- **Accent Colors**: Used for buttons and interactive elements

### Image Guidelines:
- **Supported Formats**: JPG, PNG, GIF
- **Maximum Size**: 5MB
- **Recommended Dimensions**: 1920x1080 or higher for backgrounds
- **Note**: Images will have a color overlay applied based on the background color setting

## Browser Compatibility
- Modern browsers with CSS custom properties support
- Color picker input support
- File upload support

## Security Features
- File type validation for uploads
- File size limits (5MB max)
- CSRF protection on all forms
- Input validation and sanitization

## Troubleshooting

### Common Issues:

1. **Settings not applying**:
   - Check if `storage/app/settings.json` exists and is readable
   - Ensure the SettingsHelper class is properly autoloaded
   - Clear browser cache

2. **Images not displaying**:
   - Verify `php artisan storage:link` has been run
   - Check file permissions on storage directories
   - Ensure images are within size limits

3. **Color picker not working**:
   - Ensure modern browser with HTML5 color input support
   - Check if JavaScript is enabled

### File Permissions:
- `storage/app/` should be writable by the web server
- `storage/app/public/` should be writable by the web server
- `public/storage/` should be a valid symlink

## Future Enhancements
- Font family selection
- More granular color controls
- Theme presets
- Logo upload functionality
- Advanced layout customization
- Color scheme templates
