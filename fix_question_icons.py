#!/usr/bin/env python3
"""
Fix ALL remaining fa-question-circle placeholder icons by reading surrounding
context and replacing with the correct Font Awesome icon.

Two patterns to fix:
1. <span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
   → <i class="fas fa-CORRECT"></i>
2. <span class="{{ $var }}" style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
   → <i class="fas {{ $var }}"></i>
3. <span class="$var }}" style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
   → <i class="fas {{ $var }}"></i>  (missing opening brace fix)
"""

import glob
import re

# Pattern 2 & 3: Blade variable on span - extract variable and use on <i>
BLADE_VAR_PATTERN = re.compile(
    r'<span class="(\$[a-zA-Z_]+}\})"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>'
)
# Also match {{ $var }} format
BLADE_VAR_PATTERN2 = re.compile(
    r'<span class="(\{\{\s*\$[a-zA-Z_]+\s*\}\})"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>'
)
# Also match broken $var }} (missing opening brace)
BLADE_VAR_PATTERN3 = re.compile(
    r'<span class="(\$[a-zA-Z_]+\}\})"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>'
)

# Pattern 1: static placeholder - replace based on context
# We'll do this by reading a window of text around each match
STATIC_PATTERN = '<span style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>'

# Context-based icon mapping (checked in order, first match wins)
# Each entry: (search_text_before, search_text_after, icon_name)
CONTEXT_RULES = [
    # Pagination - Prev
    ('Prev</span>', '', 'fa-chevron-left'),
    ('Prev</a>', '', 'fa-chevron-left'),
    ('', 'Prev</span>', 'fa-chevron-left'),
    ('', 'Prev</a>', 'fa-chevron-left'),
    # Pagination - Next
    ('Next ', '', 'fa-chevron-right'),
    ('>Next ', '', 'fa-chevron-right'),
    # Copy Code / Copy
    ('', 'Copy Code', 'fa-copy'),
    ('', ' Copy', 'fa-copy'),
    ('', '> Copy', 'fa-copy'),
    ('Copy All', '', 'fa-copy'),
    # See Full Guide
    ('', 'See Full Guide', 'fa-arrow-right'),
    # Book Spot / Book Seat
    ('', 'Book Spot', 'fa-ticket'),
    ('', 'Book Seat', 'fa-ticket'),
    # Log in / Sign In
    ('', 'Sign In', 'fa-right-to-bracket'),
    ('', 'Log in to Book', 'fa-right-to-bracket'),
    # Apply Now
    ('', 'Apply Now', 'fa-paper-plane'),
    # Send Message / Send
    ('', 'Send Message', 'fa-paper-plane'),
    ('', 'Send</button>', 'fa-paper-plane'),
    ('', '>Send', 'fa-paper-plane'),
    # REC indicator
    ('', 'REC', 'fa-circle'),
    # LIVE NOW
    ('', 'LIVE NOW', 'fa-signal'),
    # Session Summary
    ('', 'Session Summary', 'fa-receipt'),
    # Save Teaching Profile
    ('', 'Save Teaching Profile', 'fa-floppy-disk'),
    # Notes for tutor
    ('', 'Notes for tutor', 'fa-sticky-note'),
    # Requirements before review
    ('', 'Requirements before review', 'fa-list-check'),
    # Uploaded file / document
    ('', 'Uploaded file', 'fa-file'),
    ('original_name', '', 'fa-file'),
    # Learning Resources
    ('', 'Learning Resources', 'fa-book-open'),
    # Pre-Recorded Courses
    ('', 'Pre-Recorded Courses', 'fa-video'),
    # Categories heading
    ('', 'Categories</h3>', 'fa-tags'),
    # Common Questions
    ('', 'Common Questions', 'fa-circle-question'),
    # Multi-Session Series
    ('', 'Multi-Session Series', 'fa-layer-group'),
    # Please fix the following errors
    ('', 'Please fix the following errors', 'fa-triangle-exclamation'),
    # error message div
    ('', '{{ session(\'error\') }}', 'fa-triangle-exclamation'),
    # Total Earnings stat
    ('stat-icon', '', 'fa-coins'),
    # No documents uploaded yet
    ('', 'No documents uploaded yet', 'fa-folder-open'),
    # Session Duration Pricing
    ('', 'Session Duration Pricing', 'fa-money-bill-wave'),
    # Category filter label
    ('f-label', 'Category', 'fa-filter'),
    # Active filter tag with category
    ('filter-tag', 'categories->find', 'fa-tag'),
    # IB curriculum tags
    ('curr-tag', 'IB MYP', 'fa-graduation-cap'),
    ('curr-tag', 'IB Diploma', 'fa-graduation-cap'),
    # FAQ chevron
    ('faq-chevron', '', 'fa-chevron-down'),
    # Cookie banner
    ('', 'We value your privacy', 'fa-cookie-bite'),
    # Nav dropdown chevron
    ('mw-uname', '', 'fa-chevron-down'),
    # Session X of Y / series
    ('', 'Session {{', 'fa-layer-group'),
    ('', 'session_number', 'fa-layer-group'),
    # Open status
    ('', 'Open</span>', 'fa-door-open'),
    # All sessions in this series
    ('', 'All sessions in this series', 'fa-list'),
    # My Sessions
    ('', 'My Sessions', 'fa-list'),
    # My Clinic Bookings
    ('', 'My Clinic Bookings', 'fa-calendar-check'),
    # Duration minutes
    ('', 'duration_minutes', 'fa-hourglass-half'),
    ('', 'min</span>', 'fa-hourglass-half'),
    ('', 'minutes</span>', 'fa-hourglass-half'),
    # lessons count
    ('', 'lessons</span>', 'fa-book'),
    # sessions count (tutor show)
    ('', 'sessions</strong>', 'fa-calendar'),
    # Responds within
    ('', 'Responds within', 'fa-clock'),
    # Level(s) You Teach
    ('', 'Level(s) You Teach', 'fa-layer-group'),
    # Hourly Rate
    ('', 'Hourly Rate', 'fa-money-bill'),
    # National ID
    ('', 'National ID', 'fa-id-card'),
    # Within 24 hours
    ('', 'Within 24 hours', 'fa-clock'),
    # Code: (session code)
    ('', 'Code:', 'fa-key'),
    # Message textarea label
    ('', 'Message</', 'fa-comment'),
    # Today badge
    ('', 'Today</span>', 'fa-calendar-day'),
    # Examiner dashboard status dot
    ('10B981', '', 'fa-circle'),
    # Empty state icons
    ('', 'No ', 'fa-inbox'),
]


def find_icon_from_context(content, match_start, match_end):
    """Determine the correct icon by looking at surrounding text."""
    # Get a window of text around the match
    before = content[max(0, match_start - 200):match_start]
    after = content[match_end:min(len(content), match_end + 200)]
    
    for rule in CONTEXT_RULES:
        search_before, search_after, icon = rule
        if search_before and search_before in before:
            return icon
        if search_after and search_after in after:
            return icon
    
    # Default: fa-circle-dot (generic but not a question mark)
    return 'fa-circle-info'


def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as fh:
        original = fh.read()
    
    content = original
    
    # First fix Blade variable patterns (Pattern 2 & 3)
    # Match: <span class="{{ $var }}" style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
    content = re.sub(
        r'<span class="(\{\{\s*\$[a-zA-Z_]+\s*\}\})"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>',
        r'<i class="fas \1"></i>',
        content
    )
    # Match: <span class="$var}}" style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
    # (missing opening brace - fix that too)
    content = re.sub(
        r'<span class="(\$[a-zA-Z_]+\}\})"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>',
        r'<i class="fas {{ \1"></i>',
        content
    )
    # Match: <span class="{{ $var }} ..." style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>
    # where there might be extra classes
    content = re.sub(
        r'<span class="(\{\{\s*\$[a-zA-Z_]+\s*\}\})\s+[^"]*"\s+style="font-size:inherit;"><i class="fas fa-question-circle"></i></span>',
        r'<i class="fas \1"></i>',
        content
    )
    
    # Now fix static placeholder patterns with context-aware replacement
    while STATIC_PATTERN in content:
        idx = content.index(STATIC_PATTERN)
        icon = find_icon_from_context(content, idx, idx + len(STATIC_PATTERN))
        replacement = f'<i class="fas {icon}"></i>'
        content = content[:idx] + replacement + content[idx + len(STATIC_PATTERN):]
    
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
