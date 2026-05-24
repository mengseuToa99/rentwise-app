#!/bin/sh
set -e

# Safety net: this app boots the broadcaster on startup, and the default driver
# (reverb) tries to build a Pusher client with a null key -> hard crash. Force a
# harmless default unless you've explicitly configured broadcasting in Railway.
export BROADCAST_DRIVER="${BROADCAST_DRIVER:-log}"
export BROADCAST_CONNECTION="${BROADCAST_CONNECTION:-log}"

# Default Octane to FrankenPHP so `octane:*` helper commands resolve the right
# server. The start command below also passes --server=frankenphp explicitly.
export OCTANE_SERVER="${OCTANE_SERVER:-frankenphp}"

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

# Serve via Laravel Octane on FrankenPHP. The app boots once per worker and stays
# resident in memory, so each request skips the framework bootstrap that made
# `php artisan serve` slow. Octane keeps the (remote Railway) MySQL connection
# alive between requests too. --max-requests recycles each worker periodically to
# bound memory, a safeguard against leaks in long-lived Livewire workers.
# Workers default to one per CPU core; override with OCTANE_WORKERS if needed.
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port="${PORT:-8080}" \
    --workers="${OCTANE_WORKERS:-auto}" \
    --max-requests="${OCTANE_MAX_REQUESTS:-500}"
