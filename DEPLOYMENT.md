# LegalLaravel CRM - Production Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying the LegalLaravel CRM system to production. The application is containerized using Docker and includes automated CI/CD pipelines.

## Prerequisites

### Server Requirements
- Ubuntu 20.04+ or CentOS 7+ Linux server
- 2GB RAM minimum, 4GB recommended
- 20GB storage minimum
- Root or sudo access

### Domain & SSL
- Registered domain name
- SSL certificate (Let's Encrypt or purchased)

### Services Required
- AWS S3 bucket for file storage
- Mailgun account for email sending
- Redis (included in Docker stack)
- MySQL (included in Docker stack)

## Step 1: Server Preparation

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Install Docker & Docker Compose
```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
```

### 1.3 Install Nginx (Reverse Proxy)
```bash
sudo apt install nginx -y

# Configure Nginx
sudo nano /etc/nginx/sites-available/legallaravel
```

Add this configuration:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        # Timeout settings
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }
}
```

### 1.4 Enable Site & SSL
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/legallaravel /etc/nginx/sites-enabled/

# Install Certbot for SSL
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test configuration
sudo nginx -t
sudo systemctl reload nginx
```

## Step 2: Application Deployment

### 2.1 Clone Repository
```bash
cd /var/www
git clone https://github.com/yourusername/legallaravel.git
cd legallaravel
```

### 2.2 Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
docker run --rm -v $(pwd):/app -w /app laravelphp/vapor:php82 composer install --no-dev
docker run --rm -v $(pwd):/app -w /app laravelphp/vapor:php82 php artisan key:generate
```

### 2.3 Configure Environment Variables
Edit `.env` file with production values (see Environment Variables section below).

### 2.4 Start Application
```bash
# Build and start containers
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed initial data (optional)
docker-compose exec app php artisan db:seed

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Step 3: Post-Deployment Configuration

### 3.1 File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/legallaravel
sudo chmod -R 755 /var/www/legallaravel
sudo chmod -R 775 /var/www/legallaravel/storage
```

### 3.2 SSL Certificate Renewal
```bash
# Test renewal
sudo certbot renew --dry-run

# Add cron job for automatic renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 3.3 Backup Configuration
```bash
# Test backup
docker-compose exec app php artisan backup:run

# Add cron job for daily backups
# Add to crontab: 0 2 * * * cd /var/www/legallaravel && docker-compose exec app php artisan backup:run
```

## Step 4: Monitoring & Maintenance

### 4.1 Health Checks
The application includes a health check endpoint at `/health` that monitors:
- Database connectivity
- Cache connectivity
- File storage accessibility
- Application version

### 4.2 Log Monitoring
```bash
# View application logs
docker-compose logs -f app

# View nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### 4.3 Performance Monitoring
- Laravel Telescope: Access at `/telescope` (admin only)
- Redis monitoring: `docker-compose exec redis redis-cli info`
- Database monitoring: Use MySQL monitoring tools

## Step 5: Scaling & High Availability

### 5.1 Horizontal Scaling
```yaml
# docker-compose.scale.yml
version: '3.8'
services:
  app:
    scale: 3  # Run 3 app instances
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/ssl/certs
    depends_on:
      - app
```

### 5.2 Database Replication
For high availability, configure MySQL master-slave replication or use AWS RDS.

## Environment Variables

Copy the following to your `.env` file and configure with production values:

```bash
# Application
APP_NAME="LegalLaravel CRM"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com
FORCE_HTTPS=true

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=legallaravel
DB_USERNAME=laravel
DB_PASSWORD=your-secure-password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# AWS S3
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=legallaravel-documents
AWS_USE_PATH_STYLE_ENDPOINT=false

# Mailgun
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=yourdomain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret-key
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# SMS (Msg91)
SMS_PROVIDER=msg91
SMS_API_KEY=your-msg91-api-key
SMS_SENDER_ID=LEGAL

# Twilio (Alternative SMS)
TWILIO_ACCOUNT_SID=your-twilio-sid
TWILIO_AUTH_TOKEN=your-twilio-token
TWILIO_PHONE_NUMBER=your-twilio-number

# Security
SESSION_LIFETIME=120
SANCTUM_STATEFUL_DOMAINS=yourdomain.com

# Performance
IMAGE_OPTIMIZER_QUALITY=85
BACKUP_DISK=local

# Monitoring (Optional)
SENTRY_LARAVEL_DSN=your-sentry-dsn
TELESCOPE_ENABLED=true
```

## Troubleshooting

### Common Issues

1. **Container won't start**
   ```bash
   docker-compose logs app
   # Check for missing environment variables or permission issues
   ```

2. **Database connection fails**
   ```bash
   docker-compose exec db mysql -u laravel -p legallaravel
   # Verify database exists and credentials are correct
   ```

3. **File upload fails**
   - Check AWS S3 permissions
   - Verify bucket exists and is accessible
   - Check storage disk configuration

4. **Email not sending**
   - Verify Mailgun credentials
   - Check spam folder
   - Review mail logs: `docker-compose logs app | grep mail`

5. **SSL certificate issues**
   ```bash
   sudo certbot certificates
   # Renew if expired
   sudo certbot renew
   ```

### Performance Tuning

1. **PHP Optimization**
   ```bash
   # In Dockerfile, add:
   RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini
   RUN echo "max_execution_time=60" > /usr/local/etc/php/conf.d/max-execution-time.ini
   ```

2. **Database Optimization**
   ```sql
   -- Run these queries on production database
   ANALYZE TABLE users, cases, clients, invoices;
   OPTIMIZE TABLE users, cases, clients, invoices;
   ```

3. **Redis Configuration**
   ```bash
   # In docker-compose.yml, add Redis configuration
   redis:
     command: redis-server --maxmemory 256mb --maxmemory-policy allkeys-lru
   ```

## Security Checklist

- [ ] SSL certificate installed and valid
- [ ] Environment variables secured (no secrets in repo)
- [ ] File permissions set correctly
- [ ] Database credentials strong and unique
- [ ] AWS S3 bucket private with proper policies
- [ ] Admin user created with strong password
- [ ] Two-factor authentication enabled for admin
- [ ] Firewall configured (only 22, 80, 443 open)
- [ ] Regular backup testing completed
- [ ] Monitoring alerts configured

## Support

For issues or questions:
1. Check application logs: `docker-compose logs -f app`
2. Review Laravel documentation
3. Check GitHub issues for similar problems
4. Contact development team

## Backup & Recovery

### Automated Backups
- Database dumps created daily
- File backups to S3
- Application code versioned in Git

### Recovery Procedure
1. Stop application: `docker-compose down`
2. Restore database from backup
3. Restore files from S3 backup
4. Start application: `docker-compose up -d`
5. Verify functionality

This deployment guide ensures a secure, scalable, and maintainable production environment for the LegalLaravel CRM system.