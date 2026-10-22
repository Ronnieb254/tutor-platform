#!/bin/bash
# Complete System Check for Document Upload System
# Run this on your Ubuntu server

set -e

echo "🔍 Mwalimu Document Upload System Check"
echo "========================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "docker-compose.prod.yml" ]; then
    echo -e "${RED}❌ Error: docker-compose.prod.yml not found${NC}"
    echo "Please run this from /root/microservice/tutor-platform/"
    exit 1
fi

echo "📍 Working directory: $(pwd)"
echo ""

# Check if container is running
echo "🐳 Checking Docker container..."
if docker ps | grep -q mwalimu-app; then
    echo -e "${GREEN}✅ Container mwalimu-app is running${NC}"
else
    echo -e "${RED}❌ Container mwalimu-app is not running${NC}"
    exit 1
fi
echo ""

# Check storage symlink
echo "🔗 Checking storage symlink..."
if docker exec mwalimu-app test -L /var/www/html/public/storage; then
    echo -e "${GREEN}✅ Storage symlink exists${NC}"
    docker exec mwalimu-app ls -la /var/www/html/public/storage
else
    echo -e "${RED}❌ Storage symlink missing${NC}"
    echo "Run: docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage"
fi
echo ""

# Check storage permissions
echo "📁 Checking storage permissions..."
docker exec mwalimu-app ls -la /var/www/html/storage/app/ | grep public || echo -e "${YELLOW}⚠️  Public storage directory may not exist${NC}"
echo ""

# Check TutorController validation
echo "🎛️  Checking TutorController.php validation..."
if docker exec mwalimu-app grep -q "tsc_certificate" /var/www/html/app/Http/Controllers/TutorController.php; then
    echo -e "${GREEN}✅ TutorController has new document types${NC}"
    docker exec mwalimu-app grep "in:national_id" /var/www/html/app/Http/Controllers/TutorController.php || echo -e "${YELLOW}⚠️  Validation rule not found${NC}"
else
    echo -e "${RED}❌ TutorController still uses old document types${NC}"
    echo "Expected: national_id, tsc_certificate, academic_certificate"
fi
echo ""

# Check Livewire component
echo "⚡ Checking Livewire TutorOnboarding component..."
if docker exec mwalimu-app test -f /var/www/html/app/Livewire/Tutor/TutorOnboarding.php; then
    if docker exec mwalimu-app grep -q "tscCertificate" /var/www/html/app/Livewire/Tutor/TutorOnboarding.php; then
        echo -e "${GREEN}✅ Livewire component has new property names${NC}"
    else
        echo -e "${YELLOW}⚠️  Livewire component may have old property names${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Livewire component not found (may not be used)${NC}"
fi
echo ""

# Check database migration
echo "🗄️  Checking database migrations..."
if docker exec mwalimu-app test -f /var/www/html/database/migrations/2026_10_16_000001_update_tutor_documents_types.php; then
    echo -e "${GREEN}✅ Document types migration file exists${NC}"
    
    # Check if migration has run
    if docker exec mwalimu-app php artisan migrate:status | grep -q "update_tutor_documents_types"; then
        echo -e "${GREEN}✅ Migration has been executed${NC}"
    else
        echo -e "${YELLOW}⚠️  Migration exists but may not have run${NC}"
        echo "Run: docker exec mwalimu-app php artisan migrate --force"
    fi
else
    echo -e "${YELLOW}⚠️  Migration file not uploaded yet${NC}"
fi
echo ""

# Check database for document types
echo "💾 Checking database document types..."
EXISTING_DOCS=$(docker exec mwalimu-app php artisan tinker --execute="echo App\Models\TutorDocument::pluck('type')->unique()->toJson();" 2>/dev/null || echo "[]")
echo "Document types in database: $EXISTING_DOCS"
if echo "$EXISTING_DOCS" | grep -q "tsc_certificate"; then
    echo -e "${GREEN}✅ New document types found in database${NC}"
else
    echo -e "${YELLOW}⚠️  Old document types in database (migration may not have run)${NC}"
fi
echo ""

# Check tutor dashboard blade
echo "🎨 Checking tutor dashboard view..."
if docker exec mwalimu-app grep -q "TSC Certificate" /var/www/html/resources/views/pages/dashboard/tutor.blade.php; then
    echo -e "${GREEN}✅ Dashboard has new document labels${NC}"
else
    echo -e "${YELLOW}⚠️  Dashboard may have old labels${NC}"
fi

if docker exec mwalimu-app grep -q "/3 docs uploaded" /var/www/html/resources/views/pages/dashboard/tutor.blade.php; then
    echo -e "${GREEN}✅ Dashboard shows 3 document counter${NC}"
else
    echo -e "${YELLOW}⚠️  Dashboard may show wrong document counter${NC}"
fi
echo ""

# Check admin tutors blade
echo "👤 Checking admin tutors view..."
if docker exec mwalimu-app test -f /var/www/html/resources/views/pages/admin/tutors.blade.php; then
    if docker exec mwalimu-app grep -q "/3 docs uploaded" /var/www/html/resources/views/pages/admin/tutors.blade.php; then
        echo -e "${GREEN}✅ Admin view shows 3 document counter${NC}"
    else
        echo -e "${YELLOW}⚠️  Admin view may show wrong counter${NC}"
    fi
    
    if docker exec mwalimu-app grep -q "tsc_certificate" /var/www/html/resources/views/pages/admin/tutors.blade.php; then
        echo -e "${GREEN}✅ Admin view has new document type labels${NC}"
    else
        echo -e "${YELLOW}⚠️  Admin view may have old labels${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Admin tutors view not found${NC}"
fi
echo ""

# Check auth onboarding
echo "🔐 Checking auth tutor-onboarding..."
if docker exec mwalimu-app test -f /var/www/html/resources/views/pages/auth/tutor-onboarding.blade.php; then
    DOC_COUNT=$(docker exec mwalimu-app grep -c "upload-zone" /var/www/html/resources/views/pages/auth/tutor-onboarding.blade.php || echo "0")
    echo "Number of upload zones found: $DOC_COUNT"
    
    if [ "$DOC_COUNT" -eq 3 ]; then
        echo -e "${GREEN}✅ Auth onboarding has exactly 3 upload zones${NC}"
    elif [ "$DOC_COUNT" -eq 5 ]; then
        echo -e "${YELLOW}⚠️  Auth onboarding still has 5 upload zones (needs cleanup)${NC}"
    else
        echo -e "${YELLOW}⚠️  Unexpected number of upload zones: $DOC_COUNT${NC}"
    fi
    
    # Check JavaScript validation
    if docker exec mwalimu-app grep -q "academic_certificate" /var/www/html/resources/views/pages/auth/tutor-onboarding.blade.php; then
        echo -e "${GREEN}✅ Auth onboarding JavaScript has new document types${NC}"
    else
        echo -e "${YELLOW}⚠️  Auth onboarding may have old document types in JS${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Auth onboarding view not found${NC}"
fi
echo ""

# Check for any uploaded documents
echo "📄 Checking existing uploaded documents..."
DOC_COUNT=$(docker exec mwalimu-app php artisan tinker --execute="echo App\Models\TutorDocument::count();" 2>/dev/null || echo "0")
echo "Total documents in database: $DOC_COUNT"

if [ "$DOC_COUNT" -gt 0 ]; then
    echo "Sample document types:"
    docker exec mwalimu-app php artisan tinker --execute="App\Models\TutorDocument::select('type', DB::raw('count(*) as count'))->groupBy('type')->get()->each(function(\$d) { echo \$d->type . ': ' . \$d->count . PHP_EOL; });" 2>/dev/null || echo "Could not fetch document types"
fi
echo ""

# Check file storage
echo "💾 Checking file storage..."
STORAGE_FILES=$(docker exec mwalimu-app find /var/www/html/storage/app/public/documents -type f 2>/dev/null | wc -l || echo "0")
echo "Files in storage/app/public/documents: $STORAGE_FILES"
echo ""

# Check Laravel logs for recent errors
echo "📋 Checking recent Laravel logs..."
echo "Last 10 lines of Laravel log:"
docker exec mwalimu-app tail -10 /var/www/html/storage/logs/laravel.log 2>/dev/null || echo -e "${YELLOW}⚠️  Could not read Laravel log${NC}"
echo ""

# Cache status
echo "🗑️  Checking cache status..."
echo "Clearing all caches..."
docker exec mwalimu-app php artisan config:clear > /dev/null 2>&1 && echo -e "${GREEN}✅ Config cache cleared${NC}" || echo -e "${RED}❌ Config cache failed${NC}"
docker exec mwalimu-app php artisan cache:clear > /dev/null 2>&1 && echo -e "${GREEN}✅ Application cache cleared${NC}" || echo -e "${RED}❌ Cache clear failed${NC}"
docker exec mwalimu-app php artisan view:clear > /dev/null 2>&1 && echo -e "${GREEN}✅ View cache cleared${NC}" || echo -e "${RED}❌ View cache failed${NC}"
echo ""

# Route check
echo "🛤️  Checking document upload routes..."
if docker exec mwalimu-app php artisan route:list | grep -q "tutor/documents/upload"; then
    echo -e "${GREEN}✅ Document upload route exists${NC}"
    docker exec mwalimu-app php artisan route:list | grep "documents/upload"
else
    echo -e "${YELLOW}⚠️  Document upload route not found${NC}"
fi
echo ""

# Final summary
echo "════════════════════════════════════════"
echo "📊 SYSTEM CHECK SUMMARY"
echo "════════════════════════════════════════"
echo ""

# Count issues
ISSUES=0

docker exec mwalimu-app test -L /var/www/html/public/storage || ((ISSUES++))
docker exec mwalimu-app grep -q "tsc_certificate" /var/www/html/app/Http/Controllers/TutorController.php || ((ISSUES++))
docker exec mwalimu-app grep -q "/3 docs uploaded" /var/www/html/resources/views/pages/dashboard/tutor.blade.php 2>/dev/null || ((ISSUES++))

if [ $ISSUES -eq 0 ]; then
    echo -e "${GREEN}✅ All critical checks passed!${NC}"
    echo ""
    echo "System appears to be properly configured for 3-document upload."
    echo ""
    echo "🧪 Next steps:"
    echo "  1. Test document upload at: https://mwalimu.ovaldesk.co.ke/dashboard"
    echo "  2. Test tutor onboarding at: https://mwalimu.ovaldesk.co.ke/tutor/onboard"
    echo "  3. Check admin panel at: https://mwalimu.ovaldesk.co.ke/admin/tutors"
else
    echo -e "${YELLOW}⚠️  Found $ISSUES potential issue(s)${NC}"
    echo ""
    echo "Review the warnings above and fix any RED or YELLOW items."
    echo ""
    echo "Common fixes:"
    echo "  • Missing symlink: docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage"
    echo "  • Old validation: Upload updated TutorController.php"
    echo "  • Migration not run: docker exec mwalimu-app php artisan migrate --force"
fi

echo ""
echo "════════════════════════════════════════"
echo ""
