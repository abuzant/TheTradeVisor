#!/bin/bash

# Script to check AWS EC2 CPU credit balance
# This helps prevent future crashes due to CPU credit exhaustion

INSTANCE_ID=$(curl -s http://169.254.169.254/latest/meta-data/instance-id)
REGION=$(curl -s http://169.254.169.254/latest/meta-data/placement/region)

echo "=== CPU Credit Status Check ==="
echo "Instance ID: $INSTANCE_ID"
echo "Region: $REGION"
echo "Time: $(date)"
echo ""

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    echo "ERROR: AWS CLI is not installed. Please install it:"
    echo "  sudo apt-get install awscli"
    exit 1
fi

# Get CPU credit balance (last 5 minutes)
echo "Fetching CPU credit balance..."
aws cloudwatch get-metric-statistics \
    --namespace AWS/EC2 \
    --metric-name CPUCreditBalance \
    --dimensions Name=InstanceId,Value=$INSTANCE_ID \
    --start-time $(date -u -d '5 minutes ago' +%Y-%m-%dT%H:%M:%S) \
    --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
    --period 300 \
    --statistics Average \
    --region $REGION \
    --output table

echo ""
echo "Fetching CPU credit usage..."
aws cloudwatch get-metric-statistics \
    --namespace AWS/EC2 \
    --metric-name CPUCreditUsage \
    --dimensions Name=InstanceId,Value=$INSTANCE_ID \
    --start-time $(date -u -d '5 minutes ago' +%Y-%m-%dT%H:%M:%S) \
    --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
    --period 300 \
    --statistics Average \
    --region $REGION \
    --output table

echo ""
echo "=== Current System Status ==="
uptime
echo ""
free -h
echo ""
echo "Top CPU consumers:"
ps aux --sort=-%cpu | head -10

echo ""
echo "=== Recommendations ==="
echo "- If CPU credit balance < 100: Consider enabling T3 Unlimited mode"
echo "- If CPU credit balance < 50: URGENT - System may become unresponsive soon"
echo "- If CPU credit balance = 0: System is throttled, consider immediate upgrade"
