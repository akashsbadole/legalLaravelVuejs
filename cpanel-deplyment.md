cPanel Deployment Process
Step 1: Build Locally First

# In your project folder, run:

npm run build
composer install --no-dev --optimize-autoloader
Step 2: Upload Files to cPanel
Method A: File Manager

1. Login to cPanel → File Manager
2. Navigate to public_html/
3. Upload all files except:
    - node_modules/
    - .git/
    - .env
      Method B: FTP
      Host: your-domain.com
      Username: your-cpanel-username
      Password: your-cpanel-password
      Port: 21
      Step 3: Create Database
4. Login to cPanel → MySQL Databases
5. Create database: your_db_name
6. Create user: your_db_user + password
7. Add user to database (grant all privileges)
   Step 4: Configure Environment
8. In File Manager, go to public_html/
9. Create .env file:
   APP_NAME="Your App Name"
   APP_ENV=production
   APP_KEY=base64:YOUR_GENERATED_KEY
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   Generate APP_KEY:

# Run locally, then copy

php artisan key:generate --show
Step 5: Set Permissions
In cPanel → Terminal or SSH:
cd ~/public_html

# Set permissions

chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache storage/logs
chmod 644 .env
Step 6: Run Commands
In cPanel → Terminal or SSH:

# Generate key (if not set)

php artisan key:generate

# Run migrations

php artisan migrate --force

# Clear and cache

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
Step 7: Point Domain (if needed)
If domain not pointing to public_html:

1. cPanel → Domains → Aliases
2. Add your domain
3. Set document root to public_html

---

Quick Checklist
Task
[ ] Build: npm run build
[ ] Upload files to public_html
[ ] Create MySQL database
[ ] Create .env file
[ ] Set permissions
[ ] Run php artisan migrate
[ ] Run php artisan config:cache
[ ] Test: https://your-domain.com/health

---

Test Login After Deploy
URL: https://your-domain.com
Email: admin@legalfirm.com
Password: password
