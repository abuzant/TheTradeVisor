#!/bin/bash

# Script to check all .md files for broken internal links
# Author: Ruslan Abuzant

echo "🔍 Checking all .md files for broken links..."
echo "=============================================="

BROKEN_LINKS=0
TOTAL_LINKS=0

# Find all .md files
while IFS= read -r mdfile; do
    # Extract all markdown links [text](path)
    while IFS= read -r link; do
        # Extract the path from the link
        path=$(echo "$link" | sed -n 's/.*(\([^)]*\)).*/\1/p')
        
        # Skip external URLs
        if [[ "$path" =~ ^https?:// ]] || [[ "$path" =~ ^mailto: ]] || [[ "$path" =~ ^# ]]; then
            continue
        fi
        
        TOTAL_LINKS=$((TOTAL_LINKS + 1))
        
        # Get directory of the md file
        mddir=$(dirname "$mdfile")
        
        # Resolve relative path
        if [[ "$path" =~ ^\.\. ]]; then
            # Relative path with ..
            fullpath="$mddir/$path"
        elif [[ "$path" =~ ^\. ]]; then
            # Relative path with .
            fullpath="$mddir/$path"
        elif [[ "$path" =~ ^/ ]]; then
            # Absolute path from root
            fullpath="$path"
        else
            # Relative path without ./
            fullpath="$mddir/$path"
        fi
        
        # Normalize path
        fullpath=$(realpath -m "$fullpath" 2>/dev/null || echo "$fullpath")
        
        # Check if file exists
        if [ ! -f "$fullpath" ] && [ ! -d "$fullpath" ]; then
            echo "❌ BROKEN: $mdfile"
            echo "   Link: $link"
            echo "   Path: $path"
            echo "   Resolved: $fullpath"
            echo ""
            BROKEN_LINKS=$((BROKEN_LINKS + 1))
        fi
    done < <(grep -o '\[.*\](.*\.md[^)]*)' "$mdfile" 2>/dev/null)
done < <(find . -name "*.md" -type f | grep -v node_modules | grep -v vendor)

echo "=============================================="
echo "📊 Summary:"
echo "   Total links checked: $TOTAL_LINKS"
echo "   Broken links found: $BROKEN_LINKS"

if [ $BROKEN_LINKS -eq 0 ]; then
    echo "✅ All links are valid!"
    exit 0
else
    echo "❌ Found $BROKEN_LINKS broken links!"
    exit 1
fi
