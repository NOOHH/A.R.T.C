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
    
    # Run database migrations
    echo "🔄 Running database migrations..."
    php artisan migrate --force
    
    # Load schema if needed (using our custom command)
    if [ -f "database/schema/mysql-schema.sql" ]; then
        echo "📋 Loading database schema..."
        php artisan db:load-schema --file=mysql-schema.sql || {
            echo "⚠️  Schema loading failed, continuing with migrations only"
        }
    fi
    
    # Clear and cache configuration
    echo "🧹 Clearing and caching configuration..."
    php artisan config:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Set proper permissions
    echo "🔐 Setting file permissions..."
    chmod -R 775 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
    
    echo "✅ Deployment completed successfully!"
    
    # Start the application
    echo "🌐 Starting Laravel application..."
    php artisan serve --host=0.0.0.0 --port=8000
    
else
    echo "🖥️  Running in local environment"
    
    # For local development
    echo "🔄 Running database migrations..."
    php artisan migrate
    
    echo "✅ Local deployment completed!"
fi
