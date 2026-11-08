#!/usr/bin/env python3
"""
Replace smart/curly double-quotes ("") with a straight hyphen-minus (-) per the
user's instruction: remove the curly quotes where placed unnecessarily and
change to -.

Strategy:
  - \u201c (left  ") and \u201d (right ") in visible text → -
  - But when they actually wrap a quoted string inside HTML/PHP attributes we
    keep them as straight double-quotes (") because the content around them
    forms a real string literal. In practice here they appear almost exclusively
    as typographic en/em-dash substitutes in page copy, so a blanket replacement
    of both characters with " – " (or simply "-") is correct.

The user said "change to -", so we replace both \u201c and \u201d with a
straight hyphen (-).  Where the pair wraps a meaningful phrase like "text"
we replace the whole ""text"" with plain "text" so it reads cleanly.
"""

import glob
import re

def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()

    content = original

    # Replace paired smart quotes ""phrase"" → -phrase- (keep the phrase, drop the curly wrapping)
    # e.g. "Active Recall" → Active Recall
    content = re.sub(r'\u201c([^\u201d]*)\u201d', r'-\1-', content)

    # Replace any remaining lone left or right smart quotes with -
    content = content.replace('\u201c', '-')
    content = content.replace('\u201d', '-')

    if content != original:
        with open(filepath, 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(content)
        return True
    return False


def main():
    files = glob.glob('resources/views/**/*.blade.php', recursive=True)
    fixed = 0
    for f in files:
        if fix_file(f):
            fixed += 1
            print(f'  fixed  {f}')
    print(f'\nDone. {fixed} file(s) updated.')


if __name__ == '__main__':
    main()
