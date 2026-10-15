# Mwalimu Setup Script
# Run this as Administrator in PowerShell

Write-Host "=== Mwalimu Setup ===" -ForegroundColor Cyan

# Step 1: Find Herd PHP and MySQL
Write-Host "`nSearching for PHP and MySQL..." -ForegroundColor Yellow

$phpPath = $null
$mysqlPath = $null

$searchPaths = @(
    "C:\Users\$env:USERNAME\AppData\Roaming\Herd\config\herd-lite\bin",
    "C:\Users\$env:USERNAME\.config\herd\config\herd-lite\bin",
    "C:\Program Files\Herd",
    "C:\Program Files\PHP",
    "C:\PHP"
)

foreach ($path in $searchPaths) {
    if (Test-Path "$path\php.exe") { $phpPath = $path; break }
}

# Broader search if not found
if (-not $phpPath) {
    $found = Get-ChildItem "C:\Users\$env:USERNAME" -Recurse -Filter "php.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) { $phpPath = $found.DirectoryName }
}

if (-not $phpPath) {
    $found = Get-ChildItem "C:\" -Recurse -Filter "php.exe" -ErrorAction SilentlyContinue | Select-Object -First 1 2>$null
    if ($found) { $phpPath = $found.DirectoryName }
}

if ($phpPath) {
    Write-Host "Found PHP at: $phpPath" -ForegroundColor Green
} else {
    Write-Host "PHP not found! Make sure Herd is running and has PHP installed." -ForegroundColor Red
    exit 1
}

# Find MySQL
$mysqlSearch = Get-ChildItem "C:\Users\$env:USERNAME" -Recurse -Filter "mysql.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
if ($mysqlSearch) { $mysqlPath = $mysqlSearch.DirectoryName }

if ($mysqlPath) {
    Write-Host "Found MySQL at: $mysqlPath" -ForegroundColor Green
} else {
    Write-Host "MySQL not found in user folder, searching Program Files..." -ForegroundColor Yellow
    $mysqlSearch = Get-ChildItem "C:\Program Files" -Recurse -Filter "mysql.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($mysqlSearch) { $mysqlPath = $mysqlSearch.DirectoryName }
}

# Step 2: Add to system PATH
Write-Host "`nAdding to PATH..." -ForegroundColor Yellow
$currentPath = [System.Environment]::GetEnvironmentVariable("PATH", "Machine")

$newPaths = @($phpPath)
if ($mysqlPath) { $newPaths += $mysqlPath }

foreach ($p in $newPaths) {
    if ($currentPath -notlike "*$p*") {
        $currentPath = $currentPath + ";" + $p
        Write-Host "Added: $p" -ForegroundColor Green
    } else {
        Write-Host "Already in PATH: $p" -ForegroundColor Gray
    }
}

[System.Environment]::SetEnvironmentVariable("PATH", $currentPath, "Machine")
$env:PATH = $currentPath
Write-Host "PATH updated!" -ForegroundColor Green

# Step 3: Verify
Write-Host "`nVerifying installations..." -ForegroundColor Yellow
& "$phpPath\php.exe" -v 2>&1 | Select-Object -First 1
Write-Host ""

# Step 4: Create database
Write-Host "Creating database..." -ForegroundColor Yellow
if ($mysqlPath) {
    & "$mysqlPath\mysql.exe" -u root --execute="CREATE DATABASE IF NOT EXISTS tutor_platform;" 2>&1
    Write-Host "Database created!" -ForegroundColor Green
} else {
    Write-Host "MySQL not found - will create DB via Laravel migrate" -ForegroundColor Yellow
}

# Step 5: Run composer install
$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectPath

Write-Host "`nRunning composer install..." -ForegroundColor Yellow
& "$phpPath\php.exe" "$(Get-Command composer -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source)" install --ignore-platform-reqs 2>&1

# Step 6: Generate app key
Write-Host "`nGenerating app key..." -ForegroundColor Yellow
& "$phpPath\php.exe" artisan key:generate

# Step 7: Run migrations
Write-Host "`nRunning migrations and seeders..." -ForegroundColor Yellow
& "$phpPath\php.exe" artisan migrate --seed --force

# Step 8: Storage link
Write-Host "`nCreating storage link..." -ForegroundColor Yellow
& "$phpPath\php.exe" artisan storage:link

Write-Host "`n=== Setup Complete! ===" -ForegroundColor Cyan
Write-Host "Run these in separate terminals:" -ForegroundColor White
Write-Host "  npm install && npm run dev" -ForegroundColor Yellow
Write-Host "  php artisan serve" -ForegroundColor Yellow
Write-Host "`nThen open: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Admin login: admin@Mwalimu.co.ke / password" -ForegroundColor Green
