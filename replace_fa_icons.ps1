# PowerShell script to replace Font Awesome icons with emoji equivalents
# UTF-8 encoding without BOM

$ErrorActionPreference = "Stop"

# Icon mapping (order matters - more specific patterns first)
$iconMap = @{
    'fa-calendar-xmark' = '❌'
    'fa-calendar-check' = '✅'
    'fa-calendar-days' = '📅'
    'fa-calendar' = '📅'
    'fa-clock' = '⏰'
    'fa-user-graduate' = '🎓'
    'fa-user-secret' = '🕵️'
    'fa-user-tie' = '👔'
    'fa-chalkboard-user' = '👨‍🏫'
    'fa-user' = '👤'
    'fa-users' = '👥'
    'fa-envelope' = '✉️'
    'fa-phone' = '📞'
    'fa-book-open' = '📖'
    'fa-book' = '📚'
    'fa-graduation-cap' = '🎓'
    'fa-star' = '⭐'
    'fa-heart' = '❤️'
    'fa-circle-check' = '✅'
    'fa-check' = '✅'
    'fa-circle-xmark' = '❌'
    'fa-xmark' = '❌'
    'fa-times' = '❌'
    'fa-lock' = '🔒'
    'fa-unlock' = '🔓'
    'fa-shield-halved' = '🛡️'
    'fa-shield' = '🛡️'
    'fa-certificate' = '📜'
    'fa-chart-line' = '📈'
    'fa-chart-bar' = '📊'
    'fa-chart' = '📊'
    'fa-bolt' = '⚡'
    'fa-rotate-left' = '🔄'
    'fa-rotate-right' = '🔄'
    'fa-comments' = '💬'
    'fa-comment' = '💬'
    'fa-bell' = '🔔'
    'fa-headset' = '🎧'
    'fa-credit-card' = '💳'
    'fa-gauge-high' = '📊'
    'fa-gauge' = '📊'
    'fa-magnifying-glass' = '🔍'
    'fa-search' = '🔍'
    'fa-location-dot' = '📍'
    'fa-map-marker' = '📍'
    'fa-globe' = '🌐'
    'fa-upload' = '📤'
    'fa-download' = '📥'
    'fa-video' = '🎥'
    'fa-file-pdf' = '📄'
    'fa-file' = '📄'
    'fa-folder-open' = '📂'
    'fa-folder' = '📁'
    'fa-trash' = '🗑️'
    'fa-pencil' = '✏️'
    'fa-pen' = '✏️'
    'fa-edit' = '✏️'
    'fa-plus' = '➕'
    'fa-minus' = '➖'
    'fa-right-from-bracket' = '🚪'
    'fa-sign-out' = '🚪'
    'fa-arrow-right' = '→'
    'fa-arrow-left' = '←'
    'fa-arrow-up' = '↑'
    'fa-arrow-down' = '↓'
    'fa-circle-info' = 'ℹ️'
    'fa-info-circle' = 'ℹ️'
    'fa-info' = 'ℹ️'
    'fa-triangle-exclamation' = '⚠️'
    'fa-exclamation-triangle' = '⚠️'
    'fa-exclamation-circle' = '⚠️'
    'fa-home' = '🏠'
    'fa-house' = '🏠'
    'fa-settings' = '⚙️'
    'fa-cog' = '⚙️'
    'fa-gear' = '⚙️'
    'fa-bars' = '☰'
    'fa-list' = '📋'
    'fa-grid' = '⊞'
    'fa-table' = '📋'
    'fa-money-bill' = '💵'
    'fa-wallet' = '👛'
    'fa-bank' = '🏦'
    'fa-building' = '🏢'
    'fa-briefcase' = '💼'
    'fa-trophy' = '🏆'
    'fa-award' = '🏅'
    'fa-medal' = '🥇'
    'fa-flag' = '🚩'
    'fa-tag' = '🏷️'
    'fa-tags' = '🏷️'
    'fa-key' = '🔑'
    'fa-link' = '🔗'
    'fa-share' = '📤'
    'fa-print' = '🖨️'
    'fa-image' = '🖼️'
    'fa-camera' = '📷'
    'fa-microphone' = '🎤'
    'fa-wifi' = '📶'
    'fa-desktop' = '🖥️'
    'fa-laptop' = '💻'
    'fa-mobile' = '📱'
    'fa-spinner' = '⏳'
    'fa-circle-notch' = '⏳'
    'fa-eye' = '👁️'
    'fa-eye-slash' = '🙈'
    'fa-ban' = '🚫'
    'fa-power-off' = '⏻'
    'fa-question-circle' = '❓'
    'fa-question' = '❓'
    'fa-thumbs-up' = '👍'
    'fa-thumbs-down' = '👎'
    'fa-robot' = '🤖'
    'fa-language' = '🌐'
    'fa-paint-brush' = '🎨'
    'fa-lightbulb' = '💡'
    'fa-fire' = '🔥'
    'fa-wand-magic-sparkles' = '✨'
    'fa-magic' = '✨'
}

function Get-IconEmoji {
    param([string]$classString)
    
    # Check each icon pattern (most specific first)
    foreach ($pattern in $iconMap.Keys) {
        if ($classString -match $pattern) {
            return $iconMap[$pattern]
        }
    }
    
    # Default fallback
    return '❓'
}

function Replace-FAIcons {
    param([string]$content)
    
    # Pattern to match <i> tags with Font Awesome classes
    $pattern = '<i\s+class="([^"]*(?:fa-solid|fa-regular|fa-brands|fa-light|fa-thin|fa-duotone|fas|far|fab|fal|fat)[^"]*)"([^>]*)>\s*</i>'
    
    $result = [regex]::Replace($content, $pattern, {
        param($match)
        
        $fullClass = $match.Groups[1].Value
        $otherAttrs = $match.Groups[2].Value
        
        # Get the emoji
        $emoji = Get-IconEmoji $fullClass
        
        # Extract non-fa classes
        $classes = $fullClass -split '\s+' | Where-Object { $_ -notmatch '^(fa-|fas|far|fab|fal|fat|fad)' }
        
        # Check if this is a large decorative icon
        $isLarge = ($fullClass -match 'text-[456]xl') -or ($otherAttrs -match 'text-[456]xl')
        
        if ($isLarge) {
            # Large decorative icon - use div
            if ($classes.Count -gt 0) {
                $classAttr = ' class="' + ($classes -join ' ') + '"'
            } else {
                $classAttr = ' class="text-4xl mb-3"'
            }
            return "<div$classAttr>$emoji</div>"
        } else {
            # Inline icon - use span
            if ($classes.Count -gt 0) {
                $classAttr = ' class="' + ($classes -join ' ') + '"'
            } else {
                $classAttr = ''
            }
            return "<span$classAttr style=`"font-size:inherit;`">$emoji</span>"
        }
    })
    
    return $result
}

# Get all blade files
$bladeFiles = Get-ChildItem -Path "c:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views" -Recurse -Filter "*.blade.php"

$processedCount = 0
$changedCount = 0

Write-Host "Found $($bladeFiles.Count) blade files to process..."

foreach ($file in $bladeFiles) {
    $processedCount++
    Write-Host "[$processedCount/$($bladeFiles.Count)] Processing: $($file.Name)" -NoNewline
    
    # Read file as UTF-8
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.UTF8Encoding]::new($false))
    
    # Replace FA icons
    $newContent = Replace-FAIcons $content
    
    # Check if content changed
    if ($content -ne $newContent) {
        # Write back as UTF-8 without BOM
        [System.IO.File]::WriteAllText($file.FullName, $newContent, [System.Text.UTF8Encoding]::new($false))
        $changedCount++
        Write-Host " - CHANGED" -ForegroundColor Green
    } else {
        Write-Host " - no changes" -ForegroundColor Gray
    }
}

Write-Host "`nProcessing complete!"
Write-Host "Files processed: $processedCount"
Write-Host "Files changed: $changedCount"
