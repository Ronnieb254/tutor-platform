#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "=========================================="
echo "🔍 MWALIMU PLATFORM SYSTEM CHECK"
echo "=========================================="
echo ""

# Function to check status
check_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ PASSED${NC}"
        return 0
    else
        echo -e "${RED}❌ FAILED${NC}"
        return 1
    fi
}

# Counter for issues
ISSUES=0

# ============================================
# 1. DOCKER CONTAINERS
# ============================================
echo -e "${BLUE}1️⃣  Checking Docker Containers${NC}"
echo "=========================================="

echo -n "App container running: "
docker ps | grep -q mwalimu-app && check_status || { check_status; ((ISSUES++)); }

echo -n "MySQL container running: "
docker ps | grep -q mwalimu-mysql && check_status || { check_status; ((ISSUES++)); }

echo -n "Redis container running: "
docker ps | grep -q mwalimu-redis && check_status || { check_status; ((ISSUES++)); }

echo -n "Nginx-proxy running: "
docker ps | grep -q nginx-proxy && check_status || { check_status; ((ISSUES++)); }

echo -n "Let's Encrypt companion running: "
docker ps | grep -q letsencrypt && check_status || { check_status; ((ISSUES++)); }

echo -n "phpMyAdmin running: "
docker ps | grep -q mwalimu-phpmyadmin && check_status || echo -e "${YELLOW}⚠️  OPTIONAL (not running)${NC}"

echo ""

# ============================================
# 2. PHP EXTENSIONS
# ============================================
echo -e "${BLUE}2️⃣  Checking PHP Extensions${NC}"
echo "=========================================="

for ext in pdo_mysql mysqli redis gd zip intl mbstring bcmath; do
    echo -n "PHP $ext extension: "
    docker exec mwalimu-app php -m 2>/dev/null | grep -q "^$ext$" && check_status || { check_status; ((ISSUES++)); }
done

echo -n "PHP Version: "
docker exec mwalimu-app php -v | head -1
echo ""

# ============================================
# 3. DATABASE
# ============================================
echo -e "${BLUE}3️⃣  Checking Database${NC}"
echo "=========================================="

echo -n "Database connection: "
docker exec mwalimu-app php artisan db:show &>/dev/null && check_status || { check_status; ((ISSUES++)); }

echo -n "Users table exists: "
docker exec mwalimu-mysql mysql -uroot -p${DB_ROOT_PASSWORD:-root} tutor_platform -e "DESCRIBE users;" &>/dev/null && check_status || { check_status; ((ISSUES++)); }

echo -n "Migrations status: "
PENDING=$(docker exec mwalimu-app php artisan migrate:status 2>/dev/null | grep -c "Pending")
if [ "$PENDING" -eq 0 ]; then
    check_status
else
    echo -e "${YELLOW}⚠️  $PENDING pending migrations${NC}"
    ((ISSUES++))
fi

echo -n "Total tables: "
TABLES=$(docker exec mwalimu-mysql mysql -uroot -p${DB_ROOT_PASSWORD:-root} tutor_platform -e "SHOW TABLES;" 2>/dev/null | wc -l)
echo "$((TABLES-1)) tables"

echo ""

# ============================================
# 4. LARAVEL APPLICATION
# ============================================
echo -e "${BLUE}4️⃣  Checking Laravel Application${NC}"
echo "=========================================="

echo -n "APP_KEY set: "
docker exec mwalimu-app grep -q "^APP_KEY=base64:" /var/www/html/.env && check_status || { check_status; ((ISSUES++)); }

echo -n "APP_ENV: "
docker exec mwalimu-app grep "^APP_ENV=" /var/www/html/.env | cut -d= -f2

echo -n "APP_DEBUG: "
docker exec mwalimu-app grep "^APP_DEBUG=" /var/www/html/.env | cut -d= -f2

echo -n "Storage writable: "
docker exec mwalimu-app test -w /var/www/html/storage && check_status || { check_status; ((ISSUES++)); }

echo -n "Bootstrap cache writable: "
docker exec mwalimu-app test -w /var/www/html/bootstrap/cache && check_status || { check_status; ((ISSUES++)); }

echo -n "Routes cached: "
docker exec mwalimu-app test -f /var/www/html/bootstrap/cache/routes-v7.php && check_status || echo -e "${YELLOW}⚠️  NOT CACHED${NC}"

echo ""

# ============================================
# 5. STORAGE & PERMISSIONS
# ============================================
echo -e "${BLUE}5️⃣  Checking Storage & Permissions${NC}"
echo "=========================================="

echo -n "Storage directory exists: "
docker exec mwalimu-app test -d /var/www/html/storage && check_status || { check_status; ((ISSUES++)); }

echo -n "Logs directory writable: "
docker exec mwalimu-app test -w /var/www/html/storage/logs && check_status || { check_status; ((ISSUES++)); }

echo -n "Framework directory writable: "
docker exec mwalimu-app test -w /var/www/html/storage/framework && check_status || { check_status; ((ISSUES++)); }

echo -n "Public storage linked: "
docker exec mwalimu-app test -L /var/www/html/public/storage && check_status || echo -e "${YELLOW}⚠️  NOT LINKED (run: php artisan storage:link)${NC}"

echo ""

# ============================================
# 6. WEB SERVER
# ============================================
echo -e "${BLUE}6️⃣  Checking Web Server${NC}"
echo "=========================================="

echo -n "Nginx running in container: "
docker exec mwalimu-app pgrep nginx &>/dev/null && check_status || { check_status; ((ISSUES++)); }

echo -n "PHP-FPM running: "
docker exec mwalimu-app pgrep php-fpm &>/dev/null && check_status || { check_status; ((ISSUES++)); }

echo -n "Supervisor running: "
docker exec mwalimu-app pgrep supervisord &>/dev/null && check_status || { check_status; ((ISSUES++)); }

echo -n "HTTP response: "
HTTP_CODE=$(docker exec mwalimu-app curl -s -o /dev/null -w "%{http_code}" http://localhost/)
if [ "$HTTP_CODE" == "200" ] || [ "$HTTP_CODE" == "302" ]; then
    check_status
else
    echo -e "${RED}❌ HTTP $HTTP_CODE${NC}"
    ((ISSUES++))
fi

echo ""

# ============================================
# 7. NETWORKING
# ============================================
echo -e "${BLUE}7️⃣  Checking Networking${NC}"
echo "=========================================="

echo -n "Docker network exists: "
docker network ls | grep -q nginx-proxy && check_status || { check_status; ((ISSUES++)); }

echo -n "App in nginx-proxy network: "
docker inspect mwalimu-app 2>/dev/null | grep -q nginx-proxy && check_status || { check_status; ((ISSUES++)); }

echo -n "VIRTUAL_HOST set: "
docker exec mwalimu-app env | grep -q "VIRTUAL_HOST=" && check_status || { check_status; ((ISSUES++)); }

echo -n "Port 80 listening: "
ss -tlnp | grep -q ":80 " && check_status || { check_status; ((ISSUES++)); }

echo -n "Port 443 listening: "
ss -tlnp | grep -q ":443 " && check_status || { check_status; ((ISSUES++)); }

echo ""

# ============================================
# 8. SSL CERTIFICATES
# ============================================
echo -e "${BLUE}8️⃣  Checking SSL Certificates${NC}"
echo "=========================================="

DOMAIN=$(docker exec mwalimu-app env 2>/dev/null | grep "^VIRTUAL_HOST=" | cut -d= -f2)
if [ -n "$DOMAIN" ]; then
    echo "Domain: $DOMAIN"
    
    echo -n "SSL certificate exists: "
    docker exec nginx-proxy test -f /etc/nginx/certs/${DOMAIN}.crt 2>/dev/null && check_status || echo -e "${YELLOW}⚠️  NOT YET ISSUED${NC}"
    
    echo -n "SSL key exists: "
    docker exec nginx-proxy test -f /etc/nginx/certs/${DOMAIN}.key 2>/dev/null && check_status || echo -e "${YELLOW}⚠️  NOT YET ISSUED${NC}"
else
    echo -e "${YELLOW}⚠️  No VIRTUAL_HOST found${NC}"
fi

echo ""

# ============================================
# 9. CRITICAL ROUTES
# ============================================
echo -e "${BLUE}9️⃣  Checking Critical Routes${NC}"
echo "=========================================="

ROUTES=$(docker exec mwalimu-app php artisan route:list 2>/dev/null)

for route in "login" "register" "dashboard" "tutors.index" "messages.index"; do
    echo -n "Route '$route': "
    echo "$ROUTES" | grep -q "$route" && check_status || { check_status; ((ISSUES++)); }
done

echo ""

# ============================================
# 10. LOGS CHECK
# ============================================
echo -e "${BLUE}🔟 Checking Recent Errors${NC}"
echo "=========================================="

ERROR_COUNT=$(docker exec mwalimu-app tail -100 /var/www/html/storage/logs/laravel.log 2>/dev/null | grep -c "ERROR\|Exception" || echo "0")
echo "Recent errors in logs: $ERROR_COUNT"

if [ "$ERROR_COUNT" -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Recent errors found. Last 3:${NC}"
    docker exec mwalimu-app tail -100 /var/www/html/storage/logs/laravel.log 2>/dev/null | grep "ERROR\|Exception" | tail -3
    echo ""
fi

# ============================================
# 11. QUEUE & SCHEDULER
# ============================================
echo -e "${BLUE}1️⃣1️⃣  Checking Queue & Scheduler${NC}"
echo "=========================================="

echo -n "Queue worker running: "
docker ps | grep -q mwalimu-queue && check_status || echo -e "${YELLOW}⚠️  NOT RUNNING${NC}"

echo -n "Scheduler running: "
docker ps | grep -q mwalimu-scheduler && check_status || echo -e "${YELLOW}⚠️  NOT RUNNING${NC}"

echo ""

# ============================================
# SUMMARY
# ============================================
echo "=========================================="
echo -e "${BLUE}📊 SYSTEM CHECK SUMMARY${NC}"
echo "=========================================="

if [ $ISSUES -eq 0 ]; then
    echo -e "${GREEN}✅ ALL CHECKS PASSED!${NC}"
    echo ""
    echo "Your Mwalimu platform is healthy and ready to use! 🎉"
else
    echo -e "${RED}❌ FOUND $ISSUES ISSUE(S)${NC}"
    echo ""
    echo "Please review the failed checks above and fix the issues."
fi

echo ""
echo "=========================================="
echo "🔗 QUICK ACCESS"
echo "=========================================="
echo "Application: http://$DOMAIN (or https if SSL ready)"
echo "phpMyAdmin: http://pma.${DOMAIN#*.} (if running)"
echo ""
echo "📝 USEFUL COMMANDS:"
echo ""
echo "View logs:"
echo "  docker logs mwalimu-app -f"
echo ""
echo "Clear cache:"
echo "  docker exec mwalimu-app php artisan optimize:clear"
echo ""
echo "Run migrations:"
echo "  docker exec mwalimu-app php artisan migrate --force"
echo ""
echo "Create admin user:"
echo "  docker exec mwalimu-app php artisan tinker"
echo "  >>> User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>bcrypt('password'),'role'=>'admin','email_verified_at'=>now()]);"
echo ""
