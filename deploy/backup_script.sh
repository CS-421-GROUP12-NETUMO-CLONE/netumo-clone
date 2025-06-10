#!/bin/bash
set -e

DB_BACKUP_DIR="./database_backups"
mkdir -p "$DB_BACKUP_DIR"
BACKUP_FILE="$DB_BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql.gz"

echo "Creating database backup..."
docker compose -f dev.yml exec -T mysql sh -c "mysqldump -u 'laravel' -p'secret' 'laravel' --single-transaction --routines --triggers | gzip" > "$BACKUP_FILE"

if [ ! -s "$BACKUP_FILE" ]; then
  echo "::error::Database backup failed!"
  exit 1
fi
echo "Database backup created: $BACKUP_FILE ($(du -h "$BACKUP_FILE" | cut -f1))"
