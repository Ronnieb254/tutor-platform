import os, re

root = r"resources\views"

# Map every emoji found to its FA icon replacement
# Sorted longest-first to avoid partial matches
replacements = [
    # Wrapped in span
    ('<span style="font-size:inherit;">\u26a1</span>',   '<i class="fas fa-bolt"></i>'),
    ('<span style="font-size:inherit;">\U0001f3af</span>','<i class="fas fa-bullseye"></i>'),
    ('<span style="font-size:inherit;">\U0001f3c6</span>','<i class="fas fa-trophy"></i>'),
    ('<span style="font-size:inherit;">\U0001f4da</span>','<i class="fas fa-book-open"></i>'),
    ('<span style="font-size:inherit;">\U0001f393</span>','<i class="fas fa-graduation-cap"></i>'),
    ('<span style="font-size:inherit;">\U0001f4ca</span>','<i class="fas fa-chart-bar"></i>'),
    ('<span style="font-size:inherit;">\u2b50</span>',   '<i class="fas fa-star"></i>'),
    ('<span style="font-size:inherit;">\u2705</span>',   '<i class="fas fa-check-circle"></i>'),
    ('<span style="font-size:inherit;">\u274c</span>',   '<i class="fas fa-times-circle"></i>'),
    ('<span style="font-size:inherit;">\U0001f512</span>','<i class="fas fa-lock"></i>'),
    ('<span style="font-size:inherit;">\U0001f4c5</span>','<i class="fas fa-calendar"></i>'),
    ('<span style="font-size:inherit;">\u23f0</span>',   '<i class="fas fa-clock"></i>'),
    ('<span style="font-size:inherit;">\U0001f464</span>','<i class="fas fa-user"></i>'),
    ('<span style="font-size:inherit;">\U0001f465</span>','<i class="fas fa-users"></i>'),
    ('<span style="font-size:inherit;">\U0001f514</span>','<i class="fas fa-bell"></i>'),
    ('<span style="font-size:inherit;">\U0001f4ac</span>','<i class="fas fa-comments"></i>'),
    ('<span style="font-size:inherit;">\U0001f4c4</span>','<i class="fas fa-file-alt"></i>'),
    ('<span style="font-size:inherit;">\U0001f5d1\ufe0f</span>','<i class="fas fa-trash"></i>'),
    ('<span style="font-size:inherit;">\u270f\ufe0f</span>','<i class="fas fa-pencil-alt"></i>'),
    ('<span style="font-size:inherit;">\U0001f50d</span>','<i class="fas fa-search"></i>'),
    ('<span style="font-size:inherit;">\U0001f4cd</span>','<i class="fas fa-map-marker-alt"></i>'),
    ('<span style="font-size:inherit;">\U0001f310</span>','<i class="fas fa-globe"></i>'),
    ('<span style="font-size:inherit;">\U0001f4e4</span>','<i class="fas fa-upload"></i>'),
    ('<span style="font-size:inherit;">\U0001f4e5</span>','<i class="fas fa-download"></i>'),
    ('<span style="font-size:inherit;">\U0001f3a5</span>','<i class="fas fa-video"></i>'),
    ('<span style="font-size:inherit;">\U0001f4c1</span>','<i class="fas fa-folder"></i>'),
    ('<span style="font-size:inherit;">\u2795</span>',   '<i class="fas fa-plus"></i>'),
    ('<span style="font-size:inherit;">\u2796</span>',   '<i class="fas fa-minus"></i>'),
    ('<span style="font-size:inherit;">\U0001f6aa</span>','<i class="fas fa-sign-out-alt"></i>'),
    ('<span style="font-size:inherit;">\u2139\ufe0f</span>','<i class="fas fa-info-circle"></i>'),
    ('<span style="font-size:inherit;">\u26a0\ufe0f</span>','<i class="fas fa-exclamation-triangle"></i>'),
    ('<span style="font-size:inherit;">\U0001f6e1\ufe0f</span>','<i class="fas fa-shield-alt"></i>'),
    ('<span style="font-size:inherit;">\U0001f4dc</span>','<i class="fas fa-scroll"></i>'),
    ('<span style="font-size:inherit;">\U0001f504</span>','<i class="fas fa-redo"></i>'),
    ('<span style="font-size:inherit;">\U0001f3a7</span>','<i class="fas fa-headset"></i>'),
    ('<span style="font-size:inherit;">\U0001f4b3</span>','<i class="fas fa-credit-card"></i>'),
    ('<span style="font-size:inherit;">\u2709\ufe0f</span>','<i class="fas fa-envelope"></i>'),
    ('<span style="font-size:inherit;">\U0001f4de</span>','<i class="fas fa-phone"></i>'),
    ('<span style="font-size:inherit;">\u2764\ufe0f</span>','<i class="fas fa-heart"></i>'),

    # Bare emojis (all discovered in the scan)
    ('\U0001f4d6',  '<i class="fas fa-book-open"></i>'),        # 📖
    ('\U0001f511',  '<i class="fas fa-key"></i>'),              # 🔑
    ('\U0001f4f1',  '<i class="fas fa-mobile-alt"></i>'),       # 📱
    ('\u23f3',      '<i class="fas fa-hourglass-half"></i>'),   # ⏳
    ('\u2753',      '<i class="fas fa-question-circle"></i>'),  # ❓
    ('\U0001f517',  '<i class="fas fa-link"></i>'),             # 🔗
    ('\U0001f3eb',  '<i class="fas fa-school"></i>'),           # 🏫
    ('\U0001f468',  '<i class="fas fa-user"></i>'),             # 👨
    ('\U0001f6ab',  '<i class="fas fa-ban"></i>'),              # 🚫
    ('\U0001f3f7',  '<i class="fas fa-tag"></i>'),              # 🏷
    ('\U0001f4bb',  '<i class="fas fa-laptop"></i>'),           # 💻
    ('\U0001f4b5',  '<i class="fas fa-money-bill"></i>'),       # 💵
    ('\U0001f4f7',  '<i class="fas fa-camera"></i>'),           # 📷
    ('\U0001f4cb',  '<i class="fas fa-clipboard"></i>'),        # 📋
    ('\U0001f4cf',  '<i class="fas fa-ruler"></i>'),            # 📏
    ('\U0001f4da',  '<i class="fas fa-book-open"></i>'),        # 📚
    ('\U0001f9ea',  '<i class="fas fa-flask"></i>'),            # 🧪
    ('\u270f',      '<i class="fas fa-pencil-alt"></i>'),       # ✏
    ('\U0001f4d4',  '<i class="fas fa-book"></i>'),             # 📔
    ('\U0001f50e',  '<i class="fas fa-search-plus"></i>'),      # 🔎
    ('\U0001f512',  '<i class="fas fa-lock"></i>'),             # 🔒
    ('\U0001f441',  '<i class="fas fa-eye"></i>'),              # 👁
    ('\U0001f6e1',  '<i class="fas fa-shield-alt"></i>'),       # 🛡
    ('\U0001fa7a',  '<i class="fas fa-id-card"></i>'),          # 🪪
    ('\U0001f4dc',  '<i class="fas fa-scroll"></i>'),           # 📜
    ('\U0001f393',  '<i class="fas fa-graduation-cap"></i>'),   # 🎓
    ('\U0001f30d',  '<i class="fas fa-globe"></i>'),            # 🌍
    ('\U0001f6a9',  '<i class="fas fa-flag"></i>'),             # 🚩
    ('\U0001f3c5',  '<i class="fas fa-medal"></i>'),            # 🏅
    ('\U0001f454',  '<i class="fas fa-user-tie"></i>'),         # 👔
    ('\U0001f4c4',  '<i class="fas fa-file-alt"></i>'),         # 📄
    ('\U0001f4e1',  '<i class="fas fa-satellite-dish"></i>'),   # 📡
    ('\U0001f4a1',  '<i class="fas fa-lightbulb"></i>'),        # 💡
    ('\u25b6',      '<i class="fas fa-play"></i>'),             # ▶
    ('\u2611',      '<i class="fas fa-check-square"></i>'),     # ☑
    ('\u2714',      '<i class="fas fa-check"></i>'),            # ✔
    ('\u2716',      '<i class="fas fa-times"></i>'),            # ✖
    ('\u2728',      '<i class="fas fa-sparkles fa-xs"></i>'),   # ✨
    ('\u2642',      ''),                                        # ♂ (male sign, remove)
    ('\U0001f5a8',  '<i class="fas fa-print"></i>'),            # 🖨
    ('\u2630',      '<i class="fas fa-bars"></i>'),             # ☰
    ('\U0001f45b',  '<i class="fas fa-wallet"></i>'),           # 👛
    ('\u2b50',      '<i class="fas fa-star"></i>'),             # ⭐
    ('\u2705',      '<i class="fas fa-check-circle"></i>'),     # ✅
    ('\u274c',      '<i class="fas fa-times-circle"></i>'),     # ❌
    ('\u26a1',      '<i class="fas fa-bolt"></i>'),             # ⚡
    ('\U0001f3af',  '<i class="fas fa-bullseye"></i>'),         # 🎯
    ('\U0001f3c6',  '<i class="fas fa-trophy"></i>'),           # 🏆
    ('\U0001f4c5',  '<i class="fas fa-calendar"></i>'),         # 📅
    ('\u23f0',      '<i class="fas fa-clock"></i>'),            # ⏰
    ('\U0001f514',  '<i class="fas fa-bell"></i>'),             # 🔔
    ('\U0001f4b3',  '<i class="fas fa-credit-card"></i>'),      # 💳
    ('\u2709',      '<i class="fas fa-envelope"></i>'),         # ✉
    ('\U0001f4de',  '<i class="fas fa-phone"></i>'),            # 📞
    ('\u2764',      '<i class="fas fa-heart"></i>'),            # ❤
    ('\U0001f4ac',  '<i class="fas fa-comments"></i>'),         # 💬
    ('\U0001f6aa',  '<i class="fas fa-sign-out-alt"></i>'),     # 🚪
    ('\u26a0',      '<i class="fas fa-exclamation-triangle"></i>'), # ⚠
    ('\u2139',      '<i class="fas fa-info-circle"></i>'),      # ℹ
    ('\U0001f504',  '<i class="fas fa-redo"></i>'),             # 🔄
    ('\U0001f464',  '<i class="fas fa-user"></i>'),             # 👤
    ('\U0001f465',  '<i class="fas fa-users"></i>'),            # 👥
    ('\ufe0f',      ''),  # variation selector — remove leftovers
]

total = 0
for dirpath, _, files in os.walk(root):
    for fn in files:
        if not fn.endswith('.blade.php'):
            continue
        path = os.path.join(dirpath, fn)
        try:
            with open(path, 'r', encoding='utf-8') as fh:
                content = fh.read()
            original = content
            for (emoji, fa) in replacements:
                content = content.replace(emoji, fa)
            if content != original:
                with open(path, 'w', encoding='utf-8') as fh:
                    fh.write(content)
                total += 1
                print(f"Updated: {fn}")
        except Exception as e:
            print(f"Error {fn}: {e}")

print(f"\nTotal files updated: {total}")
