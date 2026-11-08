#!/usr/bin/env python3
"""
Fix broken icon pattern left by previous emoji-replacement script.

BROKEN:  <span class="{{ $icon }}" style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
FIXED:   <i class="fas {{ $icon }}"></i>

The variable ({{ $icon }}, {{ $ic }}, etc.) contains just the icon name
like "fa-calendar", so the correct render is <i class="fas fa-calendar">.
"""

import glob
import re

# Match the broken pattern. The variable content varies but always
# uses Blade {{ ... }} double-brace syntax.
BROKEN = re.compile(
    r'<span class="(\{\{[^}]+\}})" style="font-size:inherit;">'
    r'<i class="fas fa-question-circle"></i></span>'
)

REPLACEMENT = r'<i class="fas \1"></i>'


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()

    content = BROKEN.sub(REPLACEMENT, original)

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
