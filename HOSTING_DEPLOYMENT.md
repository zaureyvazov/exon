# EXON Klinika - Hosting Deploy Təlimatları

## Hosting Məlumatları

- **Domain**: https://exondr.az
- **Database Host**: 77.37.49.130
- **Database Name**: exon
- **Database User**: exon
- **Database Password**: 0603546474Zaur

---

## 1. Faylları Hostingə Yükləmək

### Upload Edilməli Fayllar
```
exon/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Document Root
├── resources/
├── routes/
├── storage/
├── vendor/          ← composer install-dən sonra
├── .env             ← .env.production-dan copy
├── artisan
├── composer.json
└── composer.lock
```

### Upload Edilməməli Fayllar (Sil və ya ignore et)
```
❌ node_modules/
❌ .git/
❌ .env.example
❌ .env.production (local backup)
❌ tests/
❌ storage/logs/*.log
❌ *.md (README, SECURITY_*, PERFORMANCE_*)
❌ phpunit.xml
❌ package.json, package-lock.json, vite.config.js
```

---

## 2. Hosting-də İcra Ediləcək Əmrlər

### SSH ilə bağlan (cPanel Terminal və ya SSH)

```bash
# 1. Layihə qovluğuna keç
cd /home/exon/public_html   # və ya layihə path-ı

# 2. .env faylını yerləşdir
# .env.production faylını .env kimi yüklə

# 3. Composer paketlərini yüklə (production)
composer install --optimize-autoloader --no-dev

# 4. Təhlükəsizlik: permissions düzəlt
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache   # və ya hosting user-i

# 5. Cache-ləri yarad
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Migrations-ı icra et
php artisan migrate --force

# 7. Storage link (public uploads üçün)
php artisan storage:link

# 8. Optimization
php artisan optimize
```

---

## 3. cPanel / Hosting Panel Konfiqurasiyaları

### Document Root (Public Folder)
```
Document Root: /home/exon/public_html/public
```

### PHP Versiya
```
PHP Version: 8.1 və ya 8.2 (minimum 8.1)
```

### PHP Configurations (php.ini və ya .htaccess)
```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 20M
post_max_size = 20M
```

### .htaccess (public/.htaccess - Laravel default mövcuddur)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # HTTPS redirect (production)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Laravel routes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 4. Database Setup

### MySQL/MariaDB
```sql
-- Database artıq yaradılıb: exon
-- User artıq yaradılıb: exon

-- Məlumatları yoxla
SHOW DATABASES;
SHOW TABLES FROM exon;

-- İzinləri yoxla
SHOW GRANTS FOR 'exon'@'%';
```

### Migration İcra
```bash
# SSH terminal-da
php artisan migrate --force

# Və ya seed ilə test datalarla
php artisan migrate:fresh --seed --force
```

---

## 5. Təhlükəsizlik Yoxlamaları

### ✅ Permissions
```bash
# Storage və cache qovluqları yazıla bilən olmalıdır
ls -la storage/
ls -la bootstrap/cache/

# Əgər icazələr düz deyilsə:
chmod -R 775 storage bootstrap/cache
```

### ✅ .env Qoruması
```apache
# public/.htaccess-ə əlavə et (Laravel default bunu edir)
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### ✅ Debugging Deaktiv
```bash
# .env-də yoxla:
APP_DEBUG=false
APP_ENV=production
```

### ✅ Session Encryption
```bash
# .env-də yoxla:
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

---

## 6. Performans Optimizasiyası

### Cache Commands (Production-da)
```bash
# Konfiqurasiya cache
php artisan config:cache

# Route cache
php artisan route:cache

# View cache
php artisan view:cache

# Event cache (əgər varsa)
php artisan event:cache

# Hamısını birdən
php artisan optimize
```

### Composer Autoload Optimize
```bash
composer dump-autoload --optimize --classmap-authoritative
```

### OPcache (PHP)
```ini
# php.ini və ya .user.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

---

## 7. Test Etmək

### Hosting-də Test
```bash
# 1. Site aç
https://exondr.az

# 2. Login test
Username: admin@exon.com (və ya yaratdığınız admin)
Password: password

# 3. Funksiyaları yoxla
- Doctor dashboard
- Patient yaratma
- Referral yaratma
- Admin panel

# 4. Error log yoxla
tail -f storage/logs/laravel.log
```

### Database Connection Test
```bash
php artisan tinker

>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
```

---

## 8. Problemlərin Həlli

### Problem: 500 Internal Server Error
```bash
# 1. .env faylını yoxla
cat .env

# 2. Permissions yoxla
ls -la storage/ bootstrap/cache/

# 3. Log-a bax
tail -50 storage/logs/laravel.log

# 4. Cache-ləri təmizlə
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Problem: Database Connection Error
```bash
# 1. Database credentials yoxla
cat .env | grep DB_

# 2. MySQL-ə bağlan test et
mysql -h 77.37.49.130 -u exon -p exon

# 3. Laravel-də test et
php artisan tinker
>>> DB::connection()->getPdo();
```

### Problem: Permission Denied
```bash
# Storage və cache permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Və ya hosting user-i ilə (məsələn: exon)
chown -R exon:exon storage bootstrap/cache
```

### Problem: PWA Not Working
```bash
# 1. manifest.json və sw.js mövcudluğunu yoxla
ls -la public/manifest.json
ls -la public/sw.js
ls -la public/images/icon-*.png

# 2. HTTPS-in aktiv olduğunu yoxla
# PWA yalnız HTTPS-də işləyir
```

---

## 9. Deployment Checklist ✅

### Pre-Upload
- [x] `.env.production` faylı hazırlandı
- [x] `composer install` local-da icra edildi
- [x] `npm run build` icra edildi (assets compiled)
- [x] Test datalar təmizləndi (real production-da)
- [x] Debug mode `false`

### Upload
- [x] Bütün fayllar FTP/SFTP ilə yükləndi
- [x] `node_modules/` yüklənmədi
- [x] `.git/` yüklənmədi
- [x] Document Root `/public` olaraq təyin edildi

### Post-Upload (SSH)
- [ ] `.env` faylı yerləşdirildi
- [ ] `composer install --no-dev` icra edildi
- [ ] Permissions düzəldildi (`chmod -R 775 storage bootstrap/cache`)
- [ ] Migrations icra edildi (`php artisan migrate --force`)
- [ ] Cache yaradıldı (`php artisan optimize`)
- [ ] Storage link (`php artisan storage:link`)

### Testing
- [ ] Site açılır (https://exondr.az)
- [ ] Login işləyir
- [ ] Database əlaqəsi var
- [ ] Doctor panel test edildi
- [ ] Admin panel test edildi
- [ ] Registrar panel test edildi
- [ ] PWA quraşdırılır (mobil-də)

---

## 10. Gələcək Yeniləmələr (Update Prosesi)

### Kod Yeniləməsi
```bash
# 1. Yeni faylları yüklə (FTP)

# 2. SSH-də
cd /home/exon/public_html

# 3. Cache-ləri təmizlə
php artisan down   # Maintenance mode
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Composer update (əgər lazımsa)
composer install --no-dev --optimize-autoloader

# 5. Migrations (əgər yeni varsa)
php artisan migrate --force

# 6. Cache yenidən yarat
php artisan optimize

# 7. Maintenance mode-dan çıx
php artisan up
```

---

## Əlaqə və Dəstək

**Hosting İdarəetməsi**: cPanel və ya hosting provider panel
**SSH Access**: Hosting provider tərəfindən verilməlidir
**Database Management**: phpMyAdmin və ya MySQL Workbench

**Təcili Problem**: `storage/logs/laravel.log` faylına baxın

---

*EXON Klinika - Production Deployment Guide*
*Son yeniləmə: 28 Yanvar 2026*
