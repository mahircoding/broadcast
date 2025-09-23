#!/bin/bash

# Laravel cPanel Deployment Script
# Run this script after uploading files to cPanel

echo "🚀 Starting Laravel deployment for cPanel..."

# 1. Check if .env exists
if [ ! -f .env ]; then
    echo "❌ .env file not found! Please copy .env.cpanel to .env and configure it."
    exit 1
fi

# 2. Generate application key if not set
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "⚠️  Generating new application key..."
    php artisan key:generate --no-interaction
fi

# 3. Clear and cache configurations
echo "🧹 Clearing old cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# 5. Seed database if needed
echo "🌱 Seeding database..."
php artisan db:seed --force

# 6. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set proper permissions (if needed)
echo "🔐 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 8. Create storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link

echo "✅ Deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "   1. Verify .env configuration (APP_URL, database, etc.)"
echo "   2. Check file permissions (storage, bootstrap/cache)"
echo "   3. Test login and registration functionality"
echo "   4. Check error logs if issues persist"
echo ""
echo "🌐 Your application should now be ready!"
