# Production Deployment Fix - Document Viewer Route

## Issue
The admin tutors page is throwing a `RouteNotFoundException: Route [documents.view] not defined` error on production.

## Root Cause
The production server's route cache is stale and doesn't include the new `documents.view` route that was added to `routes/web.php`.

## Solution

Run the following commands on your **production server** (`/var/www/html`):

```bash
cd /var/www/html

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optional: Run optimization
php artisan optimize
```

## If using Docker/Containers
If the application is running in a Docker container, run these commands inside the container:

```bash
docker exec <container_name> php artisan cache:clear
docker exec <container_name> php artisan route:cache
```

## Verification
After running the cache commands, try accessing the admin tutors page at:
```
https://mwalimu.ovaldesk.co.ke/admin/tutors
```

The document viewer modal should now work correctly when you click the "View" button on any document.

## Route Definition
The route is defined in `routes/web.php` (lines ~344):

```php
Route::get('/documents/{document}/view', [App\Http\Controllers\Admin\TutorApprovalController::class, 'viewDocument'])->name('documents.view');
```

## Controller Method
The controller method `viewDocument()` in `app/Http/Controllers/Admin/TutorApprovalController.php` handles:
- Serving files from storage
- Setting correct MIME types (PDF, PNG, JPG, etc.)
- Inline display vs download behavior

All required files are already in place; you just need to rebuild the route cache.
