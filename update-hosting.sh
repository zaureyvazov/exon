#!/bin/bash

echo "🔄 EXON Klinika - GitHub Yeniləmə Scripti"
echo "========================================"
echo ""

# 1. Git pull from main branch
echo "📥 1. GitHub-dan son dəyişikliklər çəkilir..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "❌ Git pull uğursuz oldu! Konfliktləri həll edin."
    exit 1
fi

# 2. Composer dependencies update
echo "📦 2. Composer dependencies yenilənir..."
composer install --no-dev --optimize-autoloader

# 3. Database migration (yeni migration varsa)
echo "💾 3. Yeni migration-lar yoxlanılır..."
php artisan migrate --force

# 4. Cache təmizləmə və yenidən yaratma
echo "🧹 4. Cache təmizlənir və yenilənir..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Cache optimize
echo "⚡ 5. Optimize edilir..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. İzinlər (hosting üçün lazım olarsa)
echo "🔒 6. Fayl icazələri yoxlanılır..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "✅ Yeniləmə tamamlandı!"
echo "🌐 Sayt yeniləmələr ilə işləməyə hazırdır."
echo ""
