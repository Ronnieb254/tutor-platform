# phpMyAdmin Access Guide

phpMyAdmin has been added to the production deployment for temporary database monitoring and verification.

## 🌐 Access Information

**URL:** https://pma.mwalimu.co.ke (or your configured subdomain)

**Login Credentials:**
- **Username:** Your `DB_USERNAME` from .env
- **Password:** Your `DB_PASSWORD` from .env
- **Root Login:** `root` / `DB_ROOT_PASSWORD`

## 🚀 Setup Steps

### 1. Add DNS Record

Add an A record for the phpMyAdmin subdomain:

```
pma.mwalimu.co.ke → A → your_server_ip
```

### 2. Configure Environment

In your `.env` file, add/verify:

```env
PHPMYADMIN_VIRTUAL_HOST=pma.mwalimu.co.ke
PHPMYADMIN_LETSENCRYPT_HOST=pma.mwalimu.co.ke
```

### 3. Deploy

phpMyAdmin will start automatically with:

```bash
docker-compose -f docker-compose.prod.yml up -d
```

### 4. Wait for SSL

Let's Encrypt will generate SSL certificate (1-2 minutes).

Check SSL status:
```bash
docker logs letsencrypt -f
```

### 5. Access

Visit: https://pma.mwalimu.co.ke

## 🔒 Security Recommendations

### Option 1: IP Whitelist (Recommended)

Add to `docker-compose.prod.yml` under phpMyAdmin service:

```yaml
environment:
  - APACHE_ALLOW_OVERRIDE=All
  - APACHE_CUSTOM_CONFIG=|
      <Directory /var/www/html>
        Require ip your.ip.address.here
        Require ip another.ip.if.needed
      </Directory>
```

### Option 2: Basic HTTP Authentication

Generate htpasswd:
```bash
docker run --rm httpd:alpine htpasswd -nbB admin YourPassword
```

Add to phpMyAdmin environment in `docker-compose.prod.yml`:
```yaml
environment:
  - APACHE_BASIC_AUTH_HTPASSWD=admin:$$2y$$05$$...hash...
```

### Option 3: VPN Access Only

Restrict access through your VPN/firewall rules.

## 🗑️ Remove phpMyAdmin After Verification

Once you're satisfied everything is working:

### 1. Stop and Remove Container

```bash
docker-compose -f docker-compose.prod.yml stop phpmyadmin
docker-compose -f docker-compose.prod.yml rm -f phpmyadmin
```

### 2. Remove from docker-compose.prod.yml

Comment out or delete the entire `phpmyadmin:` service block.

### 3. Restart

```bash
docker-compose -f docker-compose.prod.yml up -d
```

### 4. Remove DNS Record

Delete the `pma.mwalimu.co.ke` A record from your DNS.

### 5. Revoke SSL Certificate (Optional)

```bash
docker exec letsencrypt sh -c "rm -rf /etc/nginx/certs/pma.mwalimu.co.ke*"
```

## 📊 Common Tasks in phpMyAdmin

### Check Tables
1. Login
2. Select `tutor_platform` database (or your DB name)
3. View all tables

### Run SQL Queries
1. Go to SQL tab
2. Run queries to verify data

### Monitor Users
```sql
SELECT * FROM users ORDER BY created_at DESC LIMIT 10;
```

### Check Payments
```sql
SELECT * FROM payments ORDER BY created_at DESC LIMIT 20;
```

### View Bookings
```sql
SELECT * FROM bookings WHERE status = 'confirmed';
```

### Check Examiner Clinics
```sql
SELECT * FROM examiner_clinics WHERE status = 'active';
```

## 🐛 Troubleshooting

### Cannot Access phpMyAdmin

1. **Check DNS propagation:**
   ```bash
   nslookup pma.mwalimu.co.ke
   ```

2. **Check container status:**
   ```bash
   docker-compose -f docker-compose.prod.yml ps
   ```

3. **Check logs:**
   ```bash
   docker logs mwalimu-phpmyadmin -f
   docker logs letsencrypt -f
   ```

4. **Verify nginx-proxy:**
   ```bash
   docker logs nginx-proxy -f
   ```

### Login Failed

- Verify `DB_USERNAME` and `DB_PASSWORD` in `.env`
- Try root user: `root` / `DB_ROOT_PASSWORD`
- Check MySQL container logs:
  ```bash
  docker logs mwalimu-mysql -f
  ```

### SSL Certificate Not Generated

Wait 2-3 minutes, then check:
```bash
docker exec nginx-proxy ls -la /etc/nginx/certs/ | grep pma
```

If missing, restart Let's Encrypt:
```bash
docker-compose -f docker-compose.prod.yml restart letsencrypt
```

## 📝 Notes

- **Temporary:** phpMyAdmin is for initial deployment verification only
- **Performance:** Remove after setup to reduce resource usage
- **Security:** Always use strong passwords and consider IP whitelisting
- **Updates:** phpMyAdmin container auto-updates on restart
- **Backup:** Always backup database before making changes in phpMyAdmin

## 🎯 Quick Verification Checklist

After deployment, use phpMyAdmin to verify:

- ✅ All tables created (migrations ran)
- ✅ Users table has data
- ✅ Examiner clinics can be created
- ✅ Bookings are recorded
- ✅ Payments are tracked
- ✅ Commission calculations are correct
- ✅ BigBlueButton credentials are stored

Once verified → Remove phpMyAdmin!
