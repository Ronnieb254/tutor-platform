# 🌐 Subdomain Setup with Automatic SSL

Complete guide to deploy Mwalimu platform on a subdomain with automatic Let's Encrypt SSL certificates.

## 📋 Overview

This setup uses:
- **nginx-proxy**: Automatic reverse proxy for Docker containers
- **acme-companion**: Automatic Let's Encrypt SSL certificate generation and renewal
- **Your subdomain**: e.g., `tutor.mwalimu.co.ke`

## 🎯 Architecture

```
Internet
   ↓
nginx-proxy (ports 80, 443)
   ↓
Let's Encrypt (SSL certificates)
   ↓
Mwalimu App Container
```

## 🚀 Step-by-Step Setup

### Step 1: DNS Configuration

Point your subdomain to your Ubuntu server:

**In your DNS provider (e.g., Cloudflare, Namecheap):**

```
Type: A Record
Name: tutor
Value: YOUR_SERVER_IP
TTL: Auto
```

Or for multiple subdomains:

```
Type: A Record
Name: tutor
Value: YOUR_SERVER_IP

Type: A Record
Name: admin
Value: YOUR_SERVER_IP

Type: A Record  
Name: api
Value: YOUR_SERVER_IP
```

**Verify DNS propagation:**
```bash
# Wait 5-10 minutes, then test
dig tutor.mwalimu.co.ke
ping tutor.mwalimu.co.ke
```

### Step 2: Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose -y

# Add user to docker group
sudo usermod -aG docker $USER

# Log out and back in for group to take effect
exit
# SSH back in

# Verify installation
docker --version
docker-compose --version
```

### Step 3: Clone Repository

```bash
# Navigate to web directory
cd /var/www

# Clone repository
sudo git clone <your-repo-url> mwalimu

# Set permissions
sudo chown -R $USER:$USER /var/www/mwalimu
cd /var/www/mwalimu/tutor-platform
```

### Step 4: Configure Environment

```bash
# Copy production environment template
cp .env.production .env

# Edit with your settings
nano .env
```

**Required settings in `.env`:**

```env
# Application
APP_NAME=Mwalimu
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tutor.mwalimu.co.ke    # ⚠️ IMPORTANT: Use your subdomain

# Virtual Host (for nginx-proxy)
VIRTUAL_HOST=tutor.mwalimu.co.ke       # ⚠️ Your subdomain
VIRTUAL_PORT=80

# Let's Encrypt SSL
LETSENCRYPT_HOST=tutor.mwalimu.co.ke   # ⚠️ Same as VIRTUAL_HOST
LETSENCRYPT_EMAIL=admin@mwalimu.co.ke  # ⚠️ Your email for SSL alerts

# Database
DB_DATABASE=tutor_platform
DB_USERNAME=mwalimu
DB_PASSWORD=your_strong_password_here   # ⚠️ CHANGE THIS
DB_ROOT_PASSWORD=your_root_password     # ⚠️ CHANGE THIS

# Paystack (Production)
PAYSTACK_PUBLIC_KEY=pk_live_xxx         # ⚠️ REQUIRED
PAYSTACK_SECRET_KEY=sk_live_xxx         # ⚠️ REQUIRED

# BigBlueButton
BBB_SERVER_URL=https://bbb.mwalimu.co.ke/bigbluebutton
BBB_SECRET=your_bbb_secret              # ⚠️ REQUIRED

# Mail
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

Save and exit (Ctrl+X, Y, Enter)

### Step 5: Generate Application Key

```bash
# Generate APP_KEY
docker-compose -f docker-compose.prod.yml run --rm app php artisan key:generate --show

# Copy the output and add it to .env
nano .env
# Add the line: APP_KEY=base64:xxxxx
```

### Step 6: Deploy with nginx-proxy

```bash
# Build and start all services (including nginx-proxy and Let's Encrypt)
docker-compose -f docker-compose.prod.yml up -d

# Check all containers are running
docker-compose -f docker-compose.prod.yml ps

# You should see:
# - nginx-proxy (running)
# - letsencrypt (running)
# - mwalimu-app (running)
# - mwalimu-mysql (running)
# - mwalimu-redis (running)
# - mwalimu-queue (running)
# - mwalimu-scheduler (running)
```

### Step 7: Wait for SSL Certificate

```bash
# Watch Let's Encrypt logs (SSL generation takes 1-2 minutes)
docker-compose -f docker-compose.prod.yml logs -f letsencrypt

# You should see:
# "Creating/renewal certificates for tutor.mwalimu.co.ke"
# "Certificate successfully obtained"

# Press Ctrl+C to stop watching logs
```

### Step 8: Initialize Application

```bash
# Wait 30 seconds for MySQL to be fully ready

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Create storage link
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link

# Optimize Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan optimize

# Set permissions
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/storage
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/storage
```

### Step 9: Create Admin User

```bash
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
```

In tinker:
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@mwalimu.co.ke',
    'password' => bcrypt('your-secure-password'),
    'role' => 'admin',
    'email_verified_at' => now()
]);
exit
```

### Step 10: Setup Firewall

```bash
# Configure UFW firewall
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable

# Check status
sudo ufw status
```

## ✅ Verification

### Test HTTP → HTTPS Redirect
```bash
curl -I http://tutor.mwalimu.co.ke
# Should return: 301 redirect to https://
```

### Test HTTPS
```bash
curl -I https://tutor.mwalimu.co.ke
# Should return: 200 OK
```

### Check SSL Certificate
```bash
echo | openssl s_client -servername tutor.mwalimu.co.ke -connect tutor.mwalimu.co.ke:443 2>/dev/null | openssl x509 -noout -dates
```

### Visit in Browser
```
https://tutor.mwalimu.co.ke
```

✅ You should see a valid SSL certificate (green padlock)

## 🔧 Multiple Subdomains

To deploy multiple apps on different subdomains:

### DNS Setup
```
Type: A Record, Name: tutor,  Value: YOUR_SERVER_IP
Type: A Record, Name: admin,  Value: YOUR_SERVER_IP
Type: A Record, Name: api,    Value: YOUR_SERVER_IP
```

### Deploy Each App
Each app needs its own `docker-compose.prod.yml` with:

```yaml
services:
  app:
    environment:
      - VIRTUAL_HOST=admin.mwalimu.co.ke
      - LETSENCRYPT_HOST=admin.mwalimu.co.ke
      - LETSENCRYPT_EMAIL=admin@mwalimu.co.ke
```

**All apps share the same nginx-proxy network!**

## 🔄 Common Commands

```bash
# View all logs
docker-compose -f docker-compose.prod.yml logs -f

# View specific service
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f nginx-proxy
docker-compose -f docker-compose.prod.yml logs -f letsencrypt

# Restart services
docker-compose -f docker-compose.prod.yml restart

# Stop all services
docker-compose -f docker-compose.prod.yml down

# Rebuild after code changes
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml build --no-cache app
docker-compose -f docker-compose.prod.yml up -d

# Run artisan commands
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear

# Database backup
docker-compose -f docker-compose.prod.yml exec mysql mysqldump -u mwalimu -p tutor_platform > backup.sql
```

## 🔒 SSL Certificate Renewal

**Automatic renewal** happens every 60 days. The acme-companion container handles this automatically.

To manually force renewal:
```bash
docker-compose -f docker-compose.prod.yml restart letsencrypt
docker-compose -f docker-compose.prod.yml logs -f letsencrypt
```

## 🐛 Troubleshooting

### SSL Certificate Not Generated

**Check DNS is pointing correctly:**
```bash
dig tutor.mwalimu.co.ke
```

**Check Let's Encrypt logs:**
```bash
docker-compose -f docker-compose.prod.yml logs letsencrypt
```

**Common issues:**
- DNS not propagated (wait 10-15 minutes)
- Firewall blocking port 80/443
- Domain doesn't resolve to server IP

### 502 Bad Gateway

**Check app container is running:**
```bash
docker-compose -f docker-compose.prod.yml ps app
docker-compose -f docker-compose.prod.yml logs app
```

**Restart app:**
```bash
docker-compose -f docker-compose.prod.yml restart app
```

### Can't Connect to Database

**Check MySQL is healthy:**
```bash
docker-compose -f docker-compose.prod.yml ps mysql
docker-compose -f docker-compose.prod.yml logs mysql
```

**Verify credentials in .env:**
```bash
cat .env | grep DB_
```

## 📊 Monitoring

### Check Certificate Expiry
```bash
docker-compose -f docker-compose.prod.yml exec nginx-proxy ls -la /etc/nginx/certs/
```

### View All Certificates
```bash
docker-compose -f docker-compose.prod.yml exec letsencrypt ls -la /etc/acme.sh/
```

### Check nginx-proxy Configuration
```bash
docker-compose -f docker-compose.prod.yml exec nginx-proxy cat /etc/nginx/conf.d/default.conf
```

## 🎯 Advantages of This Setup

✅ **Automatic SSL**: Let's Encrypt certificates auto-generated and renewed
✅ **Multiple Subdomains**: Add unlimited apps on different subdomains
✅ **Zero Downtime**: nginx-proxy handles traffic while containers restart
✅ **Auto HTTPS Redirect**: HTTP automatically redirects to HTTPS
✅ **Easy Management**: One nginx-proxy handles all apps
✅ **Scalable**: Add more apps without manual nginx configuration

## 📝 Quick Reference

**Files needed:**
- `.env` (with VIRTUAL_HOST and LETSENCRYPT_HOST)
- `docker-compose.prod.yml`
- `docker/nginx/proxy.conf`

**Environment variables:**
```env
VIRTUAL_HOST=tutor.mwalimu.co.ke
VIRTUAL_PORT=80
LETSENCRYPT_HOST=tutor.mwalimu.co.ke
LETSENCRYPT_EMAIL=admin@mwalimu.co.ke
```

**Deploy command:**
```bash
docker-compose -f docker-compose.prod.yml up -d
```

---

**Deployment Complete! 🎉**

Your application is now live at: **https://tutor.mwalimu.co.ke**

With automatic SSL certificate management! 🔒
