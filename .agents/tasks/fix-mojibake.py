import os
import glob

replacements = {
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\x8e': '??'.encode('utf-8'),  # magnifying glass
    b'\xc3\xb0\xc5\xb8\xc2\x96\xc2\xbc\xc3\xaf\xc2\xb8\xc2\x8f': '???'.encode('utf-8'),  # framed picture
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\xa1': '??'.encode('utf-8'),  # light bulb
    b'\xc3\xb0\xc5\xb8\xc2\x92\xc2\xa5': '??'.encode('utf-8'),  # collision
    b'\xc3\xa2\xc5\xa1\xc2\xa1': '?'.encode('utf-8'),  # high voltage
    b'\xc3\xb0\xc5\xb8\xc5\xbd\xc2\xaf': '??'.encode('utf-8'),  # dart
    b'\xc3\xb0\xc5\xb8\xc2\x86': '??'.encode('utf-8'),  # trophy
    b'\xc3\xa2\xcb\x9c\xc2\x85': '?'.encode('utf-8'),  # star
    b'\xc3\xa2\xc5\x93\xc2\x97': '?'.encode('utf-8'),  # ballot x
    b'\xc3\xa2\xc5\x93\xc2\x94': '?'.encode('utf-8'),  # check mark
    b'\xc2\xb8\xe2\x80\x99\xc2\xb3': '??'.encode('utf-8'),  # credit card
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\xa2\xc2\x80\xc5\x93': '?'.encode('utf-8'),  # em-dash
    b'\xc3\xa2\xc5\x92\xc2\xac\xc3\x82\xc2\xa6': '?'.encode('utf-8'),  # ellipsis
    b'\xc3\x83\xc2\x97': '?'.encode('utf-8'),  # multiplication
    b'\xc3\xa2\xc2\x94\xc2\x80': '?'.encode('utf-8'),  # box drawing
    b'\xc3\xa2\xc2\x82\xc2\xb9': '?'.encode('utf-8'),  # rupee
    b'\xc3\x82\xc2\xb0': '?'.encode('utf-8'),  # degree
    b'\xc3\x82\xc2\xa7': '?'.encode('utf-8'),  # section
    b'\xc3\xa2\xc2\xa2': '?'.encode('utf-8'),  # bullet
    b'\xc3\x82\xc2\xb2': '?'.encode('utf-8'),  # superscript 2
    b'\xc3\x82\xc2\xb3': '?'.encode('utf-8'),  # superscript 3
    b'\xc3\x82\xc2\xb9': '?'.encode('utf-8'),  # superscript 1
}

views_path = r'c:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views'
blade_files = glob.glob(os.path.join(views_path, '**', '*.blade.php'), recursive=True)

files_scanned = 0
files_changed = 0
total_replacements = 0

print(f"Found {len(blade_files)} .blade.php files\n")

for filepath in blade_files:
    files_scanned += 1
    with open(filepath, 'rb') as f:
        content = f.read()
    
    new_content = content
    file_changed = False
    
    for search, replace in replacements.items():
        count = new_content.count(search)
        if count > 0:
            new_content = new_content.replace(search, replace)
            total_replacements += count
            file_changed = True
    
    if file_changed:
        with open(filepath, 'wb') as f:
            f.write(new_content)
        files_changed += 1

print("=" * 60)
print("MOJIBAKE FIX COMPLETED")
print("=" * 60)
print(f"Files scanned:       {files_scanned}")
print(f"Files changed:       {files_changed}")
print(f"Total replacements:  {total_replacements}")
