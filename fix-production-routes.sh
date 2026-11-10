#!/bin/bash

# Fix Production Route Cache
# Usage: ./fix-production-routes.sh [production_dir]
# Example: ./fix-production-routes.sh /var/www/html

PROD_DIR="${1:-.}"

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  Fixing Production Route Cache for Document Viewer Modal    ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

if [ ! -d "$PROD_DIR" ]; then
    echo "❌ Error: Directory '$PROD_DIR' not found"
    exit 1
fi

cd "$PROD_DIR" || exit

echo "📍 Working directory: $(pwd)"
echo ""

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Are you in the Laravel root?"
    exit 1
fi

echo "🗑️  Clearing caches..."
php artisan cache:clear && echo "   ✅ Application cache cleared"
php artisan config:clear && echo "   ✅ Configuration cache cleared"
php artisan route:clear && echo "   ✅ Route cache cleared"
php artisan view:clear && echo "   ✅ View cache cleared"
echo ""

echo "🔨 Rebuilding caches..."
php artisan config:cache && echo "   ✅ Configuration cached"
php artisan route:cache && echo "   ✅ Routes cached"
php artisan view:cache && echo "   ✅ Views cached"
echo ""

echo "⚙️  Running optimization..."
php artisan optimize && echo "   ✅ Optimization complete"
echo ""

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  ✅ All done! Document viewer route should now work.        ║"
echo "║                                                              ║"
echo "║  Next steps:                                                 ║"
echo "║  1. Test: https://mwalimu.ovaldesk.co.ke/admin/tutors      ║"
echo "║  2. Click 'View' on any document                            ║"
echo "║  3. Modal should open with document preview                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
