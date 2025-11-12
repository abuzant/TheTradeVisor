#!/bin/bash

# Fix ALL credits in ALL .md files to exact format

EXACT_CREDITS='---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)'

# Find all .md files
find /www/docs -type f -name "*.md" | while read file; do
    # Remove any existing Author & Contact section (everything after the last ---)
    # Find the last occurrence of "## 👨‍💻 Author" or "## Author" and remove from there to end
    if grep -q "## 👨‍💻 Author\|## Author & Contact" "$file"; then
        # Create temp file with everything before Author section
        sed '/## 👨‍💻 Author\|## Author & Contact/,$d' "$file" > "${file}.tmp"
        # Remove trailing empty lines
        sed -i -e :a -e '/^\s*$/d;N;ba' "${file}.tmp"
        # Add exact credits
        echo "$EXACT_CREDITS" >> "${file}.tmp"
        # Replace original
        mv "${file}.tmp" "$file"
        echo "✅ Fixed: $file"
    else
        # No author section, just append
        # Remove trailing empty lines first
        sed -i -e :a -e '/^\s*$/d;N;ba' "$file"
        echo "$EXACT_CREDITS" >> "$file"
        echo "✅ Added: $file"
    fi
done

echo ""
echo "✅ ALL FILES FIXED WITH EXACT FORMAT!"
