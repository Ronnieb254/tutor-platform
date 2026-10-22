# Quick Fix Commands - Run These NOW on Ubuntu Server

## Problem
File uploaded successfully but returns 404: 
`https://mwalimu.ovaldesk.co.ke/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf`

**Root Cause**: Missing symbolic link `public/storage` → `storage/app/public`

---

## INSTANT FIX (Copy & Paste - No Downtime)

```bash
# SSH into your server first:
ssh root@your-server

# Then run this single command:
docker exec mwalimu-app bash -c "rm -rf /var/www/html/public/storage && ln -sf ../storage/app/public /var/www/html/public/storage && chown -R www-data:www-data /var/www/html/storage && chmod -R 775 /var/www/html/storage && ls -la /var/www/html/public/storage"
```

**That's it!** ✅ Your file should now be accessible.

---

## Verify It Worked

```bash
# Check if file exists via symlink
docker exec mwalimu-app ls -lh /var/www/html/public/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf

# Should show file size and date - if it does, SUCCESS!
```

Then test in browser: https://mwalimu.ovaldesk.co.ke/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf

---

## Alternative: Use the Fix Script

If you prefer a more detailed approach:

```bash
# Upload the fix script to server
scp fix-storage-now.sh root@your-server:/root/tutor-platform/

# SSH into server
ssh root@your-server

# Navigate to project
cd /root/tutor-platform

# Make executable and run
chmod +x fix-storage-now.sh
./fix-storage-now.sh
```

---

## What This Does

1. **Removes** any existing `public/storage` directory/link
2. **Creates** symbolic link: `public/storage` → `../storage/app/public`
3. **Sets** ownership to `www-data:www-data` (PHP/nginx user)
4. **Sets** permissions to `775` (read/write/execute for owner & group)

---

## Why This Happened

The file was uploaded successfully to:
- **Physical location**: `/var/www/html/storage/app/public/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf` ✅

But Laravel needs a symlink to access it via web:
- **Symlink needed**: `/var/www/html/public/storage` → `../storage/app/public` ❌ (was missing)
- **Web URL**: `/storage/documents/29/...pdf` (uses the symlink)

Without the symlink, nginx looks for:
- `/var/www/html/public/storage/documents/29/...pdf` ← doesn't exist
- Instead of: `/var/www/html/storage/app/public/documents/29/...pdf` ← actual file

---

## Permanent Fix (Prevents Future Issues)

After the quick fix above works, deploy the permanent solution to prevent this on container restart:

```bash
# Upload the new files
scp docker/entrypoint.sh root@your-server:/root/tutor-platform/docker/
scp docker-compose.prod.yml root@your-server:/root/tutor-platform/

# SSH into server
ssh root@your-server
cd /root/tutor-platform

# Make entrypoint executable
chmod +x docker/entrypoint.sh

# Rebuild (do this during low traffic time)
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml build --no-cache app
docker compose -f docker-compose.prod.yml up -d

# Wait 30 seconds
sleep 30

# Verify everything works
docker exec mwalimu-app ls -la /var/www/html/public/storage
```

---

## Troubleshooting

### Still getting 404?

```bash
# Check nginx error logs
docker logs mwalimu-app 2>&1 | grep -i "404\|error\|storage" | tail -20

# Check if nginx is serving the right directory
docker exec mwalimu-app cat /etc/nginx/sites-available/default | grep root
# Should show: root /var/www/html/public;

# Restart nginx (inside container)
docker exec mwalimu-app nginx -s reload
```

### File doesn't exist?

```bash
# List all uploaded files
docker exec mwalimu-app find /var/www/html/storage/app/public/documents -name "*.pdf" -type f

# Check the specific user's directory
docker exec mwalimu-app ls -la /var/www/html/storage/app/public/documents/29/
```

### Permission denied?

```bash
# Fix all storage permissions
docker exec mwalimu-app chown -R www-data:www-data /var/www/html/storage
docker exec mwalimu-app chmod -R 775 /var/www/html/storage
```

---

## Expected Output After Fix

```bash
$ docker exec mwalimu-app ls -la /var/www/html/public/storage
lrwxrwxrwx 1 www-data www-data 23 Oct 16 15:30 storage -> ../storage/app/public

$ docker exec mwalimu-app ls -lh /var/www/html/public/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf
-rw-r--r-- 1 www-data www-data 1.2M Oct 16 15:28 /var/www/html/public/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf
```

Then your URL should work: ✅ https://mwalimu.ovaldesk.co.ke/storage/documents/29/jDUZ7c1sNWOTgt21sOcosmabMkL2QqzPHUmd8c9I.pdf

---

**Run the one-liner command at the top and test immediately!** 🚀
