const fs = require('fs');
const path = require('path');

// Mojibake replacements (search -> replacement)
const replacements = [
  ['â‚¬"', '—'],   // em-dash
  ['â‚¬Â¦', '…'],   // ellipsis
  ['Ã—', '×'],     // multiplication sign
  ['â"€', '─'],    // box-drawing horizontal
  ['â‚¹', '₹'],    // rupee sign
  ['Â°', '°'],     // degree symbol
  ['Â§', '§'],     // section sign
  ['â¢', '•'],     // bullet
  ['ðŸ"Ž', '🔎'],  // magnifying glass emoji
  ['ðŸ–¼ï¸', '🖼️'], // framed picture emoji
  ['ðŸ'¡', '💡'],  // light bulb emoji
  ['Â²', '²'],     // superscript 2
  ['Â³', '³'],     // superscript 3
  ['Â¹', '¹'],     // superscript 1
  ['ðŸ'¥', '💥'],  // collision emoji (group)
  ['âš¡', '⚡'],   // high voltage emoji (quick)
  ['ðŸŽ¯', '🎯'],  // dart emoji (guided practice)
  ['ðŸ†', '🏆'],   // trophy emoji (deep dive)
  ['â˜…', '★'],    // star (filled)
  ['âœ—', '✗'],    // ballot x (cross mark)
  ['âœ"', '✓'],    // check mark
  ['¸'Â³', '💳'],  // credit card emoji
];

// Statistics
const stats = {
  filesScanned: 0,
  filesChanged: 0,
  totalReplacements: 0,
  patternCounts: {},
  errors: []
};

// Initialize pattern counts
replacements.forEach(([pattern]) => {
  stats.patternCounts[pattern] = 0;
});

// Recursively find all .blade.php files
function findBladeFiles(dir) {
  const files = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...findBladeFiles(fullPath));
    } else if (entry.isFile() && entry.name.endsWith('.blade.php')) {
      files.push(fullPath);
    }
  }
  
  return files;
}

// Process a single file
function processFile(filePath) {
  try {
    // Read file as UTF-8, preserving BOM if present
    const content = fs.readFileSync(filePath, 'utf8');
    let newContent = content;
    let fileChanged = false;
    
    // Apply all replacements
    replacements.forEach(([search, replace]) => {
      const regex = new RegExp(search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
      const matches = (newContent.match(regex) || []).length;
      
      if (matches > 0) {
        newContent = newContent.replace(regex, replace);
        stats.patternCounts[search] += matches;
        stats.totalReplacements += matches;
        fileChanged = true;
      }
    });
    
    // Write back if changed
    if (fileChanged) {
      fs.writeFileSync(filePath, newContent, 'utf8');
      stats.filesChanged++;
    }
    
    stats.filesScanned++;
  } catch (error) {
    stats.errors.push({ file: filePath, error: error.message });
  }
}

// Main execution
const viewsPath = path.join(__dirname, '..', '..', 'resources', 'views');
console.log(`Scanning for .blade.php files in: ${viewsPath}\n`);

const bladeFiles = findBladeFiles(viewsPath);
console.log(`Found ${bladeFiles.length} .blade.php files\n`);

// Process each file
bladeFiles.forEach(file => {
  processFile(file);
});

// Generate report
const reportPath = path.join(__dirname, 'mojibake-fix-report.md');
let report = `# Mojibake Fix Report\n\n`;
report += `**Generated:** ${new Date().toISOString()}\n\n`;
report += `## Summary\n\n`;
report += `- **Files scanned:** ${stats.filesScanned}\n`;
report += `- **Files changed:** ${stats.filesChanged}\n`;
report += `- **Total replacements:** ${stats.totalReplacements}\n\n`;

report += `## Replacements by Pattern\n\n`;
report += `| Pattern | Replacement | Count |\n`;
report += `|---------|-------------|-------|\n`;
replacements.forEach(([search, replace]) => {
  const count = stats.patternCounts[search];
  if (count > 0) {
    report += `| \`${search}\` | ${replace} | ${count} |\n`;
  }
});

if (stats.errors.length > 0) {
  report += `\n## Errors\n\n`;
  stats.errors.forEach(({ file, error }) => {
    report += `- **${file}:** ${error}\n`;
  });
}

report += `\n## Status\n\n`;
report += `✅ Mojibake replacement pass completed.\n`;

fs.writeFileSync(reportPath, report, 'utf8');

// Console output
console.log('='.repeat(60));
console.log('MOJIBAKE FIX COMPLETED');
console.log('='.repeat(60));
console.log(`Files scanned:       ${stats.filesScanned}`);
console.log(`Files changed:       ${stats.filesChanged}`);
console.log(`Total replacements:  ${stats.totalReplacements}`);
console.log(`\nReport written to: ${reportPath}`);

if (stats.errors.length > 0) {
  console.log(`\n⚠️  ${stats.errors.length} file(s) had errors - check report for details`);
}
