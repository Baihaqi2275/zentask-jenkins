#!/bin/bash

echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "📊 Checking routes..."
php artisan route:list --name=dashboard
php artisan route:list --name=tasks
php artisan route:list --name=api.tasks

echo ""
echo "✅ Cache cleared! Ready to test."
echo "🚀 Run: php artisan serve"
