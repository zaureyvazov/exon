# Vergi Daxil Qiymətləndirmə Sistemi - Dəyişikliklər

**Tarix:** 24 Yanvar 2026

## Əsas Dəyişikliklər

### 1. Database Səviyyəsi
- `analyses` tablosuna `price_with_tax` sütunu əlavə edildi
- Hesablama: `price * 1.3` (30% vergi)
- Tip: DECIMAL(10,2) GENERATED ALWAYS AS (price * 1.3) STORED

### 2. Referral Model
**Yeni metodlar:**
- `getTotalPriceWithTaxAttribute()` - Ümumi qiyməti vergi daxil hesablayır
- `getFinalPriceWithTaxAttribute()` - Endirimli final qiyməti vergi daxil hesablayır

### 3. Qiymət Göstərilməsi (Rol əsasında)

#### Qeydiyyatçı (Registrar)
**Göstərilən:** Vergi daxil qiymətlər
- Dashboard: `total_price_with_tax`
- Göndəriş siyahısı: `total_price_with_tax`
- Göndəriş detalları: `total_price_with_tax` və `final_price_with_tax`
- Modal hesablama: JavaScript ilə vergi daxil hesablama

**Dəyişdirilən fayllar:**
- `resources/views/registrar/dashboard.blade.php`
- `resources/views/registrar/referrals/index.blade.php`
- `resources/views/registrar/referrals/show.blade.php` (5 yer)

#### Həkim (Doctor)
**Göstərilən:** Qiymətlər dəyişməz qaldı
**Komissiya:** Vergi olmadan (tax-exclusive) qiymət üzərindən hesablanır

**ÖNƏMLİ:** Həkim kommisiyası həmişə `price` (vergi olmadan) üzərindən hesablanır!

#### Admin
**Göstərilən:** Həm vergi daxil, həm də vergi olmadan qiymətlər
- Format: Ana qiymət + kiçik mətn ilə vergi daxil göstərilir

**Dəyişdirilən fayllar:**
- `resources/views/admin/doctor-balance-detail.blade.php`
- `resources/views/admin/discounted-referrals.blade.php`
- `resources/views/admin/reports/partials/discount-report.blade.php`

### 4. Biznes Məntiq

#### Qiymət Saxlanması
Database-də **vergi olmadan** qiymətlər saxlanılır:
- `referrals.final_price` - vergi olmadan
- `referral_analyses.analysis_price` - vergi olmadan

#### Qiymət Göstərilməsi
View-lərdə **accessor** vasitəsilə vergi əlavə olunur:
- Qeydiyyatçı üçün: `total_price_with_tax`, `final_price_with_tax`
- Admin üçün: həm `price` həm də `price * 1.3`

#### Komissiya Hesablanması
**Həmişə vergi olmadan:**
```php
$commission = ($snapshotPrice * $commissionRate) / 100;
// $snapshotPrice - vergi olmadan qiymət
```

### 5. Test Nəticələri

#### Normal Göndəriş
- Vergi olmadan: 50.00 AZN
- Vergi daxil: 65.00 AZN (50 × 1.3)
- Hesablama: ✓ Düzgün

#### Endirimli Göndəriş (10% endirim)
- Vergi olmadan total: 180.00 AZN
- Vergi daxil total: 234.00 AZN (180 × 1.3)
- Vergi olmadan final: 162.00 AZN
- Vergi daxil final: 210.60 AZN (162 × 1.3)
- Həkim komissiyası: 16.00 AZN (vergi olmadan)
- Hesablama: ✓ Düzgün

### 6. Cache Təmizləməsi
Dəyişikliklər tətbiq olunduqdan sonra:
```bash
php artisan view:clear
php artisan cache:clear
```

## Texniki Detallar

### Migration
`database/migrations/2026_01_24_105738_add_vergi_daxil_qiymet_to_analyses_table.php`

### Accessor Pattern
```php
// Referral Model
public function getTotalPriceWithTaxAttribute() {
    return $this->total_price * 1.3;
}

public function getFinalPriceWithTaxAttribute() {
    if ($this->discount_type === 'none' || !$this->final_price) {
        return $this->total_price_with_tax;
    }
    return $this->final_price * 1.3;
}
```

### Blade Syntax
```blade
{{-- Qeydiyyatçı üçün --}}
{{ number_format($referral->total_price_with_tax, 2) }} AZN

{{-- Admin üçün --}}
<div>{{ number_format($referral->final_price, 2) }} AZN</div>
<small class="text-muted">Vergi daxil: {{ number_format($referral->final_price_with_tax, 2) }} AZN</small>
```

## Gələcək İşlər (Opsional)

1. Export (Excel) funksiyalarında vergi daxil sütun əlavə etmək
2. PDF hesab-fakturalarda vergi daxil qiyməti göstərmək
3. Dashboard statistikalarında vergi daxil məbləğləri əlavə etmək
4. Reporting sistemində vergi daxil analizlər əlavə etmək

## Əlavə Qeydlər

- Vergi nisbəti (1.3) hardcoded-dir. Gələcəkdə config file-a köçürülə bilər.
- Komissiya hesablamaları **heç vaxt** dəyişmədi - həmişə vergi olmadan qiymət üzərindən.
- Database-də əlavə yer tutulması minimal (computed column).
- Performance təsiri yoxdur - accessor pattern istifadə olunur.
