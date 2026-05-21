# syntax=docker/dockerfile:1

# ---- Stage 1: build front-end assets with Vite ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---- Stage 2: PHP application ----
FROM php:8.2-cli-bookworm

# Install the PHP extensions Laravel 12 needs (pdo_mysql for your Railway DB).
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_mysql mbstring bcmath zip gd exif intl pcntl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# App source (node_modules / vendor / .env excluded via .dockerignore)
COPY . .

# Bring in the compiled Vite assets from the build stage
COPY --from=assets /app/public/build ./public/build

# Production PHP dependencies + autoloader, then fix writable dirs
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
ENTRYPOINT ["sh", "docker/entrypoint.sh"]
