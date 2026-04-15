#!/bin/bash
# ==========================================
# Your App Name - cPanel Deployment Script
# ==========================================
# Usage: ./deploy.sh
# ==========================================

set -e

# ==========================================
# CONFIGURATION - UPDATE THESE
# ==========================================
APP_NAME="your-app-name"
REMOTE_USER="your-username"
REMOTE_HOST="your-server.com"
REMOTE_PATH="/home/your-username/public_html"
DB_NAME="your_database_name"
DB_USER="your_db_username"
DB_HOST="localhost"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}==========================================${NC}"
echo -e "${GREEN}  Deploying $APP_NAME${NC}"
echo -e "${GREEN}==========================================${NC}"

# Step 1: Build assets locally
echo -e "\n${YELLOW}[1/5] Building production assets...${NC}"
npm run build

# Step 2: Create deployment package
echo -e "\n${YELLOW}[2/5] Creating deployment package...${NC}"
zip -r ${APP_NAME}-deploy.zip . \
    --exclude "node_modules/*" \
    --exclude ".git/*" \
    --exclude "*.env" \
    --exclude "storage/logs/*" \
    --exclude "storage/debugbar/*" \
    --exclude "*.log" \
    --exclude ".env.*" \
    --exclude ".idea/*" \
    --exclude ".vscode/*" \
    --exclude "*.md"

# Step 3: Upload files
echo -e "\n${YELLOW}[3/5] Uploading files to server...${NC}"
rsync -az --progress \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude '*.env' \
    --exclude 'storage/logs' \
    ./ ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/

# Step 4: SSH commands
echo -e "\n${YELLOW}[4/5] Running server commands...${NC}"
ssh ${REMOTE_USER}@${REMOTE_HOST} << 'EOF'
    cd /home/your-username/public_html
    
    # Install composer dependencies
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Create .env if not exists
    if [ ! -f .env ]; then
        cp .env.example .env
        php artisan key:generate
    fi
    
    # Run migrations
    php artisan migrate --force --no-interaction
    
    # Cache optimization
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    # Set permissions
    chmod -R 755 storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache storage/logs
    
    # Clear cache
    php artisan optimize:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
EOF

# Step 5: Cleanup
echo -e "\n${YELLOW}[5/5] Cleaning up...${NC}"
rm -f ${APP_NAME}-deploy.zip

echo -e "\n${GREEN}==========================================${NC}"
echo -e "${GREEN}  Deployment Complete!${NC}"
echo -e "${GREEN}==========================================${NC}"
echo -e "\nVisit: https://your-domain.com"
echo -e "\nDatabase: ${DB_HOST}:3306/${DB_NAME}"
