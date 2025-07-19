# File Upload Debugging - Enhanced Troubleshooting

## Current Issues
- 422 Validation Error: "The attachment failed to upload"
- Error occurs during Laravel validation phase

## Enhanced Debugging Added

### 1. JavaScript Console Debugging
**Location**: `admin-modules.blade.php` - `submitCourseContentForm()` function

**What it shows**:
- All form data being sent to server
- File details (name, size, type, lastModified)
- Request structure before sending

### 2. Server-Side Logging
**Location**: `AdminModuleController.php` - `courseContentStore()` method

**What it logs**:
- Complete file upload details
- PHP upload limits and settings
- File validation status
- Request data structure
- Detailed error codes and messages

## How to Debug the Upload Issue

### Step 1: Check Browser Console
1. Open admin modules page
2. Open browser console (F12)
3. Try uploading a file
4. Look for console output showing:
   ```
   Submitting course content form...
   Form data entries:
   attachment: {name: "file.pdf", size: 12345, type: "application/pdf", ...}
   ```

### Step 2: Check Laravel Logs
1. Open `storage/logs/laravel.log`
2. Look for entries containing:
   ```
   Course content store request received
   File upload details
   File upload validation failed (if any)
   Applying validation rules
   ```

### Step 3: Common Issues to Check

#### File Size Issues
- **Browser**: Check console for file size
- **Server**: Check if size exceeds PHP limits (40MB currently)
- **Laravel**: Check if size exceeds validation limit (50MB currently)

#### File Type Issues
- **Browser**: Check file MIME type in console
- **Server**: Verify MIME type is supported
- **Validation**: Currently simplified to size-only validation

#### Upload Process Issues
- **File Error Codes**: Check logs for PHP upload error codes
- **Temporary Directory**: Ensure `/tmp` is writable
- **Disk Space**: Ensure storage has space
- **Permissions**: Ensure `storage/app/public` is writable

## Expected Log Output

### Successful Upload:
```
[INFO] Course content store request received: {has_file: true, file_info: {...}}
[INFO] File upload details: {file_error: 0, file_size: 12345, is_valid: true}
[INFO] Applying validation rules: {rules: {...}, has_file_rule: true}
```

### Failed Upload:
```
[ERROR] File upload validation failed: {error_code: 1, error_message: "File too large"}
```

## Testing Steps

1. **Try Small File First**: Upload a small PDF (< 1MB) to isolate size issues
2. **Check Different File Types**: Try different file formats (PDF, DOC, Image)
3. **Test Without File**: Try submitting form without attachment
4. **Check Network Tab**: Look for request size and response in browser dev tools

## Quick Fixes to Try

1. **Increase PHP Limits** (in `php.ini`):
   ```
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   ```

2. **Check Apache/Nginx Limits**:
   - Apache: `LimitRequestBody`
   - Nginx: `client_max_body_size`

3. **Test File Permissions**:
   ```bash
   chmod -R 775 storage/
   chown -R www-data:www-data storage/
   ```

## Next Steps

1. **Run the upload test** and check both browser console and Laravel logs
2. **Compare the logged data** with expected values
3. **Identify the specific failure point** (PHP, Laravel validation, file system)
4. **Apply targeted fix** based on the root cause identified

The enhanced logging will now show exactly where the upload is failing and why.
