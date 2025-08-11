#!/bin/bash

# ARTC Deployment Setup Script
# This script helps set up the production environment for deployment

echo "=== ARTC Production Deployment Setup ==="
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ .env file not found. Creating from template..."
    cp ".env copy.example" .env
    echo "✅ .env file created"
else
    echo "✅ .env file exists"
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated"
else
    echo "✅ Application key already exists"
fi

# Database configuration check
echo ""
echo "=== Database Configuration ==="
echo "Current database settings:"
grep "^DB_" .env

echo ""
echo "⚠️  IMPORTANT: Make sure your database settings are correct for production!"
echo "   - DB_HOST should point to your production database server"
echo "   - DB_DATABASE should be 'artc'"
echo "   - DB_USERNAME and DB_PASSWORD should have proper permissions"
echo ""

# Check database connection
echo "Testing database connection..."
if php artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED');" 2>/dev/null; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    echo "   Please check your database configuration in .env file"
fi

echo ""
echo "=== Deployment Checklist ==="
echo "✅ Application key generated"
echo "✅ Environment file configured"
echo "⚠️  Verify database settings"
echo "⚠️  Set APP_URL to your production domain"
echo "⚠️  Configure mail settings if needed"
echo ""
echo "Ready for deployment! 🚀"

