# Mwalimu Tutoring Platform - production image
# Place at ./microservice/tutor-platform/Dockerfile
FROM php:8.4-fpm

WORKDIR /var/www/html

# System dependencies (netcat-openbsd provides "nc" for the entrypoint's DB wait)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    netcat-openbsd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && pecl install redis && docker-php-ext-enable redis

# Composer (pinned to major version 2)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Application code (.env, .git, vendor etc. are excluded by .dockerignore)
COPY . /var/www/html

# Nginx and supervisor config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP dependencies (production)
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Permissions (after composer so vendor/ is covered too)
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint kept as a real file, not an echo hack
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]