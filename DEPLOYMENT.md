# Deployment Guide

## Server Requirements (Minimum)

### Operating System

- Ubuntu Server 20.04 LTS / 22.04 LTS (recommended)
- CentOS 7+ / Rocky Linux 8+

### Software Stack

| Software | Version              | Notes                            |
| -------- | -------------------- | -------------------------------- |
| Nginx    | 1.20+                | Web server                       |
| PHP      | 8.3+                 | Runtime (required by Laravel 13) |
| MySQL    | 8.0+ / MariaDB 10.5+ | Database                         |
| Composer | 2.5+                 | PHP dependency manager           |
| Node.js  | 18+                  | (Optional) For asset building    |

### PHP Extensions

Ensure the following PHP extensions are installed:

- `openssl`
- `pdo`
- `mbstring`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `fileinfo`
- `gd` (for image upload)
- `zip` (for Excel export)
- `mysql` / `mysqli`

### Storage

- Minimum 10GB free disk space
- Dedicated directory for uploaded files (foto_bon)
- Write permissions for `storage/` and `bootstrap/cache/`

### RAM

- Minimum: 2GB
- Recommended: 4GB+
- For production with queue workers: 4GB+

---

## Installation Steps

### 1. Update System

```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Install Nginx

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 3. Install PHP 8.3

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.3-fpm php8.3-cli php8.3-common php8.3-mysql \
                 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip \
                 php8.3-gd php8.3-bcmath php8.3-json php8.3-tokenizer \
                 php8.3-fileinfo php8.3-ctype -y
```

### 4. Install MySQL

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

Create database:

```sql
CREATE DATABASE bensin_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bensin_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON bensin_monitoring.* TO 'bensin_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### 6. Deploy Application

```bash
cd /var/www
git clone <repository-url> bensin-monitoring
cd bensin-monitoring
composer install --no-dev --optimize-autoloader
```

### 7. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Sistem Monitoring BBM"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bensin_monitoring
DB_USERNAME=bensin_user
DB_PASSWORD=your_strong_password

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

### 8. Database Migration

```bash
php artisan migrate --force
```

### 9. Storage Setup

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 10. Nginx Configuration

Create `/etc/nginx/sites-available/bensin-monitoring`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/bensin-monitoring/public;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 5M;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/bensin-monitoring /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 11. Set Up Queue Worker (Optional)

For background job processing:

```bash
sudo nano /etc/systemd/system/bensin-queue.service
```

```ini
[Unit]
Description=Bensin Monitoring Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/bensin-monitoring
ExecStart=/usr/bin/php8.3 artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable bensin-queue
sudo systemctl start bensin-queue
```

---

## Maintenance

### Cache Clearance

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Backup Database

```bash
mysqldump -u bensin_user -p bensin_monitoring > backup_$(date +%Y%m%d).sql
```

### Update Application

```bash
cd /var/www/bensin-monitoring
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Performance Optimization

### OPcache

Add to `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### Laravel Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Catatan Arsitektur

### Struktur Direktori Service
Semua service class berada di `app/Services/` (kapital S) dengan namespace `App\Services\`.
Pastikan autoload sudah dioptimalkan setelah update:

```bash
composer dump-autoload -o
```

### API Tanpa Middleware Auth
Saat ini **semua endpoint API (`/api/*`) tidak memiliki middleware autentikasi**.
Rekomendasi untuk membatasi akses:

1. **Restrict IP via Nginx** (paling direkomendasikan):
   ```nginx
   location /api/ {
       allow 10.0.0.0/8;    # IP internal kantor
       allow 192.168.0.0/16;
       deny all;
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

2. Atau gunakan firewall (UFW/iptables) untuk membatasi akses ke port aplikasi.

> Jika autentikasi API diperlukan di masa depan, gunakan Laravel Sanctum
> (token-based, cocok untuk integrasi dengan Flutter). Jangan gunakan
> session-based auth untuk API.

---

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS (Let's Encrypt)
- [ ] Strong database password
- [ ] Regular backups scheduled
- [ ] File upload size limits configured
- [ ] Nginx access/error logs enabled
- [ ] Fail2ban configured for SSH
- [ ] MySQL bound to localhost only
