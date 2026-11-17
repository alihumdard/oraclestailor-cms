
# ==========================================
# Stage 1: Build Laravel Frontend Assets
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build


# ==========================================
# Stage 2: Laravel PHP + Apache
# ==========================================
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Laravel application directory
WORKDIR /var/www/html

# Copy Laravel source code
COPY . .


# Install production dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# Copy compiled Vite assets
COPY --from=frontend /app/public/build ./public/build


# ==========================================
# Configure Apache
# ==========================================
RUN printf '%s\n' \
    '<VirtualHost *:80>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '    ErrorLog ${APACHE_LOG_DIR}/error.log' \
    '    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
    '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf


# ==========================================
# Prepare SQLite and Laravel permissions
# ==========================================
RUN mkdir -p /var/data \
    && touch /var/data/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache /var/data \
    && chmod -R 775 storage bootstrap/cache /var/data


# ==========================================
# Render startup script
# ==========================================
RUN printf '%s\n' \
    '#!/bin/sh' \
    'set -e' \
    'mkdir -p /var/data' \
    'touch /var/data/database.sqlite' \
    'chown -R www-data:www-data /var/data storage bootstrap/cache' \
    'chmod -R 775 /var/data storage bootstrap/cache' \
    'PORT="${PORT:-10000}"' \
    'sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf' \
    'sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf' \
    'exec apache2-foreground' \
    > /usr/local/bin/render-start.sh \
    && chmod +x /usr/local/bin/render-start.sh


# Render port
EXPOSE 10000

CMD ["/usr/local/bin/render-start.sh"]
