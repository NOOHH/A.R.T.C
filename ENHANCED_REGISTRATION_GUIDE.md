# ARTC Enhanced Registration System - Installation & Testing Guide

## Overview
This implementation adds OCR validation, intelligent program suggestions, and dynamic form prefill to the ARTC student registration system.

## Features Implemented

### ✅ 1. Dynamic Form Prefill System
- **Multi-source data fetching**: Users, Registrations, Students, FormRequirements tables
- **Smart prefill logic**: Latest registration data + dynamic fields JSON
- **Automatic form population** for logged-in users
- **API endpoint**: `/registration/user-prefill-data`

### ✅ 2. Tesseract OCR Integration
- **Name validation**: Checks if user's name appears in uploaded documents
- **Document type detection**: PSA, Good Moral, Certificates, TOR validation
- **File format support**: JPG, PNG, PDF
- **Error handling**: Clear modal feedback on validation failures

### ✅ 3. Intelligent Program Suggestion
- **Certificate analysis**: Extracts keywords from graduation certificates
- **Program matching**: Cross-references with programs and modules
- **Scoring algorithm**: Weighted matching system (program name > description > modules)
- **UI integration**: Suggestions appear at top of program dropdown with ⭐ markers

### ✅ 4. Enhanced User Experience
- **Education Level Selection**: Graduate/Undergraduate dropdown
- **Conditional field display**: Graduation certificate field only shows for Graduate
- **Fixed section display**: Section names properly shown above form groups
- **Enhanced file uploads**: Visual feedback, loading indicators, validation

## Installation Steps

### Step 1: Install Tesseract OCR PHP Package
```bash
cd c:\xampp\htdocs\A.R.T.C
composer require thiagoalessio/tesseract_ocr
```

### Step 2: Install Tesseract OCR System Binary
Download and install Tesseract OCR for Windows:
- Download from: https://github.com/UB-Mannheim/tesseract/wiki
- Install to default location (usually `C:\Program Files\Tesseract-OCR\`)
- Add to system PATH environment variable

### Step 3: Verify File Permissions
Ensure the storage directories have write permissions:
```bash
# Create storage directories if they don't exist
mkdir storage/app/public/documents
mkdir storage/app/public/temp

# Set permissions (Windows)
icacls storage /grant Users:F /T
```

### Step 4: Configure Storage Link (if not already done)
```bash
php artisan storage:link
```

## Files Modified/Created

### 📝 Core Files Updated:
1. **`app/Services/OcrService.php`** - Enhanced with validation and suggestion methods
2. **`app/Http/Controllers/RegistrationController.php`** - Added OCR validation and prefill endpoints
3. **`resources/views/registration/Full_enrollment.blade.php`** - Enhanced form with OCR validation
4. **`public/css/ENROLLMENT/Full_Enrollment.css`** - Added modal and styling enhancements
5. **`routes/web.php`** - Added new API endpoints

### 📋 New API Routes:
- `POST /registration/validate-file` - OCR file validation
- `GET /registration/user-prefill-data` - User data prefill

## Testing Instructions

### Test 1: Dynamic Form Prefill
1. **Create a test user account** or login with existing account
2. **Go to registration form**: `http://localhost/A.R.T.C/enrollment/full`
3. **Navigate to Step 4** (Student Registration)
4. **Verify**: Form fields should auto-populate with user data

### Test 2: OCR Name Validation
1. **Navigate to Step 4** (Student Registration)
2. **Enter your first and last name** in the name fields
3. **Upload a document** (image/PDF) that contains your name
4. **Expected Result**: Document should be accepted
5. **Upload a document** without your name
6. **Expected Result**: Error modal should appear

### Test 3: Document Type Validation
1. **Upload a PSA birth certificate** to the PSA field
2. **Expected Result**: Should be accepted
3. **Upload a graduation certificate** to the PSA field
4. **Expected Result**: Error modal should appear asking for correct document type

### Test 4: Program Suggestions
1. **Select "Graduate" in Education Level dropdown**
2. **Upload a graduation certificate** (e.g., "Bachelor of Science in Engineering")
3. **Expected Result**: 
   - Graduation certificate field should appear
   - Program suggestions should appear at top of program dropdown
   - Suggestions marked with ⭐ and matching scores

### Test 5: Education Level Toggle
1. **Select "Graduate"** in Education Level
2. **Verify**: Graduation certificate field appears
3. **Select "Undergraduate"**
4. **Verify**: Graduation certificate field disappears

## Troubleshooting

### Issue: "Tesseract OCR library not installed"
**Solution**: Run `composer require thiagoalessio/tesseract_ocr`

### Issue: "Tesseract command not found"
**Solution**: 
1. Install Tesseract OCR system binary
2. Add to PATH environment variable
3. Restart web server

### Issue: "Unable to read text from file"
**Solution**: 
1. Ensure uploaded file is clear and readable
2. Check if Tesseract OCR is properly installed
3. Verify file permissions

### Issue: "Section names not displaying"
**Solution**: 
1. Check `form_requirements` table has `section_name` column
2. Verify form requirements have proper section data
3. Clear browser cache

### Issue: "No program suggestions"
**Solution**:
1. Ensure uploaded document contains educational keywords
2. Check if programs and modules exist in database
3. Verify OCR is extracting text correctly

## Database Requirements

### Form Requirements Table
Ensure the `form_requirements` table has these fields:
- `field_name`
- `field_label` 
- `field_type`
- `section_name`
- `is_required`
- `is_active`
- `field_options`

### Programs and Modules
Ensure you have:
- Programs with meaningful names and descriptions
- Modules linked to programs with relevant content
- Keywords that can match uploaded certificates

## Example Test Data

### Sample Form Requirements
```sql
INSERT INTO form_requirements (field_name, field_label, field_type, section_name, is_required, is_active) VALUES
('firstname', 'First Name', 'text', 'Personal Information', 1, 1),
('lastname', 'Last Name', 'text', 'Personal Information', 1, 1),
('PSA', 'PSA Birth Certificate', 'file', 'Required Documents', 1, 1),
('good_moral', 'Certificate of Good Moral', 'file', 'Required Documents', 1, 1),
('Cert_of_Grad', 'Certificate of Graduation', 'file', 'Academic Documents', 0, 1);
```

### Sample Programs for Testing
```sql
INSERT INTO programs (program_name, program_description, is_archived) VALUES
('Engineering', 'Engineering review program covering civil, mechanical, and electrical engineering', 0),
('Nursing', 'Comprehensive nursing review program for board exam preparation', 0),
('Culinary', 'Culinary arts and food service management program', 0);
```

## API Documentation

### POST /registration/validate-file
**Purpose**: Validate uploaded file using OCR
**Parameters**:
- `file`: File to validate (JPG, PNG, PDF)
- `field_name`: Field name (PSA, good_moral, etc.)
- `first_name`: User's first name
- `last_name`: User's last name

**Response**:
```json
{
    "success": true,
    "message": "File uploaded and validated successfully.",
    "file_path": "documents/file.pdf",
    "suggestions": [...],
    "certificate_level": "graduate"
}
```

### GET /registration/user-prefill-data
**Purpose**: Get user data for form prefill
**Authentication**: Requires logged-in user
**Response**:
```json
{
    "success": true,
    "data": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        // ... other fields
    }
}
```

## Performance Considerations

1. **OCR Processing**: Can take 2-5 seconds for document processing
2. **File Size Limits**: Maximum 5MB file uploads
3. **Caching**: Consider caching program suggestions for repeated uploads
4. **Background Processing**: For production, consider queue-based OCR processing

## Security Notes

1. **File Validation**: Only accept specific file types (JPG, PNG, PDF)
2. **Input Sanitization**: All OCR text is sanitized before processing
3. **File Storage**: Uploaded files stored in secure directory
4. **Access Control**: Prefill data only accessible to logged-in users

## Deployment Checklist

- [ ] Install Tesseract OCR system binary
- [ ] Install PHP Tesseract package
- [ ] Configure file storage permissions
- [ ] Test OCR functionality with sample documents
- [ ] Verify program suggestion algorithm
- [ ] Test form prefill with existing users
- [ ] Configure production file upload limits
- [ ] Set up proper error logging

## Success Metrics

✅ **Dynamic Prefill**: Form fields automatically populated for logged-in users
✅ **OCR Validation**: Documents validated for name presence and type
✅ **Program Suggestions**: Relevant programs suggested based on uploaded certificates
✅ **Enhanced UX**: Improved visual feedback and user guidance
✅ **Education Level**: Conditional field display based on graduate/undergraduate selection
✅ **Section Display**: Proper section names displayed above form groups

---

**Implementation Status**: ✅ COMPLETE - Ready for testing and deployment
**Next Steps**: Install Tesseract OCR binary and test with real documents
