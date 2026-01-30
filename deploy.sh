#!/bin/bash

echo "🚀 EXON Klinika - Production Deployment Script"
echo "=============================================="
echo ""

# 1. Environment ayarları
echo "📝 1. Environment ayarları kontrol ediliyor..."
if [ ! -f .env ]; then
    echo "⚠️  .env dosyası bulunamadı! .env.production'dan kopyalanıyor..."
    cp .env.production .env
    echo "✅ .env dosyası oluşturuldu. Lütfen veritabanı bilgilerini güncelleyin!"
    exit 1
fi

# 2. Composer dependencies
echo "📦 2. Composer dependencies yükleniyor..."
composer install --no-dev --optimize-autoloader

# 3. Laravel key generate
echo "🔑 3. Application key generate ediliyor..."
php artisan key:generate --force

# 4. Database migration
echo "💾 4. Database migration çalıştırılıyor..."
read -p "Database migration'ı çalıştırmak istediğinize emin misiniz? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]
then
    php artisan migrate --force
    echo "✅ Migration tamamlandı"

    read -p "Seed (test verileri) eklemek istiyor musunuz? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]
    then
        php artisan db:seed --force
        echo "✅ Seeding tamamlandı"
    fi
fi

# 5. Storage link
echo "🔗 5. Storage link oluşturuluyor..."
php artisan storage:link

# 6. Cache temizleme
echo "🧹 6. Cache temizleniyor..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Cache oluşturma
echo "⚡ 7. Cache oluşturuluyor..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Optimize
echo "🚀 8. Optimizasyon yapılıyor..."
php artisan optimize

# 9. İzinler
echo "🔒 9. Dosya izinleri ayarlanıyor..."
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 10. Kontrol
echo ""
echo "✅ Deployment tamamlandı!"
echo ""
echo "📋 Kontrol Listesi:"
echo "  - .env dosyasını kontrol edin (DB, APP_URL, vb.)"
echo "  - Document root /public klasörüne ayarlı mı?"
echo "  - SSL sertifikası kurulu mu?"
echo "  - storage ve bootstrap/cache izinleri 775 mi?"
echo ""
echo "🌐 Test kullanıcıları:"
echo "  Admin: admin@admin.com / password"
echo "  Doktor: doctor@doctor.com / password"
echo "  Qeydiyyatçı: registrar@registrar.com / password"
echo ""
echo "🎉 EXON Klinika kullanıma hazır!"
