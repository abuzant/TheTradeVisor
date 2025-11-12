#!/bin/bash

# Create GitHub Issue for System Crash Incident
# Usage: ./create_github_issue.sh [GITHUB_TOKEN]

GITHUB_TOKEN="${1:-$GITHUB_TOKEN}"
REPO_OWNER="abuzant"
REPO_NAME="TheTradeVisor"

if [ -z "$GITHUB_TOKEN" ]; then
    echo "Error: GitHub token required"
    echo "Usage: ./create_github_issue.sh YOUR_GITHUB_TOKEN"
    echo "Or set GITHUB_TOKEN environment variable"
    exit 1
fi

# Read the issue template
ISSUE_BODY=$(cat /www/docs/GITHUB_ISSUE_TEMPLATE.md)

# Create JSON payload
PAYLOAD=$(jq -n \
  --arg title "🔴 CRITICAL: System Crash Due to Unbounded Database Queries - RESOLVED" \
  --arg body "$ISSUE_BODY" \
  --argjson labels '["bug", "critical", "resolved", "incident-report"]' \
  '{
    title: $title,
    body: $body,
    labels: $labels
  }')

# Create the issue
RESPONSE=$(curl -X POST \
  -H "Authorization: token $GITHUB_TOKEN" \
  -H "Accept: application/vnd.github.v3+json" \
  -H "Content-Type: application/json" \
  -d "$PAYLOAD" \
  "https://api.github.com/repos/$REPO_OWNER/$REPO_NAME/issues" \
  2>/dev/null)

# Check if successful
ISSUE_URL=$(echo "$RESPONSE" | jq -r '.html_url // empty')

if [ -n "$ISSUE_URL" ]; then
    echo "✅ GitHub issue created successfully!"
    echo "URL: $ISSUE_URL"
    echo ""
    echo "Issue Number: $(echo "$RESPONSE" | jq -r '.number')"
    echo "State: $(echo "$RESPONSE" | jq -r '.state')"
    exit 0
else
    echo "❌ Failed to create GitHub issue"
    echo "Response: $RESPONSE"
    exit 1
fi
