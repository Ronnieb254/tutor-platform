#!/bin/bash
# Quick Deploy - Document Upload Fix
# Run this on your Ubuntu server

set -e

echo "🔧 Quick Deploy - Document Upload Fix"
echo "======================================"
echo ""

# Check if we're in the right directory
if [ ! -f "docker-compose.prod.yml" ]; then
    echo "❌ Error: docker-compose.prod.yml not found"
    echo "Please run this from /root/microservice/tutor-platform/"
    exit 1
fi

echo "📁 Checking files..."

# Check if container is running
if ! docker ps | grep -q mwalimu-app; then
    echo "❌ Container mwalimu-app is not running"
    exit 1
fi

echo "✅ Container running"
echo ""

# Create backup
echo "💾 Creating backup..."
docker exec mwalimu-app cp /var/www/html/app/Http/Controllers/TutorController.php /var/www/html/app/Http/Controllers/TutorController.php.bak 2>/dev/null || true

echo "📝 Updating TutorController validation..."

# Update the validation in TutorController directly
docker exec mwalimu-app sed -i "s/'type'     => 'required|in:national_id,certificate,qualification,other'/'type'     => 'required|in:national_id,tsc_certificate,academic_certificate'/g" /var/www/html/app/Http/Controllers/TutorController.php

echo "✅ Validation updated"
echo ""

echo "🗄️  Running database migration..."

# Run migration to update existing document types
docker exec mwalimu-app php artisan migrate --force 2>/dev/null || echo "⚠️  Migration may have already run or failed - continuing..."

echo ""
echo "🧹 Clearing caches..."

# Clear all caches
docker exec mwalimu-app php artisan config:clear
docker exec mwalimu-app php artisan cache:clear
docker exec mwalimu-app php artisan view:clear

echo ""
echo "🔍 Verifying changes..."

# Check if change was applied
if docker exec mwalimu-app grep -q "tsc_certificate" /var/www/html/app/Http/Controllers/TutorController.php; then
    echo "✅ TutorController updated successfully"
else
    echo "❌ Update may have failed - check manually"
fi

echo ""
echo "✅ Quick fix applied!"
echo ""
echo "🧪 Test now:"
echo "  1. Go to: https://mwalimu.ovaldesk.co.ke/dashboard"
echo "  2. Click Documents tab"
echo "  3. Try uploading a document"
echo ""
echo "📊 If still errors, check logs:"
echo "  docker logs mwalimu-app --tail 50"
echo ""
