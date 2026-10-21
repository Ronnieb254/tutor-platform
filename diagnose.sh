#!/bin/bash

echo "=========================================="
echo "MWALIMU PLATFORM DIAGNOSTICS"
echo "=========================================="
echo ""

echo "1️⃣  CHECKING DOCKER CONTAINERS"
echo "=========================================="
docker ps | grep mwalimu
echo ""

echo "2️⃣  CHECKING DATABASE CONNECTION"
echo "=========================================="
docker exec mwalimu-app php artisan db:show 2>&1 || echo "❌ Database connection failed"
echo ""

echo "3️⃣  CHECKING MIGRATIONS STATUS"
echo "=========================================="
docker exec mwalimu-app php artisan migrate:status 2>&1
echo ""

echo "4️⃣  CHECKING IF USERS TABLE EXISTS"
echo "=========================================="
docker exec mwalimu-mysql mysql -uroot -p${DB_ROOT_PASSWORD} tutor_platform -e "DESCRIBE users;" 2>&1 || echo "❌ Users table doesn't exist"
echo ""

echo "5️⃣  CHECKING STORAGE PERMISSIONS"
echo "=========================================="
docker exec mwalimu-app ls -la /var/www/html/storage/logs/ 2>&1
echo ""

echo "6️⃣  CHECKING LARAVEL LOGS (Last 30 lines)"
echo "=========================================="
docker exec mwalimu-app tail -30 /var/www/html/storage/logs/laravel.log 2>&1 || echo "⚠️  No log file found"
echo ""

echo "7️⃣  CHECKING .ENV CONFIGURATION"
echo "=========================================="
docker exec mwalimu-app grep -E "^(APP_ENV|APP_DEBUG|DB_CONNECTION|DB_HOST|DB_DATABASE)" /var/www/html/.env 2>&1
echo ""

echo "8️⃣  TESTING DATABASE CREDENTIALS"
echo "=========================================="
docker exec mwalimu-mysql mysql -uroot -p${DB_ROOT_PASSWORD} -e "SELECT 'Database OK' as status;" 2>&1 || echo "❌ MySQL connection failed"
echo ""

echo "9️⃣  CHECKING AUTHCONTROLLER EXISTS"
echo "=========================================="
docker exec mwalimu-app test -f /var/www/html/app/Http/Controllers/Auth/AuthController.php && echo "✅ AuthController exists" || echo "❌ AuthController missing"
echo ""

echo "🔟 CHECKING ROUTES"
echo "=========================================="
docker exec mwalimu-app php artisan route:list | grep -E "(register|dashboard)" 2>&1 | head -20
echo ""

echo "1️⃣1️⃣  TESTING ARTISAN TINKER (Create Test User)"
echo "=========================================="
docker exec mwalimu-app php artisan tinker --execute="try { \$user = App\\Models\\User::create(['name'=>'Test User','email'=>'test@test.com','password'=>bcrypt('password'),'role'=>'student','email_verified_at'=>now()]); echo 'User created: ' . \$user->email; } catch(Exception \$e) { echo 'Error: ' . \$e->getMessage(); }" 2>&1
echo ""

echo "1️⃣2️⃣  CHECKING PHP VERSION & EXTENSIONS"
echo "=========================================="
docker exec mwalimu-app php -v
docker exec mwalimu-app php -m | grep -E "(pdo_mysql|mysqli|PDO)"
echo ""

echo "1️⃣3️⃣  CHECKING APP KEY"
echo "=========================================="
docker exec mwalimu-app grep "^APP_KEY=" /var/www/html/.env 2>&1 || echo "⚠️  APP_KEY not set"
echo ""

echo "=========================================="
echo "DIAGNOSIS COMPLETE"
echo "=========================================="
echo ""
echo "📋 QUICK FIXES:"
echo ""
echo "If migrations not run:"
echo "  docker exec mwalimu-app php artisan migrate --force"
echo ""
echo "If storage permission errors:"
echo "  docker exec mwalimu-app chown -R www-data:www-data /var/www/html/storage"
echo "  docker exec mwalimu-app chmod -R 775 /var/www/html/storage"
echo ""
echo "If APP_KEY missing:"
echo "  docker exec mwalimu-app php artisan key:generate --force"
echo ""
echo "Clear cache:"
echo "  docker exec mwalimu-app php artisan optimize:clear"
echo ""
