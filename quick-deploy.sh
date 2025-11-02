#!/bin/bash

# License Admin Panel - Quick Deploy Script
# Usage: ./quick-deploy.sh

set -e

echo "🚀 Deploying License Admin Panel..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Paths
REPO_DIR="/home/metamondes.com/license-admin"
PUBLIC_DIR="/home/metamondes.com/public_html/license"

echo -e "${YELLOW}📦 Step 1: Copying public files...${NC}"
cp -v "${REPO_DIR}/public/index.php" "${PUBLIC_DIR}/index.php"
chmod 640 "${PUBLIC_DIR}/index.php"

echo -e "${YELLOW}📦 Step 2: Setting permissions...${NC}"
chmod -R 755 "${REPO_DIR}/storage"
chmod -R 777 "${REPO_DIR}/storage/sessions"
chmod -R 777 "${REPO_DIR}/storage/logs"

echo -e "${GREEN}✅ Deployment complete!${NC}"
echo -e "${GREEN}🌐 Access: https://metamondes.com/license/${NC}"
