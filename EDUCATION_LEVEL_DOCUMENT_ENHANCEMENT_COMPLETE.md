# Education Level Document Requirements Enhancement

## Overview
Enhanced the education level management system to allow admins to configure specific document upload requirements for each education level with granular control over file types and availability.

## Key Features Added

### 1. Document-Specific Requirements
**Before**: Generic "file requirements" with basic name and type
**After**: Specific document types with predefined options:
- School ID
- Diploma
- Certificate of Graduation
- Transcript of Records (TOR)
- PSA Birth Certificate
- Good Moral Certificate
- Course Certificate
- Photo 2x2
- Custom Document (admin-defined)

### 2. File Type Controls
Admins can now specify exactly what file types are accepted for each document:
- **Image Only**: JPG, PNG, GIF (perfect for School ID, Photo 2x2)
- **PDF Only**: PDF files (ideal for official documents)
- **Document**: PDF, DOC, DOCX (for various document formats)
- **Any File Type**: All supported formats

### 3. Plan-Specific Availability
Each document requirement can be configured for:
- **Full Plan**: Enable/disable for full enrollment plan
- **Modular Plan**: Enable/disable for modular enrollment plan
- **Required**: Mark as mandatory or optional

### 4. Enhanced User Interface
- **Visual Document Types**: Clear dropdown with predefined document options
- **File Type Icons**: Visual indicators for different file types (image, PDF, document)
- **Status Badges**: Color-coded badges showing requirement status
- **Custom Names**: Support for custom document names when needed

## Technical Implementation

### Modal Structure
```html
<!-- Document Requirements Section -->
<div class="mb-4">
    <h6>Document Requirements</h6>
    <div id="fileRequirementsContainer">
        <!-- Dynamic document requirement items -->
    </div>
    <button onclick="addDocumentRequirement()">Add Document Requirement</button>
</div>
```

### JavaScript Functions
```javascript
// Enhanced document requirement management
function addDocumentRequirement(data = {})
function handleDocumentTypeChange(selectElement)
function removeDocumentRequirement(button)

// Legacy compatibility
function addFileRequirement(data = {})  // Redirects to addDocumentRequirement
function removeFileRequirement(button)   // Redirects to removeDocumentRequirement
```

### Data Structure
```json
{
  "field_name": "school_id",
  "document_type": "school_id",
  "file_type": "image",
  "custom_name": null,
  "is_required": true,
  "available_full_plan": true,
  "available_modular_plan": true
}
```

## Use Cases

### Example 1: Undergraduate Level
- **School ID**: Image only, required for both plans
- **Photo 2x2**: Image only, required for both plans
- **PSA Birth Certificate**: PDF only, required for full plan only

### Example 2: Graduate Level  
- **Diploma**: PDF or document, required for both plans
- **TOR**: PDF only, required for both plans
- **School ID**: Image only, required for both plans

### Example 3: Professional Level
- **Certificate of Graduation**: PDF only, required for both plans
- **Professional License**: PDF or document, optional for modular plan
- **School ID**: Image only, required for both plans

## Benefits

### For Administrators
1. **Granular Control**: Specify exactly what documents and file types are needed
2. **Plan Flexibility**: Different requirements for full vs modular plans
3. **Easy Management**: Visual interface with clear document type selection
4. **Custom Options**: Ability to add custom document types when needed

### For Students
1. **Clear Requirements**: Know exactly what documents are needed
2. **File Type Guidance**: Understand what file formats are accepted
3. **OCR Integration**: Automatic document validation and program suggestions
4. **Visual Feedback**: Clear upload status and validation results

### For System
1. **Better Validation**: OCR service can validate specific document types
2. **Improved Organization**: Structured document requirements
3. **Enhanced UX**: Better file upload experience with type restrictions
4. **Data Consistency**: Standardized document requirement structure

## Integration with OCR Service

The enhanced document requirements work seamlessly with the OCR service:

1. **Document Type Validation**: OCR validates documents against expected types
2. **Program Suggestions**: Analyzes document content for relevant programs
3. **File Type Optimization**: Different OCR strategies for images vs PDFs
4. **Diploma Handling**: Unified handling of diploma and certificate documents

## Backward Compatibility

The system maintains full backward compatibility:
- Existing education levels continue to work
- Legacy file requirements are automatically converted
- Old API responses are supported
- Gradual migration to new format

## Files Modified

1. **admin-settings.blade.php**:
   - Enhanced modal with document type selection
   - Updated JavaScript functions
   - Improved visual display of requirements

2. **OcrService.php**:
   - Enhanced document type validation
   - Unified diploma/certificate handling
   - Better file type support

## Testing Checklist

- [ ] Create new education level with mixed document requirements
- [ ] Edit existing education level and modify document requirements
- [ ] Test custom document type functionality
- [ ] Verify file type restrictions work in enrollment forms
- [ ] Confirm OCR integration with new document types
- [ ] Test plan-specific availability (full vs modular)
- [ ] Verify backward compatibility with existing education levels

## Future Enhancements

1. **File Size Limits**: Per-document file size restrictions
2. **Multiple Files**: Allow multiple files per document type
3. **Document Templates**: Predefined templates for common education levels
4. **Bulk Operations**: Mass update document requirements across levels
5. **Validation Rules**: Custom validation rules per document type

---

**Status**: ✅ Complete - Enhanced education level document management implemented
**Date**: July 15, 2025
**Version**: Enhanced Document Requirements v2.0
