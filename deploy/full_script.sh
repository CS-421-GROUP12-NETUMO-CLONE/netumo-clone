#!/bin/bash
set -e

# Get Laravel container
CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'app[0-9]*|php' | head -n 1)

if [ -z "$CONTAINER" ]; then
  echo "::error::Could not find a running app container (name containing 'app')."
  exit 1
fi

echo "App running on node: $CONTAINER"

# Database Backup
DB_BACKUP_DIR="./database_backups"
mkdir -p "$DB_BACKUP_DIR"
BACKUP_FILE="$DB_BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql.gz"

echo "Creating database backup..."
docker-compose exec -T mysql sh -c "mysqldump -u 'laravel' -p'secret' 'laravel' --single-transaction --routines --triggers | gzip" > "$BACKUP_FILE"

if [ ! -s "$BACKUP_FILE" ]; then
  echo "::error::Database backup failed!"
  exit 1
fi
echo "Database backup created: $BACKUP_FILE ($(du -h "$BACKUP_FILE" | cut -f1))"

# Laravel Deployment Commands
docker-compose exec "$CONTAINER" php artisan migrate --force
docker-compose exec "$CONTAINER" php artisan storage:link
docker-compose exec "$CONTAINER" php artisan optimize:clear
docker-compose exec "$CONTAINER" php artisan optimize

# Supervisor Setup
echo "Setting up Supervisor for queue worker..."
if ! command -v supervisorctl >/dev/null; then
  sudo apt-get update
  sudo apt-get install -y supervisor
fi

sudo cp deploy/supervisor/netumo-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart netumo-worker:*

echo "Deployment completed successfully"
exit 0
