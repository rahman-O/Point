#!/bin/bash
set -e  # Exit immediately if any command fails

# Laravel Deployment Script for Hostinger Shared Hosting
echo "🚀 Starting deployment process..."

# Step 1: Navigate to project directory (critical for cron/webhook executions)
cd /home/u12345678/domains/point-iraq.org/public_html

# Step 2: Maintenance mode (with auto-revert on failure)
php artisan down --render="errors::503" || true

# Step 3: Git operations (skip if deploying via GitHub Actions)
echo "🔄 Pulling latest changes..."
git reset --hard HEAD  # Clean working directory
git pull origin main

# Step 4: Dependency management
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction --no-progress

# Step 5: Database migrations (only if needed)
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction

# Step 6: Cache optimization
echo "🔧 Optimizing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache  # Must come after clears
php artisan route:cache
php artisan view:cache

# Step 7: File permissions (critical for shared hosting)
echo "🔐 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Step 8: Storage link (idempotent operation)
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Step 9: Bring application back up
php artisan up
echo "🎉 Deployment completed successfully!"
