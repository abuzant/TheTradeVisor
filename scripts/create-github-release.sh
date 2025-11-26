#!/bin/bash

# GitHub Release Creation Script for TheTradeVisor v2.1.0
# This script creates a GitHub release using the GitHub CLI

echo "🚀 Creating GitHub Release for TheTradeVisor v2.1.0"
echo "=================================================="
echo ""

# Check if gh is installed
if ! command -v gh &> /dev/null; then
    echo "❌ GitHub CLI (gh) is not installed"
    echo "Install it with: sudo apt install gh"
    exit 1
fi

# Check if authenticated
if ! gh auth status &> /dev/null; then
    echo "🔐 GitHub CLI is not authenticated. Starting authentication..."
    echo ""
    gh auth login
fi

# Create the release
echo ""
echo "📦 Creating release v2.1.0..."
gh release create v2.1.0 \
    --title "🎉 TheTradeVisor v2.1.0 - Latest Release" \
    --notes-file RELEASE_NOTES_v2.1.0.md \
    --latest

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Release v2.1.0 created successfully!"
    echo "🌐 View it at: https://github.com/abuzant/TheTradeVisor/releases/tag/v2.1.0"
else
    echo ""
    echo "❌ Failed to create release"
    echo "You can create it manually at: https://github.com/abuzant/TheTradeVisor/releases/new"
fi
