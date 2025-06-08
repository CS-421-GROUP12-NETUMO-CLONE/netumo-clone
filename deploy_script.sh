#!/bin/bash

set -e

# Auto-detect container (name like app1, app2, php)
CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'app[0-9]*|php' | head -n 1)

if [ -z "$CONTAINER" ]; then
  echo "Could not find a running Laravel container (name containing 'app' or 'php')."
  exit 1
fi

echo "Stopping any running containers..."
docker-compose down

echo "Rebuilding Docker images..."
docker-compose build --no-cache

echo "Starting containers in background..."
docker-compose up -d

echo "Running Laravel migrations..."
docker-compose exec $CONTAINER php artisan migrate --force

echo "Creating storage symlink..."
docker-compose exec $CONTAINER php artisan storage:link

echo "✅ Deployment complete!"
