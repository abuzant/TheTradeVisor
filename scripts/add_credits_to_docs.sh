#!/bin/bash

# Add author credits to all .md files in /vhosts/thetradevisor.com/docs

CREDITS='

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
'

# Find all .md files in /vhosts/thetradevisor.com/docs
find /vhosts/thetradevisor.com/docs -type f -name "*.md" | while read file; do
    # Check if credits already exist
    if ! grep -q "Ruslan Abuzant" "$file"; then
        # Remove any existing author sections
        sed -i '/^## 👨‍💻 Author/,/^$/d' "$file"
        
        # Add credits at the end
        echo "$CREDITS" >> "$file"
        echo "✅ Added credits to: $file"
    else
        echo "⏭️  Credits already in: $file"
    fi
done

echo ""
echo "✅ Credits added to all documentation files!"
