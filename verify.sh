#!/bin/bash

set -e

echo "🚀 Verifying Laravel setup & installed packages..."

# Helper: Run artisan inside container
artisan() {
  docker exec -it laravel_app php artisan "$@"
}

# 1. Breeze (Auth + Livewire)
echo "✅ Checking Breeze (Auth)..."
if artisan route:list | grep -q "register"; then
  echo "   ✔ Breeze routes exist (/register, /login)"
else
  echo "   ❌ Breeze not detected!"
fi

# 2. Filament
echo "✅ Checking Filament..."
if artisan filament:upgrade --version >/dev/null 2>&1; then
  echo "   ✔ Filament installed"
else
  echo "   ❌ Filament not detected!"
fi

# 3. Spatie Permissions
echo "✅ Checking Spatie Permissions..."
if artisan migrate:status | grep -q "create_permission_tables"; then
  echo "   ✔ Permission tables migration found"
else
  echo "   ❌ Spatie Permission not detected!"
fi

# 4. Tenancy
echo "✅ Checking Stancl Tenancy..."
if artisan | grep -q "tenants:create"; then
  echo "   ✔ Tenancy commands available"
else
  echo "   ❌ Tenancy not detected!"
fi

# 5. Cashier
echo "✅ Checking Cashier..."
if artisan migrate:status | grep -q "subscriptions"; then
  echo "   ✔ Cashier tables found"
else
  echo "   ❌ Cashier not detected!"
fi

# 6. Telescope
echo "✅ Checking Telescope..."
if artisan | grep -q "telescope:"; then
  echo "   ✔ Telescope commands available"
else
  echo "   ❌ Telescope not detected!"
fi

# 7. Horizon
echo "✅ Checking Horizon..."
if artisan | grep -q "horizon:"; then
  echo "   ✔ Horizon commands available"
else
  echo "   ❌ Horizon not detected!"
fi

# 8. Debugbar
echo "✅ Checking Debugbar..."
if grep -q "Barryvdh\\\Debugbar\\\ServiceProvider" src/config/app.php 2>/dev/null; then
  echo "   ✔ Debugbar registered"
else
  echo "   ⚠ Debugbar may not be active (check manually in browser)"
fi

# 9. Spatie Backup
echo "✅ Checking Spatie Backup..."
if artisan | grep -q "backup:run"; then
  echo "   ✔ Backup commands available"
else
  echo "   ❌ Backup not detected!"
fi

# 10. Spatie Activity Log
echo "✅ Checking Spatie Activity Log..."
if artisan migrate:status | grep -q "activity_log"; then
  echo "   ✔ Activity Log table migration found"
else
  echo "   ❌ Activity Log not detected!"
fi

echo "🎉 Verification finished!"
