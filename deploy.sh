#!/bin/bash

# ARTC Laravel Application Deployment Script
# This script handles the deployment process including database setup

set -e  # Exit on any error

echo "🚀 Starting ARTC application deployment..."

# Check if we're in a containerized environment
if [ -f /.dockerenv ] || [ -f /run/.containerenv ]; then
    echo "📦 Running in containerized environment"
    
    # Wait for database to be ready (if using external database)
    if [ -n "$DB_HOST" ]; then
        echo "⏳ Waiting for database connection..."
        until php artisan db:show --database=mysql; do
            echo "Database is unavailable - sleeping"
            sleep 2
        done
        echo "✅ Database connection established"
    fi
    
    # Run database migrations with error handling
    echo "🔄 Running database migrations..."
    if php artisan migrate --force; then
        echo "✅ Migrations completed successfully"
    else
        echo "⚠️  Migrations failed, but continuing deployment..."
    fi
    
    # Load schema if needed (using our custom command) - only if file exists and is not empty
    if [ -f "database/schema/mysql-schema.sql" ] && [ -s "database/schema/mysql-schema.sql" ]; then
        echo "📋 Loading database schema..."
        if php artisan db:load-schema --file=mysql-schema.sql; then
            echo "✅ Schema loaded successfully"
        else
            echo "⚠️  Schema loading failed, continuing with migrations only"
        fi
    else
        echo "ℹ️  No schema file found or schema file is empty, skipping schema loading"
    fi
    
    # Clear and cache configuration
    echo "🧹 Clearing and caching configuration..."
    php artisan config:clear || echo "⚠️  Config clear failed, continuing..."
    php artisan config:cache || echo "⚠️  Config cache failed, continuing..."
    php artisan route:cache || echo "⚠️  Route cache failed, continuing..."
    php artisan view:cache || echo "⚠️  View cache failed, continuing..."
    
    # Set proper permissions
    echo "🔐 Setting file permissions..."
    chmod -R 775 storage bootstrap/cache || echo "⚠️  Permission setting failed, continuing..."
    chown -R www-data:www-data storage bootstrap/cache || echo "⚠️  Ownership setting failed, continuing..."
    
    echo "✅ Deployment completed successfully!"
    
    # Start the application
    echo "🌐 Starting Laravel application..."
    php artisan serve --host=0.0.0.0 --port=8000
    
else
    echo "🖥️  Running in local environment"
    
    # For local development
    echo "🔄 Running database migrations..."
    php artisan migrate || echo "⚠️  Local migration failed"
    
    echo "✅ Local deployment completed!"
fi
