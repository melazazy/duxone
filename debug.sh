#!/bin/bash

echo "===== DOCKER CONTAINERS ====="
docker ps --format "table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "===== DOCKER COMPOSE SERVICES ====="
docker compose ps

echo ""
echo "===== LARAVEL .env CONFIG ====="
if docker compose exec app bash -c "cd /var/www && test -f .env"; then
  echo ".env file found in /var/www"
else
  echo ".env file not found!"
fi

echo ""
echo "===== RUNNING LOGS (last 50 lines) ====="
docker compose logs --tail=50 app

echo ""
echo "===== CHECK PHP VERSION INSIDE CONTAINER ====="
docker compose exec app php -v

echo ""
echo "===== CHECK NPM/VITE STATUS ====="
docker compose exec node_build bash -c "cd /var/www && ps aux | grep vite | grep -v grep || echo 'No vite process found'"

echo ""
echo "===== CHECK LARAVEL CONFIG ====="
docker compose exec app bash -c "cd /var/www && php artisan --version"
docker compose exec app bash -c "cd /var/www && php artisan route:list || true"

echo ""
echo "===== DATABASE MIGRATIONS STATUS ====="
docker compose exec app bash -c "cd /var/www && php artisan migrate:status"

echo ""
echo "===== PERMISSIONS ON STORAGE/BOOTSTRAP ====="
docker compose exec app bash -c "cd /var/www && ls -ld bootstrap/cache storage"
