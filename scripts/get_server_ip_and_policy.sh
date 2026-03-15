#!/bin/bash

echo "=========================================="
echo "AWS SES Security Recovery Helper"
echo "=========================================="
echo ""

# Get server's public IP
echo "Getting server's public IP address..."
SERVER_IP=$(curl -4 -s ifconfig.me)

if [ -z "$SERVER_IP" ]; then
    echo "ERROR: Could not determine server IP"
    exit 1
fi

echo "✓ Server IP: $SERVER_IP"
echo ""

# Get AWS account ID (if AWS CLI is configured)
echo "Attempting to get AWS Account ID..."
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text 2>/dev/null)

if [ -z "$AWS_ACCOUNT_ID" ]; then
    echo "⚠ AWS CLI not configured or not available"
    echo "  You'll need to replace ACCOUNT_ID manually in the policy"
    AWS_ACCOUNT_ID="ACCOUNT_ID"
else
    echo "✓ AWS Account ID: $AWS_ACCOUNT_ID"
fi
echo ""

# Generate IAM Policy
echo "=========================================="
echo "STEP 1: IAM Policy for IP-Restricted SES"
echo "=========================================="
echo ""
echo "Copy this policy when creating the new IAM user:"
echo ""

cat << EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowSESFromServerIPOnly",
            "Effect": "Allow",
            "Action": [
                "ses:SendEmail",
                "ses:SendRawEmail"
            ],
            "Resource": "arn:aws:ses:eu-north-1:${AWS_ACCOUNT_ID}:identity/thetradevisor.com",
            "Condition": {
                "IpAddress": {
                    "aws:SourceIp": "${SERVER_IP}/32"
                }
            }
        },
        {
            "Sid": "AllowSESIdentityVerification",
            "Effect": "Allow",
            "Action": [
                "ses:GetIdentityVerificationAttributes",
                "ses:VerifyDomainIdentity",
                "ses:VerifyDomainDkim"
            ],
            "Resource": "*"
        }
    ]
}
EOF

echo ""
echo ""
echo "=========================================="
echo "STEP 2: DNS Records to Add"
echo "=========================================="
echo ""
echo "After verifying your domain in SES, you'll receive DNS records."
echo "You'll need to add:"
echo "  1. TXT record for domain verification"
echo "  2. 3 CNAME records for DKIM"
echo "  3. SPF record (if not present):"
echo ""
echo "     Type: TXT"
echo "     Name: thetradevisor.com"
echo "     Value: v=spf1 include:amazonses.com ~all"
echo ""
echo "  4. DMARC record (recommended):"
echo ""
echo "     Type: TXT"
echo "     Name: _dmarc.thetradevisor.com"
echo "     Value: v=DMARC1; p=quarantine; rua=mailto:hello@thetradevisor.com"
echo ""

echo ""
echo "=========================================="
echo "STEP 3: Quick Reference"
echo "=========================================="
echo ""
echo "Server IP: $SERVER_IP"
echo "SES Region: eu-north-1 (Stockholm)"
echo "SMTP Endpoint: email-smtp.eu-north-1.amazonaws.com"
echo "SMTP Port: 587 (TLS)"
echo ""
echo "Compromised Access Keys to DELETE:"
echo "  - Check AWS IAM console for old access keys"
echo "  - Rotate any keys that were previously exposed"
echo ""

echo "=========================================="
echo "Next Steps:"
echo "=========================================="
echo ""
echo "1. Go to AWS IAM Console"
echo "2. Delete/deactivate the compromised access keys above"
echo "3. Create new IAM user: thetradevisor-ses-production"
echo "4. Attach the policy shown above"
echo "5. Generate new access keys"
echo "6. Update /vhosts/thetradevisor.com/.env with new credentials"
echo "7. Run: cd /vhosts/thetradevisor.com && php artisan config:clear"
echo ""
echo "Full guide: /vhosts/thetradevisor.com/AWS_SES_SECURITY_RECOVERY.md"
echo ""
