# Dynamic Registration System Documentation

## Overview
The Dynamic Registration System allows administrators to modify registration form fields without breaking existing data. It uses a static columns approach where each possible field has a corresponding column in the `registrations` table.

## Key Features

### ✅ No Data Loss
- When a field is disabled, it's marked as `is_active = false` in the database
- All historical data remains intact in its respective column
- Re-enabling the field shows it in forms again with preserved data

### ✅ Static Columns Approach
- Each field has a dedicated column in the `registrations` table
- No complex JSON parsing or migration conflicts
- Direct database queries and relationships work seamlessly

### ✅ Dynamic Form Generation
- Forms are generated based on `form_requirements` table
- Real-time validation based on active and required fields
- Support for multiple field types: text, email, tel, date, file, select, textarea, checkbox, radio, number

## Database Structure

### `form_requirements` Table
```sql
- id (primary key)
- field_name (varchar) - corresponds to column name in registrations table
- field_label (varchar) - display label for the field
- field_type (enum) - text, email, tel, date, file, select, textarea, checkbox, radio, number, section
- program_type (enum) - complete, modular, both
- is_required (boolean) - whether field is required during validation
- is_active (boolean) - whether field appears in forms
- is_bold (boolean) - whether field label should be bold
- field_options (json) - options for select/radio fields
- validation_rules (text) - additional Laravel validation rules
- sort_order (integer) - display order in forms
- section_name (varchar) - grouping section name
- created_at, updated_at
```

### `registrations` Table
```sql
- id (primary key)
- firstname, lastname, middlename
- user_id, student_school, street_address, etc.
- Dynamic fields (added as static columns):
  - phone_number, telephone_number, religion
  - citizenship, civil_status, birthdate, gender
  - work_experience, preferred_schedule
  - emergency_contact_relationship, health_conditions
  - disability_support, valid_id, birth_certificate
  - diploma_certificate, medical_certificate, etc.
- created_at, updated_at
```

## Usage Guide

### For Administrators

#### Adding a New Field
1. Go to Admin Settings > Student > Register tab
2. Click "Add Field/Section"
3. Fill in field details:
   - **Section Name**: Group fields logically
   - **Field Name**: Database column name (e.g., `middle_name`)
   - **Display Label**: What users see (e.g., "Middle Name")
   - **Field Type**: Select appropriate input type
   - **Program**: Choose which programs show this field
   - **Required**: Toggle if field is mandatory
   - **Active**: Toggle if field appears in forms

#### Managing Existing Fields
- **Disable Field**: Uncheck "Active" - hides from forms but preserves data
- **Enable Field**: Check "Active" - shows in forms again
- **Bold Labels**: Click bold button to emphasize important fields
- **Reorder**: Drag and drop using the grip handle

#### Adding Database Columns
If you need a field that doesn't have a database column:
1. Click "Add DB Column" in Advanced Options
2. Enter field name and type
3. Run the generated migration: `php artisan migrate`
4. Add the field name to the Registration model's `$fillable` array

### For Developers

#### Adding New Field Types
1. Update the `field_type` enum in the migration
2. Add validation logic in `StudentRegistrationController`
3. Add rendering logic in `dynamic-enrollment-form.blade.php`

#### Artisan Commands
```bash
# List all form fields
php artisan registration:manage-fields list

# Add a new field
php artisan registration:manage-fields add field_name

# Disable a field
php artisan registration:manage-fields remove field_name
```

## Implementation Details

### ✅ Dynamic Education Level Field
The education level field has been successfully converted from a hardcoded implementation to a fully dynamic field:

- **Field Configuration**: Stored in `form_requirements` table with `field_name: education_level`
- **Field Type**: Select dropdown with button group styling for better UX
- **Options**: Configurable through admin settings (default: Undergraduate, Graduate)
- **Validation**: Dynamic validation based on field configuration
- **Storage**: Saved to `education_level` column in `registrations` table
- **Admin Management**: Fully manageable through admin settings interface

### Form Rendering
Forms are dynamically generated using the `DynamicEnrollmentForm` component:
```php
<x-dynamic-enrollment-form program-type="complete" />
```

### Validation
Validation rules are built dynamically based on active fields:
```php
$formRequirements = FormRequirement::active()
    ->forProgram($programType)
    ->get();

foreach ($formRequirements as $requirement) {
    $rules[$requirement->field_name] = $this->buildValidationRules($requirement);
}
```

### Data Storage
Data is saved directly to corresponding columns:
```php
foreach ($formRequirements as $requirement) {
    if ($requirement->is_active && $request->has($requirement->field_name)) {
        $registration->{$requirement->field_name} = $request->input($requirement->field_name);
    }
}
```

## Migration Strategy

### From JSON to Static Columns
If migrating from a JSON-based system:
1. Create migrations for each field in the JSON structure
2. Copy data from JSON to respective columns
3. Update form requirements to mark fields as active
4. Test thoroughly before removing JSON columns

### Adding New Fields
1. Create a migration to add the column
2. Add the field to the Registration model's `$fillable` array
3. Create a form requirement entry
4. Test in preview mode before making live

## Education Level Field Migration

### What Was Changed
1. **Removed Hardcoded Implementation**: 
   - Removed static HTML buttons for Undergraduate/Graduate
   - Removed hardcoded JavaScript function `selectEducation()`
   - Removed static validation logic in controllers

2. **Added Dynamic Implementation**:
   - Added `education_level` field to `form_requirements` table
   - Configured as select field with button group styling
   - Added to `FormRequirementsSeeder.php` for automatic setup
   - Integrated with dynamic form component

3. **Admin Management**:
   - Field can be enabled/disabled via admin settings
   - Options can be modified (add more than just Undergraduate/Graduate)
   - Sort order can be changed
   - Field can be made required/optional

### How It Works
- The `education_level` field is rendered using the `dynamic-enrollment-form` component
- When `field_type` is 'select' and `field_name` is 'education_level', it renders as button group
- Form submission validates the value against the defined options
- Data is stored in the static `education_level` column in the `registrations` table

### Admin Usage
1. Go to Admin Settings > Student > Register tab
2. Find "Education Level" field in the form requirements list
3. Modify options by editing the field options textarea
4. Change sort order by dragging the field up/down
5. Toggle active/required status as needed
6. Save changes to update the live registration form

## Best Practices

### Field Naming
- Use snake_case for field names
- Be descriptive but concise (e.g., `emergency_contact_name`)
- Avoid conflicts with existing Laravel model attributes

### Field Management
- Always test new fields in preview mode first
- Use meaningful section names to group related fields
- Set appropriate validation rules for data integrity
- Document any custom validation rules

### Data Preservation
- Never manually delete columns from the registrations table
- Use the `is_active` flag to control field visibility
- Keep old field definitions for historical reference

## Troubleshooting

### Common Issues
1. **Field not appearing**: Check if `is_active = true` in form_requirements
2. **Validation errors**: Verify field_name matches database column
3. **Missing data**: Ensure field is in Registration model's `$fillable` array

### Error Logs
Check Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

## Security Considerations

- All form inputs are validated against defined rules
- File uploads are restricted by type and size
- CSRF protection is enforced on all form submissions
- SQL injection protection through Eloquent ORM

## Performance Notes

- Form requirements are cached during form rendering
- Database queries are optimized with proper indexing
- File uploads are stored efficiently in organized directories

This system provides a robust, scalable solution for dynamic form management while maintaining data integrity and system performance.
