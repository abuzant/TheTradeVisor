#!/bin/bash

# GitHub Release Creation Script for TheTradeVisor v1.0.0
# This script creates a GitHub release using the GitHub CLI

echo "🚀 Creating GitHub Release for TheTradeVisor v1.0.0"
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
echo "📦 Creating release v1.0.0..."
gh release create v1.0.0 \
    --title "🎉 TheTradeVisor v1.0.0 - First Official Release" \
    --notes-file RELEASE_NOTES_v1.0.0.md \
    --latest

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Release v1.0.0 created successfully!"
    echo "🌐 View it at: https://github.com/abuzant/TheTradeVisor/releases/tag/v1.0.0"
else
    echo ""
    echo "❌ Failed to create release"
    echo "You can create it manually at: https://github.com/abuzant/TheTradeVisor/releases/new"
fi
