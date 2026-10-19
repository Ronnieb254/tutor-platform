# 🐧 Ubuntu Server Deployment Guide

Complete guide to deploy Mwalimu platform on Ubuntu Server using Docker.

## 📋 Prerequisites

- Ubuntu 20.04 LTS or 22.04 LTS
- Root or sudo access
- 2GB RAM minimum (4GB recommended)
- 20GB disk space
- Domain name pointed to your server
- Open ports: 80, 443, 22

## 🚀 Step-by-Step Deployment

### Step 1: Update System

```bash
sudo apt update && sudo apt upgrade -y
```

### Step 2: Install Docker

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add your user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose -y

# Verify installation
docker --version
docker-compose --version
```

**Log out and back in** for group changes to take effect.

### Step 3: Clone Repository

```bash
# Navigate to web directory
cd /var/www

# Clone your repository
sudo git clone <your-repository-url> mwalimu

# Set permissions
sudo chown -R $USER:$USER /var/www/mwalimu
cd /var/www/mwalimu/tutor-platform
```

### Step 4: Configure Environment

```bash
# Copy the production environment template
cp .env.production .env

# Edit with your credentials
nano .env
```

**Required configurations in `.env`:**

```env
# Generate APP_KEY first (see step 5)
APP_KEY=base64:xxx

# Database
DB_DATABASE=tutor_platform
DB_USERNAME=mwalimu
DB_PASSWORD=your_strong_password_here
DB_ROOT_PASSWORD=your_strong_root_password_here

# Paystack (Production keys)
PAYSTACK_PUBLIC_KEY=pk_live_xxx
PAYSTACK_SECRET_KEY=sk_live_xxx

# BigBlueButton
BBB_SERVER_URL=https://bbb.mwalimu.co.ke/bigbluebutton
BBB_SECRET=your_bbb_secret

# Mail (if using Gmail)
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Domain
APP_URL=https://mwalimu.co.ke
```

Save and exit (Ctrl+X, Y, Enter)

### Step 5: Generate Application Key

```bash
# Generate APP_KEY
docker-compose run --rm app php artisan key:generate --show

# Copy the output and add to .env
nano .env
# Add: APP_KEY=base64:xxx (the output from above)
```

### Step 6: Start Services

```bash
# Build and start containers
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

### Step 7: Initialize Application

```bash
# Wait 30 seconds for MySQL to be ready, then:

# Run migrations
docker-compose exec app php artisan migrate --force

# Create storage link
docker-compose exec app php artisan storage:link

# Optimize
docker-compose exec app php artisan optimize

# Set permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Step 8: Create Admin User

```bash
docker-compose exec app php artisan tinker
```

In tinker:
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@mwalimu.co.ke',
    'password' => bcrypt('your-admin-password'),
    'role' => 'admin',
    'email_verified_at' => now()
]);
exit
```

### Step 9: Setup Nginx Reverse Proxy + SSL

```bash
# Install Nginx (on host, not in Docker)
sudo apt install nginx -y

# Install Certbot for SSL
sudo apt install certbot python3-certbot-nginx -y

# Create Nginx config
sudo nano /etc/nginx/sites-available/mwalimu
```

**Nginx configuration:**

```nginx
server {
    listen 80;
    server_name mwalimu.co.ke www.mwalimu.co.ke;

    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # WebSocket support (if using Pusher)
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }

    client_max_body_size 100M;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/mwalimu /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Get SSL certificate
sudo certbot --nginx -d mwalimu.co.ke -d www.mwalimu.co.ke
```

### Step 10: Setup Firewall

```bash
# Enable UFW firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Check status
sudo ufw status
```

## ✅ Verification

```bash
# Check all containers are running
docker-compose ps

# Test the application
curl http://localhost

# Check logs
docker-compose logs -f app

# Monitor resources
docker stats
```

Visit: https://mwalimu.co.ke

## 🔧 Common Commands

### Container Management
```bash
# View logs
docker-compose logs -f

# Restart services
docker-compose restart

# Stop all services
docker-compose down

# Start services
docker-compose up -d

# Rebuild after code changes
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Laravel Commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Run queue manually
docker-compose exec app php artisan queue:work

# Database backup
docker-compose exec mysql mysqldump -u mwalimu -p tutor_platform > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u mwalimu -p tutor_platform < backup.sql
```

## 🔒 Security Hardening

### 1. Disable phpMyAdmin
Already commented out in production docker-compose.yml

### 2. Close Database Port
Database port is not exposed externally by default (good!)

### 3. Enable Fail2Ban
```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 4. Auto Security Updates
```bash
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
```

### 5. Change SSH Port (Optional)
```bash
sudo nano /etc/ssh/sshd_config
# Change: Port 22 to Port 2222
sudo systemctl restart ssh
sudo ufw allow 2222/tcp
```

## 📊 Monitoring

### View Resource Usage
```bash
docker stats
```

### Check Disk Space
```bash
df -h
```

### View Application Logs
```bash
docker-compose logs -f app
docker-compose logs -f queue
docker-compose logs -f mysql
```

## 🔄 Updates

```bash
cd /var/www/mwalimu/tutor-platform

# Pull latest code
git pull

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear cache
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize
```

## 💾 Backup Script

Create `/root/backup-mwalimu.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"

mkdir -p $BACKUP_DIR

# Backup database
docker-compose -f /var/www/mwalimu/tutor-platform/docker-compose.yml exec -T mysql mysqldump -u mwalimu -p'your_password' tutor_platform > $BACKUP_DIR/db_$DATE.sql

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/mwalimu/tutor-platform/storage

# Backup .env
cp /var/www/mwalimu/tutor-platform/.env $BACKUP_DIR/env_$DATE

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
chmod +x /root/backup-mwalimu.sh

# Add to crontab (daily at 2 AM)
crontab -e
# Add: 0 2 * * * /root/backup-mwalimu.sh
```

## 🐛 Troubleshooting

### Container won't start
```bash
docker-compose logs app
docker-compose logs mysql
```

### Permission errors
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Database connection failed
```bash
# Check MySQL is running
docker-compose ps mysql

# Check credentials in .env
cat .env | grep DB_
```

### Out of disk space
```bash
# Clean Docker
docker system prune -a

# Check disk usage
df -h
```

## 📞 Support

For issues:
- Check logs: `docker-compose logs -f`
- Check container status: `docker-compose ps`
- Restart services: `docker-compose restart`

---

**Deployment Complete! 🎉**

Your Mwalimu platform is now running on Ubuntu Server with Docker.

Access: https://mwalimu.co.ke
