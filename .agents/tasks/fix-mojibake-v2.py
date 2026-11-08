import os
import glob

# Mojibake byte sequences and their correct UTF-8 replacements
replacements = {
    # Emojis
    b'\xc3\xb0\xc5\xb8\xc2\x8f\xe2\x80\xa0': b'\xf0\x9f\x8f\x86',  # trophy
    b'\xc3\xb0\xc5\xb8\xe2\x80\x98\xc2\xa5': b'\xf0\x9f\x92\xa5',  # collision/group
    b'\xc3\xa2\xc5\xa1\xc2\xa1': b'\xe2\x9a\xa1',  # high voltage
    b'\xc3\xb0\xc5\xb8\xc5\xbd\xc2\xaf': b'\xf0\x9f\x8e\xaf',  # dart
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\x8e': b'\xf0\x9f\x94\x8e',  # magnifying glass
    b'\xc3\xb0\xc5\xb8\xc2\x96\xc2\xbc\xc3\xaf\xc2\xb8\xc2\x8f': b'\xf0\x9f\x96\xbc\xef\xb8\x8f',  # framed picture
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\xa1': b'\xf0\x9f\x92\xa1',  # light bulb
    
    # Star and marks  
    b'\xc3\xa2\xcb\x9c\xc2\x85': b'\xe2\x98\x85',  # star filled
    b'\xc3\xa2\xcb\x9c\xe2\x80\xa6': b'\xe2\x98\x85',  # star filled (alternate encoding)
    b'\xc3\xa2\xc5\x93\xc2\x97': b'\xe2\x9c\x97',  # ballot x
    b'\xc3\xa2\xc5\x93\xc2\x94': b'\xe2\x9c\x94',  # check mark
    
    # Punctuation
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\xa2\xc2\x80\xc5\x93': b'\xe2\x80\x94',  # em-dash
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\x82\xc2\xa6': b'\xe2\x80\xa6',  # ellipsis
    b'\xc3\x83\xc2\x97': b'\xc3\x97',  # multiplication
    b'\xc3\xa2\xc2\x94\xc2\x80': b'\xe2\x94\x80',  # box drawing
    b'\xc3\xa2\xc2\xa2': b'\xe2\x80\xa2',  # bullet
    
    # Currency and symbols
    b'\xc3\xa2\xc2\x82\xc2\xb9': b'\xe2\x82\xb9',  # rupee
    b'\xc3\x82\xc2\xb0': b'\xc2\xb0',  # degree
    b'\xc3\x82\xc2\xa7': b'\xc2\xa7',  # section
    b'\xc2\xb8\xe2\x80\x99\xc2\xb3': b'\xf0\x9f\x92\xb3',  # credit card
    
    # Superscripts
    b'\xc3\x82\xc2\xb2': b'\xc2\xb2',  # superscript 2
    b'\xc3\x82\xc2\xb3': b'\xc2\xb3',  # superscript 3
    b'\xc3\x82\xc2\xb9': b'\xc2\xb9',  # superscript 1
}

views_path = r'c:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views'
blade_files = glob.glob(os.path.join(views_path, '**', '*.blade.php'), recursive=True)

files_scanned = 0
files_changed = 0
total_replacements = 0
pattern_counts = {}

for pattern in replacements:
    pattern_counts[pattern] = 0

print(f"Found {len(blade_files)} .blade.php files\n")

for filepath in blade_files:
    files_scanned += 1
    
    try:
        with open(filepath, 'rb') as f:
            content = f.read()
        
        new_content = content
        file_changed = False
        
        for search, replace in replacements.items():
            count = new_content.count(search)
            if count > 0:
                new_content = new_content.replace(search, replace)
                pattern_counts[search] += count
                total_replacements += count
                file_changed = True
        
        if file_changed:
            with open(filepath, 'wb') as f:
                f.write(new_content)
            files_changed += 1
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

# Generate report
report_path = os.path.join(os.path.dirname(__file__), 'mojibake-fix-report.md')
report = f"""# Mojibake Fix Report

**Generated:** {__import__('datetime').datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

## Summary

- **Files scanned:** {files_scanned}
- **Files changed:** {files_changed}
- **Total replacements:** {total_replacements}

## Replacements by Pattern

| Pattern (hex) | Replacement | Count |
|---------------|-------------|-------|
"""

emoji_names = {
    b'\xc3\xb0\xc5\xb8\xc2\x8f\xe2\x80\xa0': '🏆 (trophy)',
    b'\xc3\xb0\xc5\xb8\xe2\x80\x98\xc2\xa5': '💥 (collision)',
    b'\xc3\xa2\xc5\xa1\xc2\xa1': '⚡ (high voltage)',
    b'\xc3\xb0\xc5\xb8\xc5\xbd\xc2\xaf': '🎯 (dart)',
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\x8e': '🔎 (magnifying glass)',
    b'\xc3\xb0\xc5\xb8\xc2\x96\xc2\xbc\xc3\xaf\xc2\xb8\xc2\x8f': '🖼️ (framed picture)',
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\xa1': '💡 (light bulb)',
    b'\xc3\xa2\xcb\x9c\xc2\x85': '★ (star)',
    b'\xc3\xa2\xc5\x93\xc2\x97': '✗ (ballot x)',
    b'\xc3\xa2\xc5\x93\xc2\x94': '✓ (check mark)',
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\xa2\xc2\x80\xc5\x93': '— (em-dash)',
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\x82\xc2\xa6': '… (ellipsis)',
    b'\xc3\x83\xc2\x97': '× (multiplication)',
    b'\xc3\xa2\xc2\x94\xc2\x80': '─ (box drawing)',
    b'\xc3\xa2\xc2\xa2': '• (bullet)',
    b'\xc3\xa2\xc2\x82\xc2\xb9': '₹ (rupee)',
    b'\xc3\x82\xc2\xb0': '° (degree)',
    b'\xc3\x82\xc2\xa7': '§ (section)',
    b'\xc2\xb8\xe2\x80\x99\xc2\xb3': '💳 (credit card)',
    b'\xc3\x82\xc2\xb2': '² (superscript 2)',
    b'\xc3\x82\xc2\xb3': '³ (superscript 3)',
    b'\xc3\x82\xc2\xb9': '¹ (superscript 1)',
}

for pattern, count in pattern_counts.items():
    if count > 0:
        hex_str = ' '.join(f'{b:02X}' for b in pattern)
        name = emoji_names.get(pattern, '(unknown)')
        report += f"| `{hex_str}` | {name} | {count} |\n"

report += f"""

## Status

✅ Mojibake replacement pass completed.
"""

with open(report_path, 'w', encoding='utf-8') as f:
    f.write(report)

print("=" * 60)
print("MOJIBAKE FIX COMPLETED")
print("=" * 60)
print(f"Files scanned:       {files_scanned}")
print(f"Files changed:       {files_changed}")
print(f"Total replacements:  {total_replacements}")
print(f"\nReport written to: {report_path}")
