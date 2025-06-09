#!/bin/bash

set -e

echo "Pulling latest images..."
docker-compose pull

echo "Stopping any running containers..."
docker-compose down

echo "Rebuilding Docker images (if needed)..."
docker-compose build --pull

echo "Starting containers in background..."
docker-compose up -d

# Wait for containers to be healthy
echo "Waiting for services to be ready..."
sleep 15

CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'app[0-9]*|php' | head -n 1)

if [ -z "$CONTAINER" ]; then
  echo "Could not find a running Laravel container (name containing 'app' or 'php')."
  exit 1
fi

echo "Running Laravel migrations..."
docker-compose exec $CONTAINER php artisan migrate --force

echo "Creating storage symlink..."
docker-compose exec $CONTAINER php artisan storage:link

echo "Clearing caches..."
docker-compose exec $CONTAINER php artisan optimize:clear
docker-compose exec $CONTAINER php artisan optimize

echo "Deployment complete!"
