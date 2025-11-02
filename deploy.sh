#!/bin/bash

# License Admin Panel Deployment Script
# Usage: ./deploy.sh

echo "🚀 License Admin Panel Deployment Starting..."

# Configuration
SERVER_HOST="your-server.com"
SERVER_USER="your-username"
SERVER_PUBLIC_PATH="/home/metamondes.com/public_html/license"
SERVER_APP_PATH="/home/metamondes.com/license-admin"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}📋 Deployment Configuration:${NC}"
echo "   Server: $SERVER_HOST"
echo "   Public Path: $SERVER_PUBLIC_PATH"
echo "   App Path: $SERVER_APP_PATH"
echo ""

# Step 1: Build and prepare files
echo -e "${YELLOW}📦 Step 1: Preparing files for deployment...${NC}"

# Install dependencies (production only)
composer install --no-dev --optimize-autoloader --no-interaction

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Composer install failed${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Dependencies installed${NC}"

# Step 2: Create deployment package
echo -e "${YELLOW}📦 Step 2: Creating deployment package...${NC}"

# Create temporary deployment directory
DEPLOY_DIR="deploy-$(date +%Y%m%d-%H%M%S)"
mkdir -p $DEPLOY_DIR/app-files
mkdir -p $DEPLOY_DIR/public-files

# Copy application files (excluding public)
rsync -av --exclude='public/' --exclude='node_modules/' --exclude='.git/' --exclude='deploy-*/' . $DEPLOY_DIR/app-files/

# Copy public files
cp -r public/* $DEPLOY_DIR/public-files/

# Copy production .htaccess
cp public/.htaccess-production $DEPLOY_DIR/public-files/.htaccess

echo -e "${GREEN}✅ Deployment package created: $DEPLOY_DIR${NC}"

# Step 3: Upload files
echo -e "${YELLOW}🚀 Step 3: Uploading to server...${NC}"

echo "Uploading application files..."
scp -r $DEPLOY_DIR/app-files/* $SERVER_USER@$SERVER_HOST:$SERVER_APP_PATH/

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Failed to upload application files${NC}"
    exit 1
fi

echo "Uploading public files..."
scp -r $DEPLOY_DIR/public-files/* $SERVER_USER@$SERVER_HOST:$SERVER_PUBLIC_PATH/

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Failed to upload public files${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Files uploaded successfully${NC}"

# Step 4: Set permissions on server
echo -e "${YELLOW}🔐 Step 4: Setting permissions...${NC}"

ssh $SERVER_USER@$SERVER_HOST << EOF
    chmod -R 755 $SERVER_APP_PATH
    chmod -R 777 $SERVER_APP_PATH/storage/sessions
    chmod 644 $SERVER_PUBLIC_PATH/.htaccess
    chmod 644 $SERVER_PUBLIC_PATH/index.php
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Permissions set successfully${NC}"
else
    echo -e "${YELLOW}⚠️ Permission setting may have failed - please check manually${NC}"
fi

# Step 5: Cleanup
echo -e "${YELLOW}🧹 Step 5: Cleaning up...${NC}"
rm -rf $DEPLOY_DIR
echo -e "${GREEN}✅ Cleanup completed${NC}"

echo ""
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo ""
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. SSH to your server and create/update the .env file:"
echo "   ssh $SERVER_USER@$SERVER_HOST"
echo "   cd $SERVER_APP_PATH"
echo "   cp .env.example .env"
echo "   nano .env"
echo ""
echo "2. Access your admin panel:"
echo "   https://metamondes.com/license/login"
echo ""
echo -e "${YELLOW}🔧 Troubleshooting:${NC}"
echo "- Check server error logs if site doesn't load"
echo "- Verify .env configuration"
echo "- Ensure Supabase connectivity"