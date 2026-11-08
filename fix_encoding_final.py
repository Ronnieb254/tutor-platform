#!/usr/bin/env python3
"""Fix remaining UTF-8 encoding issues in blade templates."""

import os
import glob

# Define encoding replacements - using raw bytes approach
replacements = [
    ('â€"', '—'),  # em dash
    ('â€"', '–'),  # en dash
    ("â€™", "'"),  # right single quote
    ("â€˜", "'"),  # left single quote
    ('â€œ', '"'),  # left double quote
    ('â€', '"'),   # right double quote
    ('Â·', '·'),   # middle dot
    ('Ã‚·', '·'),  # another middle dot variant
    ('👋', '👋'),  # waving hand emoji fix
    ('🔴', '🔴'),  # red circle emoji fix
    ('—', '—'),  # another em dash variant
    ('€', '€'),  # euro symbol
]

def fix_file(filepath):
    """Fix encoding in a single file."""
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        original = content
        for bad, good in replacements:
            content = content.replace(bad, good)
        
        if content != original:
            with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
                f.write(content)
            print(f"Fixed: {filepath}")
            return True
        return False
    except Exception as e:
        print(f"Error in {filepath}: {e}")
        return False

def main():
    """Process all blade templates."""
    pattern = 'resources/views/**/*.blade.php'
    files = glob.glob(pattern, recursive=True)
    
    fixed_count = 0
    for filepath in files:
        if fix_file(filepath):
            fixed_count += 1
    
    print(f"\n{'='*60}")
    print(f"Fixed {fixed_count} file(s)")
    print(f"{'='*60}")

if __name__ == '__main__':
    main()
