#!/bin/bash

# Laravel Deployment Script for Hostinger Shared Hosting
echo "🚀 Starting deployment process..."

# Step 1: Put application in maintenance mode
echo "📝 Putting application in maintenance mode..."
php artisan down --render="errors::503" --secret="your-secret-token"

# Step 2: Pull latest changes (if using Git)
echo "🔄 Pulling latest changes..."
git pull origin main

# Step 3: Install/Update dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Step 4: Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Step 5: Clear and cache configuration
echo "🔧 Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 6: Storage link (if needed)
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 7: Bring application back up
echo "✅ Bringing application back online..."
php artisan up

echo "🎉 Deployment completed successfully!"
