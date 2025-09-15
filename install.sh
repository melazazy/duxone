#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Starting setup for Business Platform..."

# 1. Build & start Docker containers
echo "🐳 Building Docker containers..."
docker-compose up -d --build

# 2. Prepare Laravel installation inside src/
echo "📂 Preparing Laravel in ./src ..."
# Clean the directory and install Laravel in temp location first
docker exec -it laravel_app bash -c "find /var/www -mindepth 1 -delete" 2>/dev/null || true
docker exec -it laravel_app bash -c "composer create-project laravel/laravel /tmp/laravel"
docker exec -it laravel_app bash -c "mv /tmp/laravel/* /tmp/laravel/.[^.]* /var/www/ 2>/dev/null || mv /tmp/laravel/* /var/www/"
docker exec -it laravel_app bash -c "rm -rf /tmp/laravel"

# 3. Setup environment
echo "⚙️ Setting up environment..."
docker exec -it laravel_app bash -c "cp .env.example .env && php artisan key:generate"

# 4. Update DB settings in .env
echo "🛠 Configuring database connection..."
docker exec -it laravel_app bash -c "sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/' .env && \
 sed -i 's/DB_USERNAME=root/DB_USERNAME=laravel/' .env && \
 sed -i 's/DB_PASSWORD=/DB_PASSWORD=laravel/' .env && \
 sed -i 's/DB_DATABASE=laravel/DB_DATABASE=laravel/' .env && \
 sed -i 's/DB_PORT=3306/DB_PORT=3306/' .env"

# 5. Run migrations
echo "📂 Running initial migrations..."
docker exec -it laravel_app bash -c "php artisan migrate"

# 6. Install NPM dependencies + Tailwind
echo "🎨 Installing frontend dependencies..."
docker exec -it node_build bash -c "npm install && npm install tailwindcss postcss autoprefixer && npx tailwindcss init -p"

# 7. Install Core Packages
echo "🔑 Installing Laravel Breeze..."
docker exec -it laravel_app bash -c "composer require laravel/breeze --dev && php artisan breeze:install livewire && php artisan migrate"

echo "🔐 Installing Spatie Permissions..."
docker exec -it laravel_app bash -c "composer require spatie/laravel-permission && php artisan vendor:publish --provider='Spatie\Permission\PermissionServiceProvider' && php artisan migrate"

echo "🏢 Installing Stancl Tenancy..."
docker exec -it laravel_app bash -c "composer require stancl/tenancy && php artisan tenancy:install && php artisan migrate"

echo "💳 Installing Laravel Cashier..."
docker exec -it laravel_app bash -c "composer require laravel/cashier && php artisan vendor:publish --tag='cashier-migrations' && php artisan migrate"

echo "🖥 Installing Filament Admin..."
docker exec -it laravel_app bash -c "composer require filament/filament:'^3.0' -W && php artisan filament:install && php artisan migrate"

# 8. Utilities
echo "⚡ Installing utilities..."
docker exec -it laravel_app bash -c "composer require laravel/telescope --dev && php artisan telescope:install && php artisan migrate"
docker exec -it laravel_app bash -c "composer require laravel/horizon && php artisan horizon:install && php artisan migrate"
docker exec -it laravel_app bash -c "composer require barryvdh/laravel-debugbar --dev"
docker exec -it laravel_app bash -c "composer require spatie/laravel-backup spatie/laravel-activitylog"

echo "✅ Setup complete!"
echo "🌐 Laravel available at: http://localhost:8081"
echo "🗄  PhpMyAdmin available at: http://localhost:8080"
echo "🐬 MySQL available at: localhost:3307 (user: laravel, pass: laravel, db: laravel)"
echo "📦 Redis running at: localhost:6380"
echo "🎨 Vite/Node running at: http://localhost:5173"
