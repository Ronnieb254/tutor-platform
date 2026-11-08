#!/usr/bin/env python3
"""
Fix mojibake (garbled emoji) in blade templates.

The mojibake pattern Ã°Å¸... occurs when UTF-8 encoded emoji bytes are
interpreted as Latin-1 (ISO-8859-1) and then re-encoded as UTF-8.

To fix: find mojibake sequences, encode them back as Latin-1, then decode
as UTF-8 to recover the original emoji character.

Mojibake characters are in the Latin-1 supplement range (U+00C0-U+00FF)
that appear in sequences resembling double-encoded UTF-8 emoji.
"""

import glob
import re

# Mojibake emoji sequences start with Ã° (U+00C3 U+00B0) which is the
# Latin-1 interpretation of UTF-8 byte 0xF0 (start of 4-byte emoji sequence)
# followed by Å¸ (U+00C5 U+00B8) which is UTF-8 0xC5 0xB8 = ¸ but actually
# the second byte 0x9F interpreted as Latin-1... 

# Strategy: find runs of characters in the Latin-1 supplement range
# (Ã, Å, Â, â, etc.) that form mojibake, encode as latin-1, decode as utf-8

# Pattern: sequences of mojibake chars (Latin-1 supplement + some ASCII)
MOJIBAKE_PATTERN = re.compile(
    r'[\xc0-\xff][\x80-\xff\xc0-\xff]*'  # Run of Latin-1 supplement chars
    r'(?:[\xc0-\xff][\x80-\xff])*'       # followed by more pairs
)

# More targeted: find Ã°... sequences (emoji mojibake)
EMOJI_MOJIBAKE = re.compile(
    r'(?:Ã°[\xc0-\xff]+(?:[\xc0-\xff]+)*|â€[\xc0-\xff]+)'
)


def try_fix_mojibake(s):
    """Try to decode a mojibake string back to proper UTF-8."""
    try:
        # Encode the mojibake as Latin-1, then decode as UTF-8
        fixed = s.encode('latin-1').decode('utf-8')
        # Only return if the result contains actual emoji or special chars
        if any(ord(c) > 0x2000 for c in fixed):
            return fixed
    except (UnicodeDecodeError, UnicodeEncodeError):
        pass
    return None


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()
    
    content = original
    
    # Find all mojibake sequences and try to fix them
    # We look for sequences starting with Ã° (emoji mojibake) or â€ (punctuation mojibake)
    def replace_mojibake(match):
        s = match.group(0)
        fixed = try_fix_mojibake(s)
        if fixed:
            return fixed
        return s
    
    # Use a broader pattern to catch all mojibake
    # Mojibake chars: Ã (C3), Å (C5), Â (C2), â (E2), € (20AC in utf8 but shows differently)
    # Pattern: runs of chars in U+00C0-U+00FF range, possibly with some ASCII between
    content = re.sub(
        r'[\u00c0-\u00ff\u201a\u201e\u2026\u2019\u201d\u201c\u00a0-\u00ff]{2,}',
        replace_mojibake,
        content
    )
    
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
