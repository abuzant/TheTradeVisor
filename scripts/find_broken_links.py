#!/usr/bin/env python3
"""
Find all broken internal links in markdown files
Author: Ruslan Abuzant
"""

import os
import re
from pathlib import Path
from collections import defaultdict

def find_md_files(root_dir):
    """Find all .md files"""
    md_files = []
    for root, dirs, files in os.walk(root_dir):
        # Skip node_modules and vendor
        dirs[:] = [d for d in dirs if d not in ['node_modules', 'vendor', '.git']]
        for file in files:
            if file.endswith('.md'):
                md_files.append(os.path.join(root, file))
    return md_files

def extract_links(content):
    """Extract all markdown links"""
    # Pattern for [text](link)
    pattern = r'\[([^\]]+)\]\(([^\)]+)\)'
    return re.findall(pattern, content)

def is_external_link(link):
    """Check if link is external"""
    return link.startswith(('http://', 'https://', 'mailto:', '#'))

def resolve_link(md_file_path, link):
    """Resolve relative link to absolute path"""
    if link.startswith('/'):
        # Absolute from project root
        return os.path.join('/vhosts/thetradevisor.com', link.lstrip('/'))
    
    # Remove anchor
    link_without_anchor = link.split('#')[0]
    if not link_without_anchor:
        # Pure anchor link
        return None
    
    # Relative to current file
    md_dir = os.path.dirname(md_file_path)
    resolved = os.path.normpath(os.path.join(md_dir, link_without_anchor))
    return resolved

def main():
    root_dir = '/vhosts/thetradevisor.com'
    
    print("🔍 Finding all .md files...")
    md_files = find_md_files(root_dir)
    print(f"Found {len(md_files)} .md files\n")
    
    broken_links = defaultdict(list)
    total_links = 0
    broken_count = 0
    
    for md_file in sorted(md_files):
        try:
            with open(md_file, 'r', encoding='utf-8') as f:
                content = f.read()
        except Exception as e:
            print(f"⚠️  Error reading {md_file}: {e}")
            continue
        
        links = extract_links(content)
        
        for text, link in links:
            if is_external_link(link):
                continue
            
            total_links += 1
            resolved = resolve_link(md_file, link)
            
            if resolved and not os.path.exists(resolved):
                broken_count += 1
                broken_links[md_file].append({
                    'text': text,
                    'link': link,
                    'resolved': resolved
                })
    
    # Print results
    if broken_links:
        print("❌ BROKEN LINKS FOUND:\n")
        print("=" * 80)
        
        for md_file, links in sorted(broken_links.items()):
            rel_path = os.path.relpath(md_file, root_dir)
            print(f"\n📄 {rel_path}")
            print("-" * 80)
            for link_info in links:
                print(f"   Text: {link_info['text']}")
                print(f"   Link: {link_info['link']}")
                print(f"   Resolved: {link_info['resolved']}")
                print()
    
    print("=" * 80)
    print(f"\n📊 SUMMARY:")
    print(f"   Total .md files: {len(md_files)}")
    print(f"   Total internal links: {total_links}")
    print(f"   Broken links: {broken_count}")
    print(f"   Files with broken links: {len(broken_links)}")
    
    if broken_count == 0:
        print("\n✅ All links are valid!")
        return 0
    else:
        print(f"\n❌ Found {broken_count} broken links in {len(broken_links)} files!")
        return 1

if __name__ == '__main__':
    exit(main())
