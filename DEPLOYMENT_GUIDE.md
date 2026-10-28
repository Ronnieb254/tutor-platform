# Production Deployment Guide - Parent Dashboard Fix

## Problem
Parent dashboard at `https://mwalimu.ovaldesk.co.ke/dashboard` returns 500 Server Error.

## Root Cause
The Blade view was executing database queries inline using `@php` blocks, which can fail silently in production and cause 500 errors when:
- Database relationships are missing
- Tables/columns don't exist
- View cache is corrupted

## Solution Implemented
Created a dedicated `StudentDashboardController` that:
1. Pre-loads all data with proper error handling
2. Validates required relationships exist
3. Logs detailed errors for debugging
4. Shows a friendly error page to users when issues occur
5. Passes data explicitly to the view (no inline queries)

## Files Changed

### 1. New Controller
- `app/Http/Controllers/StudentDashboardController.php` ✅

### 2. Updated Route
- `routes/web.php` - Changed from closure to controller method ✅

### 3. Updated View
- `resources/views/pages/dashboard/student.blade.php` - Removed inline `@php` queries ✅

### 4. New Error Page
- `resources/views/errors/dashboard-error.blade.php` - Friendly error page ✅

## Deployment Steps

### Option A: Using Git (Recommended)

```bash
# 1. Commit changes locally
git add .
git commit -m "Fix parent dashboard 500 error - add dedicated controller with error handling"

# 2. Push to your repository
git push origin main  # or your branch name

# 3. SSH into production server
ssh user@mwalimu.ovaldesk.co.ke

# 4. Navigate to application directory
cd /var/www/mwalimu  # adjust path as needed

# 5. Pull latest changes
git pull origin main

# 6. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Test the dashboard
curl -I https://mwalimu.ovaldesk.co.ke/dashboard
```

### Option B: Using FTP/SFTP

1. Upload these files to production:
   - `app/Http/Controllers/StudentDashboardController.php`
   - `routes/web.php`
   - `resources/views/pages/dashboard/student.blade.php`
   - `resources/views/errors/dashboard-error.blade.php`

2. SSH into server and run:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Verification

After deployment, test these scenarios:

1. **As Parent User:**
   - Visit `https://mwalimu.ovaldesk.co.ke/dashboard`
   - Should load without 500 error
   - Should show bookings, stats, payment history

2. **As Student User:**
   - Same URL should work
   - Shows relevant student data

3. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   - Any errors now logged with detailed context
   - Error code: "Student Dashboard Error"

## If Problems Persist

### Check Database Tables
```bash
php artisan tinker
>>> Schema::hasTable('payments');
>>> Schema::hasTable('bookings');
>>> Schema::hasTable('wishlists');
>>> Schema::hasTable('child_profiles');
```

### Check User Relationships
```bash
php artisan tinker
>>> $user = User::find(1);  // replace 1 with parent user ID
>>> $user->bookingsAsStudent;
>>> $user->payments;
>>> $user->wishlist;
```

### Enable Debug Mode (Temporarily)
In `.env`:
```
APP_DEBUG=true
```
Then visit dashboard to see full stack trace.

**Remember to set `APP_DEBUG=false` after debugging!**

## Monitoring

The new controller logs errors to `storage/logs/laravel.log` with:
- User ID
- Full error message
- File and line number
- Stack trace

Search logs with:
```bash
grep "Student Dashboard Error" storage/logs/laravel.log
```

## Rollback Plan

If the fix causes issues:

```bash
# Revert to previous version
git revert HEAD
git push origin main

# On production
git pull origin main
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Contact

If issues persist after deployment:
1. Check `storage/logs/laravel.log` for "Student Dashboard Error"
2. Verify all migrations have run: `php artisan migrate:status`
3. Check database connection: `php artisan tinker` → `DB::connection()->getPdo();`
