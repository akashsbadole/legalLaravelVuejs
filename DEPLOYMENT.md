# ==========================================
# LegalLaravel - Deployment Guide
# ==========================================

## Quick Start

### Step 1: Rename Configuration Files

```bash
# Docker
mv docker-compose-example.yml docker-compose.yml
mv docker/nginx-example.conf docker/nginx.conf
mv docker/supervisord-example.conf docker/supervisord.conf
mv docker/entrypoint-example.sh docker/entrypoint.sh
chmod +x docker/entrypoint.sh

# Deployment script
mv deploy-example.sh deploy.sh
chmod +x deploy.sh
```

### Step 2: Update All Placeholders

Search and replace these placeholders in all files:

| Placeholder | Replace With |
|-------------|--------------|
| `your-app-name` | Your app name (e.g., legal-crm) |
| `your-domain.com` | Your actual domain |
| `your_database_name` | Database name |
| `your_db_username` | Database username |
| `your_db_password` | Database password |
| `your_root_password` | MySQL root password |
| `your-username` | SSH username |
| `your-server.com` | Server hostname |

### Step 3: Build Assets

```bash
npm run build
```

---

## Deployment Options

### Option 1: Docker (Recommended)

```bash
# 1. Edit docker-compose.yml with your values
nano docker-compose.yml

# 2. Build and start
docker-compose up -d --build

# 3. Run migrations
docker-compose exec app php artisan migrate --force

# 4. Check status
docker-compose ps
```

### Option 2: cPanel / Shared Hosting

```bash
# 1. Upload files to public_html via FTP

# 2. SSH into server and run:
cd ~/public_html
composer install --no-dev --optimize-autoloader
cp .env.production.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache storage/logs
```

### Option 3: VPS (Using Deploy Script)

```bash
# 1. Edit deploy.sh with your server details
nano deploy.sh

# 2. Run deployment
./deploy.sh
```

---

## Files Created

| File | Purpose |
|------|---------|
| `Dockerfile` | Docker container image |
| `docker-compose.yml` | Multi-container setup |
| `.dockerignore` | Build exclusions |
| `docker/nginx.conf` | Web server config |
| `docker/supervisord.conf` | Process manager |
| `docker/entrypoint.sh` | Startup script |
| `deploy.sh` | cPanel deployment |
| `.env.production` | Production env vars |

---

## Post-Deployment

### Verify Installation

```bash
# Health check
curl https://your-domain.com/health

# Should return:
{"status": "ok", "timestamp": "..."}
```

### Default Login

```
URL: https://your-domain.com
Email: admin@legalfirm.com
Password: password
```

---

## Troubleshooting

### 500 Error
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission Error
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache storage/logs
```

### Database Error
Check `.env` database settings match your server.
