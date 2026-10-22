#!/bin/bash
# Quick Storage Fix - Run on Ubuntu Server
# This fixes the missing symlink without rebuilding containers

set -e

echo "🔍 Mwalimu Storage Quick Fix"
echo "============================"
echo ""

# Check if we're in the right directory
if [ ! -f "docker-compose.prod.yml" ]; then
    echo "❌ Error: docker-compose.prod.yml not found"
    echo "Please run this from /root/tutor-platform/"
    exit 1
fi

echo "📋 Current Status:"
echo ""

# Check if container is running
if docker ps | grep -q mwalimu-app; then
    echo "✅ Container mwalimu-app is running"
else
    echo "❌ Container mwalimu-app is not running"
    exit 1
fi

echo ""
echo "🔗 Checking storage symlink..."
if docker exec mwalimu-app test -L /var/www/html/public/storage; then
    echo "✅ Symlink exists"
    docker exec mwalimu-app ls -la /var/www/html/public/storage
else
    echo "❌ Symlink MISSING - This is the problem!"
fi

echo ""
echo "📁 Checking if uploaded files exist..."
docker exec mwalimu-app ls -la /var/www/html/storage/app/public/documents/ 2>/dev/null || echo "No documents directory found"

echo ""
echo "🔧 Applying Fix..."
echo ""

# Create storage directories if they don't exist
echo "1. Creating storage directories..."
docker exec mwalimu-app mkdir -p /var/www/html/storage/app/public/documents
echo "   ✅ Directories created"

# Remove old public/storage if it exists as directory
echo "2. Removing old storage link/directory..."
docker exec mwalimu-app rm -rf /var/www/html/public/storage
echo "   ✅ Removed"

# Create the symbolic link
echo "3. Creating symbolic link..."
docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage
echo "   ✅ Symlink created"

# Fix permissions
echo "4. Setting permissions..."
docker exec mwalimu-app chown -R www-data:www-data /var/www/html/storage
docker exec mwalimu-app chmod -R 775 /var/www/html/storage
echo "   ✅ Permissions set"

echo ""
echo "✅ FIX APPLIED!"
echo ""
echo "🔍 Verification:"
echo ""

# Verify symlink
echo "Storage symlink:"
docker exec mwalimu-app ls -la /var/www/html/public/ | grep storage

echo ""
echo "Storage permissions:"
docker exec mwalimu-app ls -la /var/www/html/storage/app/ | grep public

echo ""
echo "Uploaded files:"
docker exec mwalimu-app find /var/www/html/storage/app/public/documents/ -type f 2>/dev/null | head -10 || echo "No files yet"

echo ""
echo "🧪 Testing your specific file..."
TEST_FILE="/var/www/html/storage/app/public/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf"
if docker exec mwalimu-app test -f "$TEST_FILE"; then
    echo "✅ File exists at: $TEST_FILE"
    docker exec mwalimu-app ls -lh "$TEST_FILE"
    echo ""
    echo "📍 Check via symlink:"
    docker exec mwalimu-app ls -lh "/var/www/html/public/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf"
else
    echo "❌ File NOT found at: $TEST_FILE"
    echo ""
    echo "Available files in documents/29/:"
    docker exec mwalimu-app ls -la /var/www/html/storage/app/public/documents/29/ 2>/dev/null || echo "Directory doesn't exist"
fi

echo ""
echo "🌐 Now try accessing:"
echo "   https://mwalimu.ovaldesk.co.ke/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf"
echo ""
echo "If it still doesn't work:"
echo "  1. Check nginx logs: docker logs mwalimu-app | tail -20"
echo "  2. Check Laravel logs: docker exec mwalimu-app tail -20 /var/www/html/storage/logs/laravel.log"
echo "  3. Restart container: docker restart mwalimu-app"
echo ""
