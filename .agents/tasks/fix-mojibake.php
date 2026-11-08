<?php
// PHP script to fix mojibake in blade templates

$replacements = [
    "â‚¬"" => "—",
    "â‚¬Â¦" => "…",
    "Ã—" => "×",
    "â"€" => "─",
    "â‚¹" => "₹",
    "Â°" => "°",
    "Â§" => "§",
    "â¢" => "•",
    "ðŸ"Ž" => "🔎",
    "ðŸ–¼ï¸" => "🖼️",
    "ðŸ'¡" => "💡",
    "Â²" => "²",
    "Â³" => "³",
    "Â¹" => "¹",
    "ðŸ'¥" => "💥",
    "âš¡" => "⚡",
    "ðŸŽ¯" => "🎯",
    "ðŸ†" => "🏆",
    "â˜…" => "★",
    "âœ—" => "✗",
    "âœ"" => "✓",
    "¸'Â³" => "💳"
];

$stats = [
    'filesScanned' => 0,
    'filesChanged' => 0,
    'totalReplacements' => 0,
    'patternCounts' => [],
    'errors' => []
];

foreach (array_keys($replacements) as $pattern) {
    $stats['patternCounts'][$pattern] = 0;
}

function findBladeFiles($dir) {
    $files = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$viewsPath = __DIR__ . '/../../resources/views';
$bladeFiles = findBladeFiles($viewsPath);

echo "Found " . count($bladeFiles) . " .blade.php files\n\n";

foreach ($bladeFiles as $filePath) {
    try {
        $content = file_get_contents($filePath);
        $newContent = $content;
        $fileChanged = false;
        
        foreach ($replacements as $search => $replace) {
            $count = substr_count($newContent, $search);
            if ($count > 0) {
                $newContent = str_replace($search, $replace, $newContent);
                $stats['patternCounts'][$search] += $count;
                $stats['totalReplacements'] += $count;
                $fileChanged = true;
            }
        }
        
        if ($fileChanged) {
            file_put_contents($filePath, $newContent);
            $stats['filesChanged']++;
        }
        
        $stats['filesScanned']++;
    } catch (Exception $e) {
        $stats['errors'][] = ['file' => $filePath, 'error' => $e->getMessage()];
    }
}

$reportPath = __DIR__ . '/mojibake-fix-report.md';
$report = "# Mojibake Fix Report\n\n";
$report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n\n";
$report .= "## Summary\n\n";
$report .= "- **Files scanned:** {$stats['filesScanned']}\n";
$report .= "- **Files changed:** {$stats['filesChanged']}\n";
$report .= "- **Total replacements:** {$stats['totalReplacements']}\n\n";
$report .= "## Replacements by Pattern\n\n";
$report .= "| Pattern | Replacement | Count |\n";
$report .= "|---------|-------------|-------|\n";

foreach ($replacements as $search => $replace) {
    $count = $stats['patternCounts'][$search];
    if ($count > 0) {
        $report .= "| `$search` | $replace | $count |\n";
    }
}

if (count($stats['errors']) > 0) {
    $report .= "\n## Errors\n\n";
    foreach ($stats['errors'] as $err) {
        $report .= "- **{$err['file']}:** {$err['error']}\n";
    }
}

$report .= "\n## Status\n\n";
$report .= "✅ Mojibake replacement pass completed.\n";

file_put_contents($reportPath, $report);

echo str_repeat('=', 60) . "\n";
echo "MOJIBAKE FIX COMPLETED\n";
echo str_repeat('=', 60) . "\n";
echo "Files scanned:       {$stats['filesScanned']}\n";
echo "Files changed:       {$stats['filesChanged']}\n";
echo "Total replacements:  {$stats['totalReplacements']}\n";
echo "\nReport written to: $reportPath\n";

if (count($stats['errors']) > 0) {
    echo "\n⚠️  " . count($stats['errors']) . " file(s) had errors - check report for details\n";
}
