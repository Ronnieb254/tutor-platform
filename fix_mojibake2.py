#!/usr/bin/env python3
"""
Fix mojibake garbled emoji by replacing with Font Awesome icon HTML.

Mojibake patterns (Ã°Å¸...) result from double-encoding: emoji UTF-8 bytes
read as CP1252, then re-encoded as UTF-8, then further mangled by smart-quote
replacement. We replace each with an appropriate FA icon <i> tag.
"""

import glob

# Mapping of mojibake substrings → FA icon HTML
# Using \u escapes for the mojibake patterns to avoid encoding issues in this file
REPLACEMENTS = [
    # Waving hand (Hello greeting)
    ("\u00c3\u00b0\u00c5\u00b8'\"\u00b9", '<i class="fas fa-hand"></i>'),
    
    # Boy/child icon
    ("\u00c3\u00b0\u00c5\u00b8'\u00c2\u00a6", '<i class="fas fa-child"></i>'),
    
    # Red circle (Live Now)
    ("\u00c3\u00b0\u00c5\u00b8\"\u00c2\u00b4", '<i class="fas fa-circle" style="color:#10B981"></i>'),
    
    # Movie camera (Join Session)
    ("\u00c3\u00b0\u00c5\u00b8\u017d\u00c2\u00a5", '<i class="fas fa-video"></i> '),
    
    # Calendar
    ("\u00c3\u00b0\u00c5\u00b8\"\u2026", '<i class="fas fa-calendar"></i>'),
    
    # Money bag (Refund)
    ("\u00c3\u00b0\u00c5\u00b8'\u00c2\u00b0", '<i class="fas fa-money-bill"></i> '),
    
    # Speech bubble (messages)
    ("\u00c3\u00b0\u00c5\u00b8'\u00c2\u00ac", '<i class="fas fa-comment"></i>'),
    
    # Clipboard (Copy codes)
    ("\u00c3\u00b0\u00c5\u00b8\"\u00b9", '<i class="fas fa-clipboard"></i> '),
    
    # Key/memo (recovery code)
    ("\u00c3\u00b0\u00c5\u00b8\"'", '<i class="fas fa-key"></i> '),
    
    # Green book (article type icon)
    ("\u00c3\u00b0\u00c5\u00b8\"\u009e", '<i class="fas fa-book"></i>'),
    
    # Red book (pdf type icon)
    ("\u00c3\u00b0\u00c5\u00b8\"\u0095", '<i class="fas fa-file-pdf"></i>'),
    
    # Memo (notes type icon)
    ("\u00c3\u00b0\u00c5\u00b8\"\u00c2", '<i class="fas fa-sticky-note"></i>'),
    
    # Family emoji (long mojibake) - match and replace
    # This is complex, handle separately
]

# Family mojibake - varies, use a broader pattern
# "Ã°Å¸'Â¨â€ÂÂÃ°Å¸'Â©â€ÂÂÃ°Å¸'Â§" 
FAMILY_MOJI = "\u00c3\u00b0\u00c5\u00b8'\u00c2\u00a8"  # first part of family
FAMILY_REPLACEMENT = '<i class="fas fa-users"></i>'


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()
    
    content = original
    
    # Handle family mojibake (multi-part) - find and replace the whole sequence
    while FAMILY_MOJI in content:
        idx = content.index(FAMILY_MOJI)
        # Find the end of the mojibake sequence (look for a non-mojibake char)
        end = idx + len(FAMILY_MOJI)
        while end < len(content) and (ord(content[end]) > 127 or content[end] in "'\""):
            end += 1
        # Replace with family icon
        content = content[:idx] + FAMILY_REPLACEMENT + content[end:]
    
    # Handle all other replacements
    for bad, good in REPLACEMENTS:
        content = content.replace(bad, good)
    
    # Clean up any remaining mojibake fragments
    # Remove stray Ã‚ (U+00C2) and other Latin-1 supplement chars that are mojibake remnants
    # But be careful not to remove legitimate characters
    
    # Remove remaining Ã° (start of emoji mojibake) sequences
    import re
    # Match: Ã° followed by mojibake chars, ending at a recognizable HTML/text char
    content = re.sub(
        r'\u00c3\u00b0[\u00c0-\u00ff\u2019\u201d\u201c\u2018\u0027\u0022\u201a\u201e]*',
        '',
        content
    )
    
    # Remove stray Ã‚ (often appears before valid chars as mojibake remnant)
    content = content.replace('\u00c3\u00c2', '')
    
    # Fix any double spaces left behind
    content = re.sub(r'  +', ' ', content)
    
    if content != original:
        with open(filepath, 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(content)
        return True
    return False


def main():
    import re
    files = glob.glob('resources/views/**/*.blade.php', recursive=True)
    fixed = 0
    for f in sorted(files):
        if fix_file(f):
            fixed += 1
            print(f'  fixed  {f}')
    print(f'\nDone. {fixed} file(s) updated.')


if __name__ == '__main__':
    main()
