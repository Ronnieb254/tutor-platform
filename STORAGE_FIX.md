# Storage Fix - File Upload Not Working

## Problem
Files uploaded via tutor document upload were not being saved/accessible because:
1. ❌ Missing `docker/entrypoint.sh` script
2. ❌ No symbolic link from `public/storage` → `storage/app/public`
3. ❌ No persistent Docker volume for `storage/app/public`
4. ❌ Files stored in container-local storage (lost on restart)

## Solution Applied

### 1. Created `docker/entrypoint.sh`
- Waits for MySQL to be ready
- Creates all necessary storage directories
- **Creates symbolic link**: `ln -sf ../storage/app/public public/storage`
- Sets proper permissions (www-data:www-data)
- Runs migrations and cache optimization
- Starts supervisor (nginx + php-fpm)

### 2. Updated `docker-compose.prod.yml`
- Added `storage-data` named volume
- Mounted to all containers that need access:
  - `app` container: `/var/www/html/storage/app/public`
  - `queue` container: `/var/www/html/storage/app/public`
  - `scheduler` container: `/var/www/html/storage/app/public`

### 3. How It Works Now
```
User uploads document
    ↓
TutorController->uploadDocument()
    ↓
Stores to: storage/app/public/documents/{user_id}/filename.pdf
    ↓
Saved in Docker volume: storage-data (persists across restarts)
    ↓
Accessible via web: https://mwalimu.ovaldesk.co.ke/storage/documents/{user_id}/filename.pdf
    ↓
Blade views use: asset('storage/'.$doc->file_path)
```

## Deployment Steps

### On Ubuntu Server:

```bash
# 1. Upload the fixed files
cd /root
rsync -avz --exclude='vendor' --exclude='node_modules' \
  /path/to/local/tutor-platform/ /root/tutor-platform/

# Or use scp for specific files:
scp docker/entrypoint.sh root@your-server:/root/tutor-platform/docker/
scp docker-compose.prod.yml root@your-server:/root/tutor-platform/

# 2. SSH into server
ssh root@your-server

# 3. Navigate to project
cd /root/tutor-platform

# 4. Make entrypoint executable
chmod +x docker/entrypoint.sh

# 5. Stop and remove existing containers
docker compose -f docker-compose.prod.yml down

# 6. Remove old app image (force rebuild)
docker rmi mwalimu-app 2>/dev/null || true

# 7. Rebuild and start all services
docker compose -f docker-compose.prod.yml build --no-cache app
docker compose -f docker-compose.prod.yml up -d

# 8. Verify storage link was created
docker exec mwalimu-app ls -la /var/www/html/public/ | grep storage

# Expected output:
# lrwxrwxrwx 1 www-data www-data   23 Oct 16 14:30 storage -> ../storage/app/public

# 9. Check storage permissions
docker exec mwalimu-app ls -la /var/www/html/storage/app/

# Expected: drwxrwxr-x www-data www-data public

# 10. Test file upload
# Go to: https://mwalimu.ovaldesk.co.ke/dashboard
# Login as tutor → Documents tab → Upload a file
# Verify file appears and is accessible

# 11. Check logs if issues
docker logs mwalimu-app
docker logs mwalimu-queue
```

## Verification Checklist

- [ ] Entrypoint script exists and is executable
- [ ] Symbolic link created: `public/storage` → `../storage/app/public`
- [ ] Storage directory has correct permissions (775, www-data:www-data)
- [ ] Docker volume `storage-data` is created and mounted
- [ ] File upload works in UI
- [ ] Uploaded files are visible in admin panel
- [ ] Files persist after container restart
- [ ] Files are accessible via browser (not 404)

## Testing

```bash
# Test upload from command line inside container
docker exec -it mwalimu-app bash

# Create a test file
echo "Test document" > /var/www/html/storage/app/public/documents/test.txt

# Check if accessible via public/storage
ls -la /var/www/html/public/storage/documents/test.txt

# Test via curl
curl -I https://mwalimu.ovaldesk.co.ke/storage/documents/test.txt
# Should return 200 OK, not 404

# Clean up test
rm /var/www/html/storage/app/public/documents/test.txt
```

## Troubleshooting

### Issue: Files upload but return 404
```bash
# Check if symlink exists
docker exec mwalimu-app ls -la /var/www/html/public/storage

# If not, recreate it
docker exec mwalimu-app ln -sf ../storage/app/public /var/www/html/public/storage
```

### Issue: Permission denied when uploading
```bash
# Fix permissions
docker exec mwalimu-app chown -R www-data:www-data /var/www/html/storage
docker exec mwalimu-app chmod -R 775 /var/www/html/storage
```

### Issue: Files lost after restart
```bash
# Check if volume is mounted
docker volume ls | grep storage
docker inspect mwalimu-app | grep storage-data

# Should see: storage-data:/var/www/html/storage/app/public
```

## File Locations

- **Uploaded files stored in**: `/var/www/html/storage/app/public/documents/{user_id}/`
- **Docker volume name**: `storage-data`
- **Web accessible via**: `/storage/documents/{user_id}/filename.pdf`
- **Database path stored**: `documents/{user_id}/filename.pdf` (relative to storage/app/public)

## Related Files Modified

1. ✅ `docker/entrypoint.sh` - Created with storage setup
2. ✅ `docker-compose.prod.yml` - Added storage-data volume
3. ✅ `Dockerfile` - Already copies entrypoint (line existed)
4. ✅ `app/Http/Controllers/TutorController.php` - Already correct (uses 'public' disk)

---

**Status**: Ready to deploy
**Priority**: HIGH - Blocks tutor verification workflow
**Estimated Deploy Time**: 5-10 minutes (includes rebuild)
