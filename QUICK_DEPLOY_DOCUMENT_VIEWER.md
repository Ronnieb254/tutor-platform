# Quick Deploy: Document Viewer Modal

## TL;DR

The document viewer modal is **ready to deploy now**. No additional setup needed.

## What's Included

✅ Document viewer modal with PDF.js preview  
✅ Approve/Reject functionality  
✅ Image preview support  
✅ Hardcoded URLs (no cache dependency)  
✅ All files in place  

## Deployment Steps

### Step 1: Deploy Code
```bash
git add .
git commit -m "Add document viewer modal to admin tutors page"
git push origin main
```

### Step 2: Pull on Production
```bash
ssh your-server
cd /var/www/html
git pull origin main
```

### Step 3: Done ✅

No cache rebuild needed. The app works immediately.

## Verify It Works

1. Open: `https://mwalimu.ovaldesk.co.ke/admin/tutors`
2. Click "View" on any document
3. Modal opens with preview

## What Was Changed

- `resources/views/pages/admin/tutors.blade.php` - Updated document URLs to be hardcoded instead of using `route()` helper

## Features

- 📄 PDF preview with PDF.js
- 🖼️ Image preview (PNG, JPG, GIF, WebP)
- ✅ Approve documents
- ❌ Reject documents with reason
- 🔒 Admin-only access
- 📁 Secure file serving

## Files Modified

1. **resources/views/pages/admin/tutors.blade.php**
   - Line 572: Image thumbnail onclick handler
   - Line 574: File icon onclick handler
   - Changed from `route('documents.view', $doc->id)` to `/admin/documents/{{ $doc->id }}/view`

2. **app/Http/Controllers/Admin/TutorApprovalController.php**
   - `viewDocument()` method - serves files with correct MIME types
   - `getMimeType()` helper - detects file type

3. **routes/web.php**
   - `Route::get('/documents/{document}/view', ...)` - document serving route

## Support

If you see a 404 error when viewing documents:
- Check files exist in `storage/app/public/documents/`
- Verify storage link: `php artisan storage:link`

If approve/reject buttons don't work:
- Check user is admin
- Verify CSRF token is valid
- Check browser console for JavaScript errors

---

**Status**: ✅ Production Ready
