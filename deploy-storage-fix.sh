#!/bin/bash
# Deploy Storage Fix to Production Server
# Usage: ./deploy-storage-fix.sh

set -e

echo "🔧 Mwalimu Storage Fix Deployment Script"
echo "=========================================="
echo ""

# Make entrypoint executable locally first
chmod +x docker/entrypoint.sh

echo "✅ Entrypoint made executable"
echo ""
echo "📋 Files to upload:"
echo "  - docker/entrypoint.sh"
echo "  - docker-compose.prod.yml"
echo ""
echo "⚠️  This script will:"
echo "  1. Stop running containers"
echo "  2. Rebuild the app container"
echo "  3. Start all services with storage fix"
echo "  4. Run migrations"
echo "  5. Create storage symlink"
echo ""
echo "Expected downtime: ~2-3 minutes"
echo ""

read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled"
    exit 1
fi

echo ""
echo "🚀 Starting deployment..."
echo ""

# Detect if we're on the server or local
if [ -d "/root/tutor-platform" ]; then
    # Already on server
    echo "ℹ️  Running on production server"
    PROJECT_DIR="/root/tutor-platform"
else
    # Running locally - need server details
    echo "ℹ️  Running from local machine"
    echo ""
    read -p "Enter server SSH address (e.g., root@your-server.com): " SERVER_ADDRESS
    
    if [ -z "$SERVER_ADDRESS" ]; then
        echo "❌ Server address required"
        exit 1
    fi
    
    echo ""
    echo "📤 Uploading files to server..."
    
    # Upload specific files
    scp docker/entrypoint.sh "$SERVER_ADDRESS:/root/tutor-platform/docker/"
    scp docker-compose.prod.yml "$SERVER_ADDRESS:/root/tutor-platform/"
    
    echo "✅ Files uploaded"
    echo ""
    echo "🔗 Connecting to server to complete deployment..."
    echo ""
    
    # Execute rest of script on remote server
    ssh -t "$SERVER_ADDRESS" "cd /root/tutor-platform && bash" << 'ENDSSH'
#!/bin/bash
set -e

echo "📍 On production server: $(hostname)"
echo ""

# Make entrypoint executable
chmod +x docker/entrypoint.sh

echo "🛑 Stopping containers..."
docker compose -f docker-compose.prod.yml down

echo "🗑️  Removing old app image..."
docker rmi tutor-platform-app 2>/dev/null || true

echo "🔨 Rebuilding app container..."
docker compose -f docker-compose.prod.yml build --no-cache app

echo "🚀 Starting all services..."
docker compose -f docker-compose.prod.yml up -d

echo "⏳ Waiting for containers to be healthy (30s)..."
sleep 30

echo "🔗 Verifying storage symlink..."
if docker exec mwalimu-app test -L /var/www/html/public/storage; then
    echo "✅ Storage symlink exists"
    docker exec mwalimu-app ls -la /var/www/html/public/storage
else
    echo "❌ Storage symlink missing - attempting to create..."
    docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage
fi

echo ""
echo "📁 Verifying storage permissions..."
docker exec mwalimu-app ls -la /var/www/html/storage/app/

echo ""
echo "🐳 Verifying storage volume..."
docker volume ls | grep storage

echo ""
echo "✅ Deployment complete!"
echo ""
echo "🧪 Next steps:"
echo "  1. Visit: https://mwalimu.ovaldesk.co.ke/dashboard"
echo "  2. Login as tutor"
echo "  3. Go to Documents tab"
echo "  4. Upload a test document"
echo "  5. Verify it appears and is accessible"
echo ""
echo "📊 View logs: docker logs mwalimu-app"
echo "🔍 Check app: docker exec -it mwalimu-app bash"
echo ""
ENDSSH
    
    exit 0
fi

# If we're already on the server, execute directly
echo "📍 On production server: $(hostname)"
echo ""

cd "$PROJECT_DIR"

echo "🛑 Stopping containers..."
docker compose -f docker-compose.prod.yml down

echo "🗑️  Removing old app image..."
docker rmi tutor-platform-app 2>/dev/null || true

echo "🔨 Rebuilding app container..."
docker compose -f docker-compose.prod.yml build --no-cache app

echo "🚀 Starting all services..."
docker compose -f docker-compose.prod.yml up -d

echo "⏳ Waiting for containers to be healthy (30s)..."
sleep 30

echo "🔗 Verifying storage symlink..."
if docker exec mwalimu-app test -L /var/www/html/public/storage; then
    echo "✅ Storage symlink exists"
    docker exec mwalimu-app ls -la /var/www/html/public/storage
else
    echo "❌ Storage symlink missing - attempting to create..."
    docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage
fi

echo ""
echo "📁 Verifying storage permissions..."
docker exec mwalimu-app ls -la /var/www/html/storage/app/

echo ""
echo "🐳 Verifying storage volume..."
docker volume ls | grep storage

echo ""
echo "✅ Deployment complete!"
echo ""
echo "🧪 Next steps:"
echo "  1. Visit: https://mwalimu.ovaldesk.co.ke/dashboard"
echo "  2. Login as tutor"
echo "  3. Go to Documents tab"
echo "  4. Upload a test document"
echo "  5. Verify it appears and is accessible"
echo ""
echo "📊 View logs: docker logs mwalimu-app"
echo "🔍 Check app: docker exec -it mwalimu-app bash"
echo ""
