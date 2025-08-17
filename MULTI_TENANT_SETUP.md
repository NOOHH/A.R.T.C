# Multi-Tenant System Setup - ARTC

## Overview
Your Laravel application now has a complete multi-tenant system where:
- **Main Database**: `smartprep` - Stores tenant information and system-wide data
- **Template Database**: `smartprep_artc` - Your original client database, used as template for new tenants
- **Tenant Databases**: Each new tenant gets a copy of `smartprep_artc` structure

## Current Configuration

### Environment (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartprep
DB_USERNAME=root
DB_PASSWORD=
```

### Database Connections
- **mysql**: Main database (`smartprep`) for tenant management
- **tenant**: Dynamic connection that switches to tenant-specific databases

## Current Tenants

| ID | Name | Domain | Database |
|----|------|--------|----------|
| 1 | ARTC - Advanced Real-Time Computing | artc.smartprep.local | smartprep_artc |
| 2 | Demo College | demo.smartprep.local | smartprep_demo-smartprep-local |

## How It Works

### 1. Tenant Creation
```bash
php artisan tenant:create "Tenant Name" "domain.example.com"
```
This command:
- Creates a tenant record in the main database
- Creates a new database for the tenant
- Copies the complete structure from `smartprep_artc`
- Copies template data (courses, quizzes, etc.) but excludes user-specific data

### 2. Domain-Based Tenant Resolution
The `TenantMiddleware` automatically:
- Detects the domain from the request
- Finds the matching tenant
- Switches to the tenant's database
- For localhost/development, defaults to ARTC tenant

### 3. Database Switching
The `TenantService` handles:
- Creating new tenant databases
- Copying template structure and data
- Switching database connections dynamically
- Managing tenant lifecycle

## Available Commands

```bash
# List all tenants
php artisan tenant:list

# Create a new tenant
php artisan tenant:create "Client Name" "client.domain.com"
```

## File Structure

### Core Files
- `app/Models/Tenant.php` - Tenant model
- `app/Services/TenantService.php` - Main tenant management service
- `app/Http/Middleware/TenantMiddleware.php` - Domain-based tenant switching
- `app/Console/Commands/CreateTenant.php` - Tenant creation command
- `app/Console/Commands/ListTenants.php` - List tenants command

### Database
- `database/migrations/2025_08_17_082339_create_tenants_table.php` - Tenants table migration
- Main database: `smartprep` (tenant management)
- Template database: `smartprep_artc` (client template)

## How to Add New Tenants

1. **Create tenant**: `php artisan tenant:create "New Client" "newclient.com"`
2. **Configure DNS/hosts**: Point the domain to your server
3. **Access**: Visit `http://newclient.com` and the system will automatically use the tenant's database

## Development Setup

For development, add to your hosts file:
```
127.0.0.1 artc.smartprep.local
127.0.0.1 demo.smartprep.local
```

Then access:
- `http://artc.smartprep.local:8000` (ARTC tenant)
- `http://demo.smartprep.local:8000` (Demo tenant)
- `http://localhost:8000` (defaults to ARTC)

## What Gets Copied to New Tenants

### ✅ Copied (Template Data)
- Course structures
- Quiz templates
- System configurations
- Application settings
- Static content

### ❌ Not Copied (User-Specific Data)
- Users
- User enrollments
- Quiz attempts
- User progress
- Sessions
- Personal access tokens

This ensures each new tenant starts with a clean slate but has all the course content and system structure ready to use.

## Next Steps

1. **Configure your web server** to handle multiple domains
2. **Set up SSL certificates** for your tenant domains
3. **Customize the tenant creation process** if needed
4. **Add tenant-specific branding/theming** support
5. **Implement tenant-specific admin panels**

Your multi-tenant system is now fully functional and ready for production use!
