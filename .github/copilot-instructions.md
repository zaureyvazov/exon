# EXON Klinika - AI Coding Agent Instructions

## Project Overview
Medical clinic patient referral and analysis management system built with **Laravel 10 + Blade** + Vite. The application is in **Azerbaijani language** - all UI text, comments, database fields, and messages use Azerbaijani.

**Primary Domain:** Private clinic workflow where doctors create patient analysis referrals, registrars approve them, and admins manage the entire system including pricing and commissions.

## Architecture & Key Patterns

### Role-Based Access Control (RBAC)
- **3 Roles:** `admin`, `doctor`, `registrar` (defined in `roles` table)
- Middleware: `CheckRole` middleware checks user roles via `User::hasRole($role)`
- Route structure: Namespaced by role (`admin.*`, `doctor.*`, `registrar.*`)
- Data isolation: Doctors see ONLY their own patients/referrals via query scopes (`where('doctor_id', Auth::id())`)

### Performance Optimization (Critical for 1000+ Analyses)
**Database Indexes:** See migration `2026_01_20_103437_add_performance_indexes_to_tables.php`
- Index on: `analyses.category_id`, `analyses.is_active`, `referrals.is_approved`, `patients.fin_code`
- Always filter by indexed columns when querying large tables

**Caching Strategy:** Use `CachesAnalyses` trait for analysis data
```php
use App\Http\Controllers\Traits\CachesAnalyses;
$analyses = $this->getCachedActiveAnalyses(); // 1 hour cache
$this->clearAnalysesCache(); // Clear after admin updates
```

**Service Layer:** `CacheService` provides centralized caching (5-min TTL by default)

### Domain Models & Relationships

**Core Entities:**
- `User` (doctors/admins/registrars) → many-to-many `roles` via `user_roles`
- `Patient` → belongs to doctor (`registered_by`)
- `Referral` → belongs to `patient` and `doctor`, many-to-many `analyses` via `referral_analyses` pivot
- `Analysis` → belongs to `AnalysisCategory`, has `price` and `commission_percentage`
- `Payment` → tracks doctor commission payouts

**Pivot Table Pattern:** `referral_analyses` stores snapshot data:
- `analysis_price`, `commission_percentage` (frozen at creation time)
- `discount_commission_rate`, `is_cancelled`, `cancellation_reason`

### Discount & Commission System
**Referral Pricing:** When admin applies discount to a referral:
1. Set `discount_type` (percentage/flat), `discount_value`, `discount_reason`
2. Calculate `final_price` and `doctor_commission` (stored on `referrals` table)
3. Individual analysis commissions stored in `referral_analyses.discount_commission_rate`

**Why Snapshots?** Price/commission changes shouldn't affect historical referrals.

## Development Workflows

### Setup Commands
```bash
# Initial setup
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed  # Creates admin@exon.com, doctor@exon.com, registrar@exon.com (password: password)

# Development
php artisan serve  # Backend
npm run dev        # Vite (frontend assets)
```

### Database Workflow
- Migrations are dated chronologically (latest performance indexes: `2026_01_20_*`)
- Seeders: `RoleSeeder`, `UserSeeder`, `AnalysisSeeder`
- Test accounts in README.md for each role

### Testing Strategy
**Manual Testing:** Use demo accounts (see README.md)
- Test role isolation: Doctor should NEVER see other doctors' data
- Test approval flow: Doctor creates → Registrar approves → Admin prices

## Critical Conventions

### Language & Localization
**ALL user-facing text is Azerbaijani:** 
- View labels: "Təsdiq" (Approve), "Göndəriş" (Referral), "Xəstə" (Patient)
- Flash messages: Use Azerbaijani in controller redirects
- Validation messages: Default Laravel messages (English) - consider translating

### Naming Patterns
**Routes:** `{role}.{resource}.{action}` (e.g., `doctor.referrals.create`)
**Controllers:** Namespaced by role (`App\Http\Controllers\Doctor\DoctorController`)
**Views:** `resources/views/{role}/{resource}/{action}.blade.php`

### UI Components
**Blade Templates:** Bootstrap 5 + Bootstrap Icons
- Sticky category headers in analysis selection (`resources/views/doctor/referrals/create.blade.php`)
- Real-time search filtering without page reload
- Toast notifications for success/error messages

### Security Best Practices (See SECURITY_CHECKLIST.md)
- ✅ CSRF tokens on ALL forms (`@csrf`)
- ✅ XSS protection via Blade `{{ }}` escaping (NEVER use `{!! !!}` for user input)
- ✅ SQL injection prevention via Eloquent ORM (avoid raw queries)
- ✅ Rate limiting on login (`throttle.login` middleware)
- ✅ Password hashing via `Hash::make()` (bcrypt)

## Integration Points

### Notifications System
**Database-Driven:** `notifications` table stores user notifications
- Unread count endpoint: `GET /notifications/unread`
- Mark as read: `POST /notifications/{id}/read`

### Messaging System
**Doctor ↔ Registrar Communication:**
- Real-time message count: `GET /messages/unread/count`
- Conversation view: `GET /messages/{userId}`

### Export Functionality
**Excel Exports:** Uses `maatwebsite/excel` package
- `DoctorAnalysisCategoryExport` - Doctor-specific analysis category reports
- `ReportExport` - General reporting (admin)

## Common Tasks

### Adding New Analysis
1. Admin navigates to `/admin/analyses/create`
2. Must assign to `AnalysisCategory` (required field)
3. Set `price`, `commission_percentage`, `is_active` status
4. **IMPORTANT:** Clear cache via `$this->clearAnalysesCache()` in `AdminController::storeAnalysis()`

### Creating Referral (Doctor Workflow)
1. Select patient (or create new via 7-char FIN code)
2. Select analyses (UI shows by category, cached for performance)
3. System calculates total price at creation time
4. Referral starts as `is_approved = false` (pending registrar)

### Approving Referral (Registrar Workflow)
1. View referral details at `/registrar/referrals/{id}`
2. Click "Təsdiqlə" (Approve) - sets `is_approved = true`, `approved_at`, `approved_by`
3. Rejection sets `is_approved = false` again

## File Reference Guide
- **Role middleware:** `app/Http/Middleware/CheckRole.php`
- **Cache trait:** `app/Http/Controllers/Traits/CachesAnalyses.php`
- **Performance docs:** `PERFORMANCE_OPTIMIZATION.md` (latest optimizations 2026-01-20)
- **Security checklist:** `SECURITY_CHECKLIST.md`
- **Seeders:** `database/seeders/` (demo accounts)
- **Routes:** `routes/web.php` (all role-namespaced routes)

## Debugging Tips
- Check active sessions: Admin panel → Active Sessions
- Verify indexes: Review migration `2026_01_20_103437_add_performance_indexes_to_tables.php`
- Cache issues: Clear with `php artisan cache:clear` or `$this->clearAnalysesCache()` in code
- Role access denied: Verify user has correct role in `user_roles` table
