#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Replace Font Awesome icons with emoji equivalents in blade templates"""

import re
import os
from pathlib import Path

# Icon mapping (order matters - more specific patterns first)
ICON_MAP = {
    'fa-calendar-xmark': '❌',
    'fa-calendar-check': '✅',
    'fa-calendar-days': '📅',
    'fa-calendar': '📅',
    'fa-clock': '⏰',
    'fa-user-graduate': '🎓',
    'fa-user-secret': '🕵️',
    'fa-user-tie': '👔',
    'fa-chalkboard-user': '👨‍🏫',
    'fa-user': '👤',
    'fa-users': '👥',
    'fa-envelope': '✉️',
    'fa-phone': '📞',
    'fa-book-open': '📖',
    'fa-book': '📚',
    'fa-graduation-cap': '🎓',
    'fa-star': '⭐',
    'fa-heart': '❤️',
    'fa-circle-check': '✅',
    'fa-check': '✅',
    'fa-circle-xmark': '❌',
    'fa-xmark': '❌',
    'fa-times': '❌',
    'fa-lock': '🔒',
    'fa-unlock': '🔓',
    'fa-shield-halved': '🛡️',
    'fa-shield': '🛡️',
    'fa-certificate': '📜',
    'fa-chart-line': '📈',
    'fa-chart-bar': '📊',
    'fa-chart': '📊',
    'fa-bolt': '⚡',
    'fa-rotate-left': '🔄',
    'fa-rotate-right': '🔄',
    'fa-comments': '💬',
    'fa-comment': '💬',
    'fa-bell': '🔔',
    'fa-headset': '🎧',
    'fa-credit-card': '💳',
    'fa-gauge-high': '📊',
    'fa-gauge': '📊',
    'fa-magnifying-glass': '🔍',
    'fa-search': '🔍',
    'fa-location-dot': '📍',
    'fa-map-marker': '📍',
    'fa-globe': '🌐',
    'fa-upload': '📤',
    'fa-download': '📥',
    'fa-video': '🎥',
    'fa-file-pdf': '📄',
    'fa-file': '📄',
    'fa-folder-open': '📂',
    'fa-folder': '📁',
    'fa-trash': '🗑️',
    'fa-pencil': '✏️',
    'fa-pen': '✏️',
    'fa-edit': '✏️',
    'fa-plus': '➕',
    'fa-minus': '➖',
    'fa-right-from-bracket': '🚪',
    'fa-sign-out': '🚪',
    'fa-arrow-right': '→',
    'fa-arrow-left': '←',
    'fa-arrow-up': '↑',
    'fa-arrow-down': '↓',
    'fa-circle-info': 'ℹ️',
    'fa-info-circle': 'ℹ️',
    'fa-info': 'ℹ️',
    'fa-triangle-exclamation': '⚠️',
    'fa-exclamation-triangle': '⚠️',
    'fa-exclamation-circle': '⚠️',
    'fa-home': '🏠',
    'fa-house': '🏠',
    'fa-settings': '⚙️',
    'fa-cog': '⚙️',
    'fa-gear': '⚙️',
    'fa-bars': '☰',
    'fa-list': '📋',
    'fa-grid': '⊞',
    'fa-table': '📋',
    'fa-money-bill': '💵',
    'fa-wallet': '👛',
    'fa-bank': '🏦',
    'fa-building': '🏢',
    'fa-briefcase': '💼',
    'fa-trophy': '🏆',
    'fa-award': '🏅',
    'fa-medal': '🥇',
    'fa-flag': '🚩',
    'fa-tag': '🏷️',
    'fa-tags': '🏷️',
    'fa-key': '🔑',
    'fa-link': '🔗',
    'fa-share': '📤',
    'fa-print': '🖨️',
    'fa-image': '🖼️',
    'fa-camera': '📷',
    'fa-microphone': '🎤',
    'fa-wifi': '📶',
    'fa-desktop': '🖥️',
    'fa-laptop': '💻',
    'fa-mobile': '📱',
    'fa-spinner': '⏳',
    'fa-circle-notch': '⏳',
    'fa-eye': '👁️',
    'fa-eye-slash': '🙈',
    'fa-ban': '🚫',
    'fa-power-off': '⏻',
    'fa-question-circle': '❓',
    'fa-question': '❓',
    'fa-thumbs-up': '👍',
    'fa-thumbs-down': '👎',
    'fa-robot': '🤖',
    'fa-language': '🌐',
    'fa-paint-brush': '🎨',
    'fa-lightbulb': '💡',
    'fa-fire': '🔥',
    'fa-wand-magic-sparkles': '✨',
    'fa-magic': '✨',
}

def get_icon_emoji(class_string):
    """Find the appropriate emoji for a given class string"""
    for pattern in ICON_MAP.keys():
        if pattern in class_string:
            return ICON_MAP[pattern]
    return '❓'  # Default fallback

def replace_fa_icon(match):
    """Replace a single Font Awesome icon with emoji"""
    full_match = match.group(0)
    class_attr = match.group(1)
    other_attrs = match.group(2) if match.lastindex >= 2 else ''
    
    # Check if this has dynamic icon classes ({{ }})
    has_blade_var = '{{' in class_attr or '{{' in full_match
    
    # If it has blade variables for the icon itself, we need to handle it differently
    # For example: <i class="fas {{ $icon }}">
    # We can't replace this statically - skip it
    if has_blade_var and re.search(r'\{\{\s*\$\w+', class_attr):
        # Dynamic icon - we can't replace this statically
        return full_match
    
    # Get the emoji
    emoji = get_icon_emoji(class_attr)
    
    # Extract non-fa classes (but preserve Blade expressions)
    class_parts = class_attr.split()
    classes = []
    for c in class_parts:
        if not c.startswith(('fa-', 'fas', 'far', 'fab', 'fal', 'fat', 'fad')):
            classes.append(c)
    
    # Check if this is a large decorative icon
    is_large = False
    if class_attr:
        is_large = any('text-4xl' in c or 'text-5xl' in c or 'text-6xl' in c for c in class_attr.split())
    if other_attrs and isinstance(other_attrs, str):
        is_large = is_large or 'text-4xl' in other_attrs or 'text-5xl' in other_attrs or 'text-6xl' in other_attrs
    
    # Preserve other attributes (x-show, onclick, etc.)
    preserved_attrs = other_attrs.strip() if other_attrs else ''
    
    if is_large:
        # Large decorative icon - use div
        class_str = f' class="{" ".join(classes)}"' if classes else ' class="text-4xl mb-3"'
        attrs_str = f' {preserved_attrs}' if preserved_attrs else ''
        return f'<div{class_str}{attrs_str}>{emoji}</div>'
    else:
        # Inline icon - use span
        class_str = f' class="{" ".join(classes)}"' if classes else ''
        attrs_str = f' {preserved_attrs}' if preserved_attrs else ''
        return f'<span{class_str} style="font-size:inherit;"{attrs_str}>{emoji}</span>'

def process_file(file_path):
    """Process a single blade file"""
    try:
        # Read file as UTF-8
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Pattern to match <i> tags with Font Awesome classes
        # More flexible pattern to catch various FA formats including those with x-show, onclick, etc.
        pattern = r'<i\s+class="([^"]*(?:fa-solid|fa-regular|fa-brands|fa-light|fa-thin|fa-duotone|fas|far|fab|fal|fat)[^"]*)"([^>]*)></i>'
        
        # Replace all matches
        content = re.sub(pattern, replace_fa_icon, content)
        
        # Check if content changed
        if content != original_content:
            # Write back as UTF-8
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        return False
    except Exception as e:
        print(f"Error processing {file_path}: {e}")
        return False

def main():
    """Main execution"""
    base_path = Path(r'c:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views')
    
    # Find all blade files
    blade_files = list(base_path.rglob('*.blade.php'))
    
    print(f"Found {len(blade_files)} blade files to process...")
    
    processed_count = 0
    changed_count = 0
    
    for file_path in blade_files:
        processed_count += 1
        print(f"[{processed_count}/{len(blade_files)}] Processing: {file_path.name}", end='')
        
        if process_file(file_path):
            changed_count += 1
            print(" - CHANGED")
        else:
            print(" - no changes")
    
    print(f"\nProcessing complete!")
    print(f"Files processed: {processed_count}")
    print(f"Files changed: {changed_count}")

if __name__ == '__main__':
    main()
