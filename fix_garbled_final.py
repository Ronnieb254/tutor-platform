#!/usr/bin/env python3
"""
Final cleanup of remaining garbled mojibake fragments.

After the Ã° prefix was stripped, these fragments remain:
  ¸Å½Â¥ → video icon
  ¸""ž → book/article icon
  ¸"•  → pdf icon
  ¸""¹ → clipboard/past_paper icon
  ¸Å½"° → video icon
  ¸Å½"  → video icon (variant)
  Ã‚Â© → © (copyright)
  Ã‚Â± → ± (plus-minus)
  ¸"• → pdf icon
"""

import glob
import re

REPLACEMENTS = [
    # typeIcons arrays - replace with FA icon HTML
    ("'¸\"\"ž'", "'<i class=\"fas fa-book\"></i>'"),
    ("'¸\"•'", "'<i class=\"fas fa-file-pdf\"></i>'"),
    ("'¸\"\"¹'", "'<i class=\"fas fa-file-lines\"></i>'"),
    ("'¸Å½Â¥'", "'<i class=\"fas fa-video\"></i>'"),
    ("'¸Å½\"°'", "'<i class=\"fas fa-video\"></i>'"),
    ("'¸Å½\"'", "'<i class=\"fas fa-video\"></i>'"),
    
    # Standalone garbled strings used as icons (in divs with font-size)
    ('font-size:32px;text-align:center;margin-bottom:16px;">¸Å½"°</div>',
     'font-size:32px;text-align:center;margin-bottom:16px;"><i class="fas fa-video"></i></div>'),
    ('font-size:32px;margin-bottom:10px;">¸Å½"</div>',
     'font-size:32px;margin-bottom:10px;"><i class="fas fa-graduation-cap"></i></div>'),
    
    # Join Session button with garbled prefix
    ("'¸Å½Â¥ Join Session'", "'<i class=\\'fas fa-video\\'></i> Join Session'"),
    
    # In ternary for video type
    ("? '¸Å½Â¥ Watch Video'", "? '<i class=\\'fas fa-video\\'></i> Watch Video'"),
    ("'<i class=\"fas fa-sticky-note\"></i>¥ Download '", "'<i class=\\'fas fa-download\\'></i> Download '"),
    
    # Academic Certificate label
    ("¸Å½\" Academic Certificate", "Academic Certificate"),
    
    # Jitsi heading
    ("¸Å½Â¥ Jitsi Live Sessions", "Jitsi Live Sessions"),
    
    # Copyright symbol
    ("Ã‚Â©", "©"),
    
    # Plus-minus
    ("Ã‚Â±", "±"),
    
    # Remaining stray ¸ characters (cedilla used as mojibake remnant)
    # Only replace when followed by specific mojibake chars
    ("¸Å½Â¥", ""),  # Remove any remaining video mojibake
    ("¸\"\"ž", ""),  # Remove any remaining book mojibake
    ("¸\"•", ""),    # Remove any remaining pdf mojibake
    ("¸\"\"¹", ""),  # Remove any remaining clipboard mojibake
    ("¸Å½\"", ""),   # Remove any remaining video mojibake variant
    
    # Stray sticky-note + garbled char in notes type
    ('<i class="fas fa-sticky-note"></i>', '<i class="fas fa-sticky-note"></i>'),
    ('<i class="fas fa-sticky-note"></i>¥', '<i class="fas fa-sticky-note"></i>'),
]


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()
    
    content = original
    for bad, good in REPLACEMENTS:
        content = content.replace(bad, good)
    
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
