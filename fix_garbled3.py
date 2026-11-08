#!/usr/bin/env python3
"""Final final cleanup of remaining ¸" mojibake fragments."""

import glob
import re

REPLACEMENTS = [
    # Live Now badge in parent dashboard
    ("¸\"Â´ Live Now", '<i class="fas fa-signal"></i> Live Now'),
    # Recovery code button
    ("¸\"' Use a recovery code instead", '<i class="fas fa-key"></i> Use a recovery code instead'),
    # View Recovery Codes
    ("¸\"' View Recovery Codes", '<i class="fas fa-key"></i> View Recovery Codes'),
    # Regenerate
    ("¸\"\"ž Regenerate", '<i class="fas fa-rotate"></i> Regenerate'),
    # REQUIRED FOR ADMINS badge
    ("¸\"' REQUIRED FOR ADMINS", '<i class="fas fa-shield-halved"></i> REQUIRED FOR ADMINS'),
    # Empty tutor search
    ("¸\"Â", '<i class="fas fa-magnifying-glass"></i>'),
    # TSC Certificate label
    ("¸\"Å- TSC Certificate", "TSC Certificate"),
    # Good Conduct Certificate label
    ("¸\"ºÂ¡Ã¯Â¸Â Certificate of Good Conduct", "Certificate of Good Conduct"),
    # Category icon fallback in admin subjects
    ("¸\"Å¡", "\U0001F3F7"),  # label emoji - or just empty
    # General heading in settings
    ("¸Å\x92Â General", '<i class="fas fa-gear"></i> General'),
    # Maintenance heading
    ("¸\"Â§ Maintenance", '<i class="fas fa-wrench"></i> Maintenance'),
    
    # Academic Certificate label (if still there)
    ("¸\"Å- Academic Certificate", "Academic Certificate"),
]


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()
    
    content = original
    for bad, good in REPLACEMENTS:
        content = content.replace(bad, good)
    
    # Also clean up any remaining stray ¸" patterns that are clearly mojibake
    # Pattern: ¸ followed by " and then garbled chars
    content = re.sub(r'¸"[^\s<>\w/]{1,5}', '', content)
    
    if content != original:
        with open(filepath, 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(content)
        return True
    return False


def main():
    files = glob.glob('resources/views/**/*.blade.php', recursive=True)
    fixed = 0
    for f in sorted(files):
        if fix_file(f):
            fixed += 1
            print(f'  fixed  {f}')
    print(f'\nDone. {fixed} file(s) updated.')


if __name__ == '__main__':
    main()
