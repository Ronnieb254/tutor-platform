Write-Host "=== TutorLink Diagnostic ===" -ForegroundColor Cyan

Write-Host "`n1. Laravel version:" -ForegroundColor Yellow
php artisan --version

Write-Host "`n2. APP_KEY set:" -ForegroundColor Yellow
Select-String "APP_KEY=base64" .env

Write-Host "`n3. Database:" -ForegroundColor Yellow
php artisan db:show 2>&1 | Select-Object -First 8

Write-Host "`n4. Routes loaded:" -ForegroundColor Yellow
php artisan route:list --columns=method,uri 2>&1 | Select-Object -First 15

Write-Host "`n5. Free ports:" -ForegroundColor Yellow
foreach ($p in @(8080, 8888, 3000, 5000, 7000)) {
    $t = Test-NetConnection -ComputerName localhost -Port $p -WarningAction SilentlyContinue -ErrorAction SilentlyContinue
    if (-not $t.TcpTestSucceeded) {
        Write-Host "  Port $p is FREE" -ForegroundColor Green
    } else {
        Write-Host "  Port $p is IN USE" -ForegroundColor Red
    }
}

Write-Host "`n6. Starting server on 0.0.0.0:8080..." -ForegroundColor Yellow
$job = Start-Job -ScriptBlock {
    Set-Location "C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform"
    php artisan serve --host=0.0.0.0 --port=8080 2>&1
}
Start-Sleep -Seconds 4
$output = Receive-Job $job
Write-Host $output

Write-Host "`n7. Testing HTTP response:" -ForegroundColor Yellow
try {
    $r = Invoke-WebRequest -Uri "http://127.0.0.1:8080" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "  SUCCESS - Status: $($r.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  FAILED: $_" -ForegroundColor Red
}

try {
    $r2 = Invoke-WebRequest -Uri "http://localhost:8080" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "  localhost SUCCESS - Status: $($r2.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "  localhost FAILED: $_" -ForegroundColor Red
}

Write-Host "`nDone." -ForegroundColor Cyan
Stop-Job $job
Remove-Job $job
