# Mwalimu Tutoring Platform - Production Dockerfile (Ubuntu Server Optimized)
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install PHP dependencies (production)
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Note: The following commands will run during container initialization via entrypoint
# - php artisan key:generate (if APP_KEY is empty)
# - php artisan migrate --force (on first run)
# - php artisan optimize

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Wait for MySQL to be ready\n\
echo "Waiting for MySQL..."\n\
while ! nc -z mysql 3306; do\n\
    sleep 1\n\
done\n\
echo "MySQL is ready!"\n\
\n\
# Run migrations on first start\n\
if [ ! -f /var/www/html/storage/.migrated ]; then\n\
    echo "Running initial migrations..."\n\
    php artisan migrate --force\n\
    touch /var/www/html/storage/.migrated\n\
fi\n\
\n\
# Optimize Laravel\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Set permissions\n\
chown -R www-data:www-data /var/www/html/storage\n\
chmod -R 775 /var/www/html/storage\n\
\n\
# Start supervisor\n\
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

# Expose port 80
EXPOSE 80

# Use entrypoint for initialization
ENTRYPOINT ["/entrypoint.sh"]

