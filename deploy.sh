#!/bin/bash

# ============================================================
# TeraSamarinda Deployment Script
# ============================================================
# Cara pakai: ./deploy.sh
# Prerequisites: npm, zip, scp, ssh
# ============================================================

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Load konfigurasi
if [ -f "deploy_config.sh" ]; then
    source deploy_config.sh
    echo -e "${GREEN}✓ Loaded configuration from deploy_config.sh${NC}"
else
    echo -e "${RED}✗ deploy_config.sh not found!${NC}"
    echo "Please copy deploy_config.example.sh to deploy_config.sh and edit it"
    exit 1
fi

# Validasi konfigurasi
if [ -z "$SERVER_HOST" ] || [ -z "$SERVER_USER" ] || [ -z "$SERVER_PATH" ]; then
    echo -e "${RED}✗ Missing configuration values!${NC}"
    echo "Please check deploy_config.sh"
    exit 1
fi

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}  TeraSamarinda Deployment Script${NC}"
echo -e "${YELLOW}========================================${NC}"
echo ""

# Step 1: Install dependencies dan Build
echo -e "${YELLOW}Step 1: Installing dependencies & building...${NC}"
if [ ! -d "node_modules" ]; then
    echo "Installing npm packages..."
    npm install
fi

echo "Building project..."
npm run build

if [ ! -d "dist" ]; then
    echo -e "${RED}✗ Build failed! dist folder not found${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Build completed successfully${NC}"
echo ""

# Step 2: Create deployment package
echo -e "${YELLOW}Step 2: Creating deployment package...${NC}"
rm -f dist.zip
zip -r dist.zip dist
echo -e "${GREEN}✓ Package created: dist.zip${NC}"
echo ""

# Step 3: Upload to VPS
echo -e "${YELLOW}Step 3: Uploading to VPS...${NC}"
echo "Server: $SERVER_USER@$SERVER_HOST"
echo "Path: $SERVER_PATH"

scp -P "$SERVER_PORT" dist.zip "$SERVER_USER@$SERVER_HOST:/tmp/"

if [ $? -ne 0 ]; then
    echo -e "${RED}✗ Upload failed!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Upload completed${NC}"
echo ""

# Step 4: Extract on server
echo -e "${YELLOW}Step 4: Extracting on server...${NC}"

SSH_CMD="ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST"

# Backup old dist if exists
$SSH_CMD "if [ -d '$SERVER_PATH/dist' ]; then rm -rf '$SERVER_PATH/dist_old'; mv '$SERVER_PATH/dist' '$SERVER_PATH/dist_old'; fi"

# Extract new dist
$SSH_CMD "unzip -o /tmp/dist.zip -d $SERVER_PATH/"

# Clean up temp files
$SSH_CMD "rm -f /tmp/dist.zip"

echo -e "${GREEN}✓ Extraction completed${NC}"
echo ""

# Step 5: Final message
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Deployment Successful!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Frontend deployed to: https://$SERVER_HOST"
echo "Don't forget to upload backend folder if needed!"
echo ""
