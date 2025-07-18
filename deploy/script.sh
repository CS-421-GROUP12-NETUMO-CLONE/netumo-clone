#!/bin/bash

# Auto-detect container (name like app1, app2, php)
CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'app[0-9]*|php' | head -n 1)

if [ -z "$CONTAINER" ]; then
  echo "Could not find a running Laravel container (name containing 'app' or 'php')."
  exit 1
fi

function help() {
  echo "Docker Helper"
  echo ""
  echo "Usage: ./deploy.sh [command]"
  echo ""
  echo "Available commands:"
  echo "  artisan [args]     Run any artisan command"
  echo "  migrate            Run migrations"
  echo "  seed               Run database seeder"
  echo "  fresh              Drop all tables and re-run migrations"
  echo "  install            Run composer install"
  echo "  bash               Open bash in a container"
  echo "  queue              Run queue:work"
  echo "  schedule           Run schedule:run"
  echo "  logs               Tail Laravel log"
  echo "  help               Show this help message"
  echo "  install            Running: composer install --no-dev --optimize-autoloader"
  echo "  clean-vite         Removing old build in container..."
  echo "  clean-vite         Removing the old build"
  echo "  vite               Build Vite assets on host and copy to the container"
  echo "  addpermission      Add permission to storage and bootstrap/cache folder"
  echo "  changeuser         Change ownership"
  echo "  trustfolder         Trust user ownership"
}

case "$1" in
  artisan)
    shift
    docker exec -it $CONTAINER php artisan "$@"
    ;;
  migrate)
    docker exec -it $CONTAINER php artisan migrate
    ;;
  seed)
    docker exec -it $CONTAINER php artisan db:seed
    ;;
  fresh)
    docker exec -it $CONTAINER php artisan migrate:fresh --seed
    ;;
  install)
    docker exec -it $CONTAINER composer install --no-dev --optimize-autoloader
    ;;
  bash)
    docker exec -it $CONTAINER bash
    ;;
  queue)
    docker exec -it $CONTAINER php artisan queue:work
    ;;
  schedule)
    docker exec -it $CONTAINER php artisan schedule:run
    ;;
  changeuser)
    docker exec -it $CONTAINER chown -R $(whoami):$(whoami) /var/www
    ;;
  trustfolder)
    docker exec -it $CONTAINER git config --global --add safe.directory /var/www
    ;;
  logs)
    docker exec -it $CONTAINER tail -f storage/logs/laravel.log
    ;;
  clean-vite)
    echo "Removing old build in container..."
    docker exec -it $CONTAINER rm -rf /var/www/public/build
    ;;
  vite)
    echo "Building Vite assets on host..."
    npm install && npm run build
    echo "Copying public/build to Docker container..."
    docker cp public/build $CONTAINER:/var/www/public
    ;;
  addpermission)
    echo "Change permission to bootstrap file"
    docker exec -it $CONTAINER chown -R www-data:www-data storage bootstrap/cache
    echo "Permission changed to container $CONTAINER"
    ;;
  help|*)
    help
    ;;
esac
