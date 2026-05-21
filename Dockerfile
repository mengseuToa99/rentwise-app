# syntax=docker/dockerfile:1

# ---- Stage 1: Composer dependencies (provides vendor/ for the asset build) ----
# resources/css/app.css imports vendor/livewire/flux/dist/flux.css, so vendor/
# must exist before Vite runs. --no-scripts/--ignore-platform-reqs keep this
# stage lightweight; the runtime stage does a proper install below.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction \
    --no-scripts --no-autoloader --ignore-platform-reqs

# ---- Stage 2: build front-end assets with Vite ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
# Flux CSS lives in vendor/ — bring it in so the Tailwind/Vite build resolves it.
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---- Stage 3: PHP runtime ----
FROM php:8.2-cli-bookworm

# PHP extensions Laravel 12 needs (pdo_mysql for your Railway DB).
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_mysql mbstring bcmath zip gd exif intl pcntl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# App source (node_modules / vendor / .env excluded via .dockerignore)
COPY . .

# Compiled Vite assets from the build stage
COPY --from=assets /app/public/build ./public/build

# Production PHP dependencies + optimized autoloader, then fix writable dirs.
# --no-scripts keeps the build hermetic: package discovery boots the app and
# would fail here (no env vars yet). It runs at startup instead (see entrypoint).
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
ENTRYPOINT ["sh", "docker/entrypoint.sh"]
