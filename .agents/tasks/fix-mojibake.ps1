# PowerShell script to fix mojibake in blade templates
$ErrorActionPreference = "Stop"
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# Define replacements (mojibake -> correct character)
$replacements = @{
    'â‚¬"' = '—'    # em-dash
    'â‚¬Â¦' = '…'    # ellipsis
    'Ã—' = '×'      # multiplication sign
    'â"€' = '─'     # box-drawing horizontal
    'â‚¹' = '₹'     # rupee sign
    'Â°' = '°'      # degree symbol
    'Â§' = '§'      # section sign
    'â¢' = '•'      # bullet
    'ðŸ"Ž' = '🔎'   # magnifying glass emoji
    'ðŸ–¼ï¸' = '🖼️'  # framed picture emoji
    'ðŸ'¡' = '💡'   # light bulb emoji
    'Â²' = '²'      # superscript 2
    'Â³' = '³'      # superscript 3
    'Â¹' = '¹'      # superscript 1
    'ðŸ'¥' = '💥'   # collision emoji
    'âš¡' = '⚡'    # high voltage emoji
    'ðŸŽ¯' = '🎯'   # dart emoji
    'ðŸ†' = '🏆'    # trophy emoji
    'â˜…' = '★'     # star filled
    'âœ—' = '✗'     # ballot x
    'âœ"' = '✓'     # check mark
    '¸'Â³' = '💳'   # credit card emoji
}

# Statistics
$stats = @{
    filesScanned = 0
    filesChanged = 0
    totalReplacements = 0
    patternCounts = @{}
    errors = @()
}

# Initialize pattern counts
foreach ($pattern in $replacements.Keys) {
    $stats.patternCounts[$pattern] = 0
}

# Find all blade.php files
$viewsPath = Join-Path $PSScriptRoot "..\..\" "resources\views"
$bladeFiles = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse -File

Write-Host "Found $($bladeFiles.Count) .blade.php files`n" -ForegroundColor Cyan

# Process each file
foreach ($file in $bladeFiles) {
    try {
        # Read file as UTF-8
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        $newContent = $content
        $fileChanged = $false
        
        # Apply all replacements
        foreach ($search in $replacements.Keys) {
            $replace = $replacements[$search]
            $matches = ([regex]::Matches($newContent, [regex]::Escape($search))).Count
            
            if ($matches -gt 0) {
                $newContent = $newContent -replace [regex]::Escape($search), $replace
                $stats.patternCounts[$search] += $matches
                $stats.totalReplacements += $matches
                $fileChanged = $true
            }
        }
        
        # Write back if changed
        if ($fileChanged) {
            [System.IO.File]::WriteAllText($file.FullName, $newContent, (New-Object System.Text.UTF8Encoding($false)))
            $stats.filesChanged++
        }
        
        $stats.filesScanned++
    }
    catch {
        $stats.errors += @{
            file = $file.FullName
            error = $_.Exception.Message
        }
    }
}

# Generate report
$reportPath = Join-Path $PSScriptRoot "mojibake-fix-report.md"
$report = @"
# Mojibake Fix Report

**Generated:** $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")

## Summary

- **Files scanned:** $($stats.filesScanned)
- **Files changed:** $($stats.filesChanged)
- **Total replacements:** $($stats.totalReplacements)

## Replacements by Pattern

| Pattern | Replacement | Count |
|---------|-------------|-------|
"@

foreach ($pattern in $replacements.Keys | Sort-Object) {
    $count = $stats.patternCounts[$pattern]
    if ($count -gt 0) {
        $replace = $replacements[$pattern]
        $report += "`n| ``$pattern`` | $replace | $count |"
    }
}

if ($stats.errors.Count -gt 0) {
    $report += "`n`n## Errors`n`n"
    foreach ($err in $stats.errors) {
        $report += "- **$($err.file):** $($err.error)`n"
    }
}

$report += "`n`n## Status`n`n"
$report += "✅ Mojibake replacement pass completed.`n"

[System.IO.File]::WriteAllText($reportPath, $report, (New-Object System.Text.UTF8Encoding($false)))

# Console output
Write-Host "=" * 60 -ForegroundColor Green
Write-Host "MOJIBAKE FIX COMPLETED" -ForegroundColor Green
Write-Host "=" * 60 -ForegroundColor Green
Write-Host "Files scanned:       $($stats.filesScanned)" -ForegroundColor White
Write-Host "Files changed:       $($stats.filesChanged)" -ForegroundColor Yellow
Write-Host "Total replacements:  $($stats.totalReplacements)" -ForegroundColor Cyan
Write-Host "`nReport written to: $reportPath" -ForegroundColor White

if ($stats.errors.Count -gt 0) {
    Write-Host "`n⚠️  $($stats.errors.Count) file(s) had errors - check report for details" -ForegroundColor Red
}
