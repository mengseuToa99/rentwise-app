#!/bin/sh
set -e

# Safety net: this app boots the broadcaster on startup, and the default driver
# (reverb) tries to build a Pusher client with a null key -> hard crash. Force a
# harmless default unless you've explicitly configured broadcasting in Railway.
export BROADCAST_DRIVER="${BROADCAST_DRIVER:-log}"
export BROADCAST_CONNECTION="${BROADCAST_CONNECTION:-log}"

# Discover packages now that env vars are present (skipped at build time).
php artisan package:discover --ansi

# Cache config for a faster, production-like boot.
# route:cache is skipped (breaks on closure routes); view:cache is skipped
# (precompiles every Blade view and aborts on the missing <x-secondary-button>
# component in maintenance-request/create). Views compile lazily per-request.
php artisan config:cache

# Apply database migrations against the Railway MySQL DB (idempotent, safe to re-run).
php artisan migrate --force

# Symlink storage/app/public -> public/storage for uploaded files.
php artisan storage:link || true

# Serve on the port Railway provides (defaults to 8080 locally).
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
