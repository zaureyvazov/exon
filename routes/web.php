<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Registrar\RegistrarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root - session varsa dashboard-a, yoxsa login-ə
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        } elseif ($user->isDoctor()) {
            return redirect('/doctor/dashboard');
        } elseif ($user->isRegistrar()) {
            return redirect('/registrar/dashboard');
        }
        
        return redirect('/dashboard');
    }
    
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle.login')->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/charts', [AdminController::class, 'charts'])->name('charts');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Analysis Management
    Route::get('/analyses', [AdminController::class, 'analyses'])->name('analyses');
    Route::get('/analyses/create', [AdminController::class, 'createAnalysis'])->name('analyses.create');
    Route::post('/analyses', [AdminController::class, 'storeAnalysis'])->name('analyses.store');
    Route::get('/analyses/{id}/edit', [AdminController::class, 'editAnalysis'])->name('analyses.edit');
    Route::put('/analyses/{id}', [AdminController::class, 'updateAnalysis'])->name('analyses.update');
    Route::delete('/analyses/{id}', [AdminController::class, 'deleteAnalysis'])->name('analyses.delete');

    // Doctor Balances
    Route::get('/balances', [AdminController::class, 'balances'])->name('balances');
    Route::get('/balances/doctor/{id}', [AdminController::class, 'doctorBalanceDetail'])->name('balances.doctor');

    // Discounted Referrals
    Route::get('/discounted-referrals', [AdminController::class, 'discountedReferrals'])->name('discounted.referrals');
    Route::post('/discounted-referrals/{id}/set-commission', [AdminController::class, 'setCommission'])->name('discounted.setCommission');

    // All Referrals Management
    Route::get('/referrals/non-discounted', [AdminController::class, 'nonDiscountedReferrals'])->name('referrals.non-discounted');
    Route::get('/referrals/discounted', [AdminController::class, 'allDiscountedReferrals'])->name('referrals.discounted');
    Route::get('/referrals/cancelled', [AdminController::class, 'cancelledReferrals'])->name('referrals.cancelled');
    Route::get('/referrals/{id}', [AdminController::class, 'showReferral'])->name('referrals.show');
    Route::post('/referrals/{id}/cancel', [AdminController::class, 'cancelReferral'])->name('referrals.cancel');
    Route::post('/referrals/{id}/uncancel', [AdminController::class, 'uncancelReferral'])->name('referrals.uncancel');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/active-sessions', [AdminController::class, 'activeSessions'])->name('active.sessions');
    Route::get('/active-users/count', [AdminController::class, 'getActiveUsersCount'])->name('active.users.count');

    // Analysis Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    Route::post('/categories/{id}/toggle', [AdminController::class, 'toggleCategory'])->name('categories.toggle');

    // Reports
    Route::get('/reports', [AdminController::class, 'reportsIndex'])->name('reports.index');
    Route::post('/reports/generate', [AdminController::class, 'generateReport'])->name('reports.generate');
    Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');
    Route::get('/reports/doctor-analysis-category/export', [AdminController::class, 'exportDoctorAnalysisCategoryReport'])->name('reports.doctor-analysis-category.export');

    // Payments
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create/{doctorId}', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');

    // Program Commission
    Route::get('/program-commission', [AdminController::class, 'programCommission'])->name('program.commission');
    Route::post('/program-commission/payment', [AdminController::class, 'storeProgramCommissionPayment'])->name('program.commission.payment');

    // Messages Monitoring
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{senderId}/{receiverId}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
});

// Doctor Routes
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');

    // Patient Management
    Route::get('/patients', [DoctorController::class, 'patients'])->name('patients');
    Route::get('/patients/create', [DoctorController::class, 'createPatient'])->name('patients.create');
    Route::post('/patients', [DoctorController::class, 'storePatient'])->name('patients.store');

    // Referral Management
    Route::get('/referrals', [DoctorController::class, 'referrals'])->name('referrals');
    Route::get('/referrals/create/{patient?}', [DoctorController::class, 'createReferral'])->name('referrals.create');
    Route::post('/referrals', [DoctorController::class, 'storeReferral'])->name('referrals.store');
    Route::get('/referrals/{id}', [DoctorController::class, 'showReferral'])->name('referrals.show');
    Route::get('/referrals/{id}/edit', [DoctorController::class, 'editReferral'])->name('referrals.edit');
    Route::put('/referrals/{id}', [DoctorController::class, 'updateReferral'])->name('referrals.update');

    // Balance
    Route::get('/balance', [DoctorController::class, 'balance'])->name('balance');
});

// Registrar Routes
Route::middleware(['auth', 'role:registrar'])->prefix('registrar')->name('registrar.')->group(function () {
    Route::get('/dashboard', [RegistrarController::class, 'dashboard'])->name('dashboard');

    // Referral Viewing
    Route::get('/referrals', [RegistrarController::class, 'referrals'])->name('referrals');
    Route::get('/referrals/{id}', [RegistrarController::class, 'showReferral'])->name('referrals.show');
    Route::get('/referrals/{id}/edit', [RegistrarController::class, 'editReferral'])->name('referrals.edit');
    Route::put('/referrals/{id}', [RegistrarController::class, 'updateReferral'])->name('referrals.update');
    Route::post('/referrals/{id}/status', [RegistrarController::class, 'updateStatus'])->name('referrals.status');
    Route::post('/referrals/{id}/approve', [RegistrarController::class, 'approveReferral'])->name('referrals.approve');
    Route::post('/referrals/{id}/reject', [RegistrarController::class, 'rejectReferral'])->name('referrals.reject');

    // Patient Viewing
    Route::get('/patients', [RegistrarController::class, 'patients'])->name('patients');
});

// Notification Routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

// Message Routes (for doctors and registrars)
Route::middleware(['auth'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('index');
    Route::get('/{userId}', [\App\Http\Controllers\MessageController::class, 'show'])->name('show');
    Route::post('/', [\App\Http\Controllers\MessageController::class, 'store'])->name('store');
    Route::get('/unread/count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('unread.count');
});

