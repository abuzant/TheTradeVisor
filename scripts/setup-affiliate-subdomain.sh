#!/bin/bash

# Setup script for join.thetradevisor.com affiliate subdomain
# Run with sudo

set -e

echo "==================================="
echo "Affiliate Subdomain Setup Script"
echo "==================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Checking DNS configuration...${NC}"
echo "Please ensure join.thetradevisor.com is pointed to this server's IP in Cloudflare"
echo "Current server IP: $(curl -s ifconfig.me)"
read -p "Press Enter to continue once DNS is configured..."

echo ""
echo -e "${YELLOW}Step 2: Installing Certbot (if not already installed)...${NC}"
if ! command -v certbot &> /dev/null; then
    apt-get update
    apt-get install -y certbot python3-certbot-nginx
    echo -e "${GREEN}Certbot installed${NC}"
else
    echo -e "${GREEN}Certbot already installed${NC}"
fi

echo ""
echo -e "${YELLOW}Step 3: Obtaining SSL certificate...${NC}"
echo "This will request a certificate from Let's Encrypt"
echo ""

# Stop nginx temporarily
systemctl stop nginx

# Obtain certificate
certbot certonly --standalone \
    --non-interactive \
    --agree-tos \
    --email admin@thetradevisor.com \
    -d join.thetradevisor.com

if [ $? -eq 0 ]; then
    echo -e "${GREEN}SSL certificate obtained successfully${NC}"
else
    echo -e "${RED}Failed to obtain SSL certificate${NC}"
    echo "Please check:"
    echo "1. DNS is properly configured"
    echo "2. Port 80 is accessible"
    echo "3. No firewall blocking"
    systemctl start nginx
    exit 1
fi

echo ""
echo -e "${YELLOW}Step 4: Configuring Nginx...${NC}"

# Copy nginx config
cp /var/www/thetradevisor.com/nginx-affiliate-subdomain.conf /etc/nginx/sites-available/join.thetradevisor.com

# Create symlink
ln -sf /etc/nginx/sites-available/join.thetradevisor.com /etc/nginx/sites-enabled/

# Test nginx configuration
nginx -t

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Nginx configuration valid${NC}"
else
    echo -e "${RED}Nginx configuration error${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Step 5: Starting Nginx...${NC}"
systemctl start nginx
systemctl reload nginx

echo ""
echo -e "${YELLOW}Step 6: Setting up auto-renewal...${NC}"

# Create renewal hook
cat > /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh << 'EOF'
#!/bin/bash
systemctl reload nginx
EOF

chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh

# Test renewal
certbot renew --dry-run

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Auto-renewal configured successfully${NC}"
else
    echo -e "${YELLOW}Warning: Auto-renewal test failed${NC}"
fi

echo ""
echo -e "${YELLOW}Step 7: Configuring Cloudflare...${NC}"
echo "Please ensure the following Cloudflare settings:"
echo "1. SSL/TLS mode: Full (strict)"
echo "2. Always Use HTTPS: ON"
echo "3. Automatic HTTPS Rewrites: ON"
echo "4. Minimum TLS Version: 1.2"
echo ""
read -p "Press Enter once Cloudflare is configured..."

echo ""
echo -e "${GREEN}==================================="
echo "Setup Complete!"
echo "===================================${NC}"
echo ""
echo "Subdomain: https://join.thetradevisor.com"
echo "SSL Certificate: Valid"
echo "Auto-renewal: Configured"
echo ""
echo "Testing URLs:"
echo "- https://join.thetradevisor.com/offers/test"
echo "- https://join.thetradevisor.com/affiliate/login"
echo "- https://join.thetradevisor.com/affiliate/register"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Test affiliate tracking link"
echo "2. Test affiliate login/register"
echo "3. Monitor logs: tail -f /var/log/nginx/join.thetradevisor.com-access.log"
echo ""
