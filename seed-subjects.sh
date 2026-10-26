#!/bin/bash

echo "========================================"
echo "  Seeding Kenyan Curriculum Subjects"
echo "========================================"
echo ""

cd "$(dirname "$0")"

echo "[1/2] Clearing config cache..."
php artisan config:clear

echo ""
echo "[2/2] Seeding subjects database..."
php artisan db:seed --class=KenyanCurriculumSeeder

echo ""
echo "========================================"
echo "  ✅ Done! Check output above"
echo "========================================"
