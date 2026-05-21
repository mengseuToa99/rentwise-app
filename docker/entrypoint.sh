#!/bin/sh
set -e

# Cache config + views for a faster, production-like boot.
# (route:cache is intentionally skipped — it breaks if any route uses a closure.)
php artisan config:cache
php artisan view:cache

# Apply database migrations against the Railway MySQL DB (idempotent, safe to re-run).
php artisan migrate --force

# Symlink storage/app/public -> public/storage for uploaded files.
php artisan storage:link || true

# Serve on the port Railway provides (defaults to 8080 locally).
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
