#!/bin/bash

set -e

echo "🚀 Starting deployment..."

# Pull latest changes
git pull origin main

# Build and start containers
docker-compose down
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Restart queue workers
docker-compose restart queue

echo "✅ Deployment completed!"
