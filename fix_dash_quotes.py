#!/usr/bin/env python3
"""
Replace all "-  patterns with just -  (removing the stray right double-quote
that was left before a hyphen after the smart-quote replacement).

Also fixes related encoding artifacts:
  Å-"    →  —   (em dash with encoding garble)
  Å-"-   →  —   (em dash with encoding garble + extra hyphen)
  "-"¹   →  ✓   (checkmark that got mangled)
  "-Â     →  (remove)
  "-Â    →  (remove)
"""

import glob
import re

def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()

    content = original

    # Main fix: "-  →  -  (right double-quote + hyphen → just hyphen)
    content = content.replace('"-', '-')

    # Fix garbled em-dash patterns
    content = content.replace('Å-"-', '—')
    content = content.replace('Å-"', '—')

    # Fix mangled checkmark patterns (from security-settings)
    content = content.replace('"-"¹', '✓')
    content = content.replace('"-Â', '')
    content = content.replace('"-"', '-')

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
