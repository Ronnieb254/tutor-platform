# Fix Production Route Cache for Document Viewer Modal
# Usage: .\fix-production-routes.ps1 -ProductionDir "C:\path\to\laravel"
# Example: .\fix-production-routes.ps1 -ProductionDir "C:\var\www\html"

param(
    [string]$ProductionDir = "."
)

Write-Host "`n╔══════════════════════════════════════════════════════════════╗`n" -ForegroundColor Cyan
Write-Host "║  Fixing Production Route Cache for Document Viewer Modal    ║" -ForegroundColor Cyan
Write-Host "`n╚══════════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

# Validate directory
if (-not (Test-Path $ProductionDir)) {
    Write-Host "❌ Error: Directory '$ProductionDir' not found" -ForegroundColor Red
    exit 1
}

Set-Location $ProductionDir

# Validate artisan exists
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: artisan file not found. Are you in the Laravel root?" -ForegroundColor Red
    exit 1
}

Write-Host "📍 Working directory: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""

# Clear caches
Write-Host "🗑️  Clearing caches..." -ForegroundColor Magenta
Write-Host ""

try {
    $output = & php artisan cache:clear 2>&1
    Write-Host "   ✅ Application cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Cache clear warning: $_" -ForegroundColor Yellow
}

try {
    $output = & php artisan config:clear 2>&1
    Write-Host "   ✅ Configuration cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Config clear warning: $_" -ForegroundColor Yellow
}

try {
    $output = & php artisan route:clear 2>&1
    Write-Host "   ✅ Route cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Route clear warning: $_" -ForegroundColor Yellow
}

try {
    $output = & php artisan view:clear 2>&1
    Write-Host "   ✅ View cache cleared" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  View clear warning: $_" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🔨 Rebuilding caches..." -ForegroundColor Magenta
Write-Host ""

try {
    $output = & php artisan config:cache 2>&1
    Write-Host "   ✅ Configuration cached" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Config cache failed: $_" -ForegroundColor Red
}

try {
    $output = & php artisan route:cache 2>&1
    Write-Host "   ✅ Routes cached" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Route cache failed: $_" -ForegroundColor Red
}

try {
    $output = & php artisan view:cache 2>&1
    Write-Host "   ✅ Views cached" -ForegroundColor Green
} catch {
    Write-Host "   ❌ View cache failed: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "⚙️  Running optimization..." -ForegroundColor Magenta
Write-Host ""

try {
    $output = & php artisan optimize 2>&1
    Write-Host "   ✅ Optimization complete" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Optimization warning: $_" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  ✅ All done! Document viewer route should now work.        ║" -ForegroundColor Cyan
Write-Host "║                                                              ║" -ForegroundColor Cyan
Write-Host "║  Next steps:                                                 ║" -ForegroundColor Cyan
Write-Host "║  1. Test: https://mwalimu.ovaldesk.co.ke/admin/tutors      ║" -ForegroundColor Cyan
Write-Host "║  2. Click 'View' on any document                            ║" -ForegroundColor Cyan
Write-Host "║  3. Modal should open with document preview                 ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""
