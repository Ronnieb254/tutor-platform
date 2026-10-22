#!/bin/bash
set -e

echo "🚀 Starting Mwalimu Tutor Platform..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL..."
until nc -z mysql 3306; do
  echo "MySQL is unavailable - sleeping"
  sleep 2
done
echo "✅ MySQL is up!"

# Create storage directories if they don't exist
echo "📁 Setting up storage directories..."
mkdir -p storage/app/public/documents
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Create symbolic link from public/storage to storage/app/public
echo "🔗 Creating storage symbolic link..."
if [ ! -L public/storage ]; then
  rm -rf public/storage  # Remove if it exists as a directory
  ln -sf ../storage/app/public public/storage
  echo "✅ Storage link created: public/storage -> storage/app/public"
else
  echo "ℹ️  Storage link already exists"
fi

# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations (with --force for production)
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"

# Start supervisor (which starts nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
