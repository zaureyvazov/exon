<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CachesAnalyses;
use App\Models\User;
use App\Models\Role;
use App\Models\Analysis;
use App\Models\AnalysisCategory;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\ProgramCommissionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use CachesAnalyses;
    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        $doctorRole = Role::where('name', 'doctor')->first();
        $registrarRole = Role::where('name', 'registrar')->first();

        $stats = [
            'total_users' => User::count(),
            'total_doctors' => User::where('role_id', $doctorRole->id)->count(),
            'total_registrars' => User::where('role_id', $registrarRole->id)->count(),
            'total_referrals' => Referral::notCancelled()->count(),
            'total_analyses' => Analysis::count(),
            'awaiting_commission' => Referral::where('is_approved', true)
                ->where('discount_type', '!=', 'none')
                ->where('is_priced', false)
                ->notCancelled()
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function charts()
    {
        // Aylıq göndərişlər (son 6 ay)
        $monthlyReferrals = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->locale('az')->translatedFormat('M Y');
            $monthlyReferrals[] = Referral::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->notCancelled()
                ->count();
        }

        // Ən çox istənilən 5 analiz
        $topAnalyses = DB::table('referral_analyses')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->select('analyses.name', DB::raw('COUNT(referral_analyses.id) as count'))
            ->where('referral_analyses.is_cancelled', false)
            ->groupBy('referral_analyses.analysis_id', 'analyses.name')
            ->orderByRaw('COUNT(referral_analyses.id) DESC')
            ->limit(5)
            ->get();

        // Ən aktiv 5 doktor
        $doctorRole = Role::where('name', 'doctor')->first();
        $doctorPerformance = User::where('role_id', $doctorRole->id)
            ->withCount(['referrals' => function($query) {
                $query->notCancelled();
            }])
            ->having('referrals_count', '>', 0)
            ->orderBy('referrals_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($doctor) {
                return [
                    'name' => $doctor->name . ' ' . $doctor->surname,
                    'referrals_count' => $doctor->referrals_count,
                ];
            });

        // Gəlir trendi (son 6 ay) - Optimized with single query
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $revenueData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->where('referrals.is_approved', true)
            ->where('referrals.created_at', '>=', $sixMonthsAgo)
            ->selectRaw('YEAR(referrals.created_at) as year')
            ->selectRaw('MONTH(referrals.created_at) as month')
            ->selectRaw('SUM(referral_analyses.analysis_price) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $revenueTrend = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueLabels[] = $date->locale('az')->translatedFormat('M Y');
            $key = $date->format('Y-m');
            $revenueTrend[] = (float) ($revenueData->get($key)->total ?? 0);
        }

        // Xəstə sayı dinamikası (son 6 ay)
        $patientDynamics = [];
        $patientLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $patientLabels[] = $date->locale('az')->translatedFormat('M Y');
            $patientDynamics[] = \App\Models\Patient::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return view('admin.dashboard-charts', compact(
            'monthlyReferrals',
            'monthlyLabels',
            'topAnalyses',
            'doctorPerformance',
            'revenueTrend',
            'revenueLabels',
            'patientDynamics',
            'patientLabels'
        ));
    }

    /**
     * Show all users.
     */
    public function users(Request $request)
    {
        $query = User::with('role');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('surname', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user creation form.
     */
    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a new user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'hospital' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Ad tələb olunur',
            'surname.required' => 'Soyad tələb olunur',
            'username.required' => 'İstifadəçi adı tələb olunur',
            'username.unique' => 'Bu istifadəçi adı artıq istifadə olunur',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'password.required' => 'Şifrə tələb olunur',
            'password.min' => 'Şifrə minimum 6 simvol olmalıdır',
            'password.confirmed' => 'Şifrə təsdiqi uyğun gəlmir',
            'role_id.required' => 'Rol seçilməlidir',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'username' => strtolower($validated['username']),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'hospital' => $validated['hospital'] ?? null,
            'position' => $validated['position'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'İstifadəçi uğurla yaradıldı');
    }

    /**
     * Show all analyses.
     */
    public function analyses(Request $request)
    {
        $query = Analysis::with('category');

        // Filter active/inactive
        if (!$request->has('show_inactive')) {
            $query->where('is_active', true);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ozel_kod', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $analyses = $query->latest()->paginate(15);
        return view('admin.analyses.index', compact('analyses'));
    }

    /**
     * Show analysis creation form.
     */
    public function createAnalysis()
    {
        return view('admin.analyses.create');
    }

    /**
     * Store a new analysis.
     */
    public function storeAnalysis(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:analysis_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'discount_commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ], [
            'category_id.required' => 'Analiz növü seçilməlidir',
            'category_id.exists' => 'Seçilmiş analiz növü mövcud deyil',
            'commission_percentage.required' => 'Komisyon faizi tələb olunur',
            'commission_percentage.max' => 'Komisyon faizi 100-dən çox ola bilməz',
            'discount_commission_rate.required' => 'Endirimli komisyon faizi tələb olunur',
        ]);

        Analysis::create($validated);

        // Clear analyses cache
        $this->clearAnalysesCache();

        return redirect()->route('admin.analyses')
            ->with('success', 'Analiz uğurla yaradıldı');
    }

    /**
     * Show edit form for analysis.
     */
    public function editAnalysis($id)
    {
        $analysis = Analysis::findOrFail($id);
        return view('admin.analyses.edit', compact('analysis'));
    }

    /**
     * Update analysis.
     */
    public function updateAnalysis(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:analysis_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'discount_commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ], [
            'category_id.required' => 'Analiz növü seçilməlidir',
            'category_id.exists' => 'Seçilmiş analiz növü mövcud deyil',
        ]);

        $analysis = Analysis::findOrFail($id);
        $analysis->update($validated);

        // Clear analyses cache
        $this->clearAnalysesCache();

        return redirect()->route('admin.analyses')
            ->with('success', 'Analiz uğurla yeniləndi');
    }

    /**
     * Delete analysis.
     */
    public function deleteAnalysis($id)
    {
        $analysis = Analysis::findOrFail($id);

        // Check if analysis can be deleted
        if (!$analysis->canBeDeleted()) {
            // Deactivate instead
            $analysis->update(['is_active' => false]);

            // Clear cache when status changes
            $this->clearAnalysesCache();

            return redirect()->route('admin.analyses')
                ->with('warning', 'Analiz artıq istifadədədir, deaktiv edildi');
        }

        $analysis->delete();

        // Clear analyses cache
        $this->clearAnalysesCache();

        return redirect()->route('admin.analyses')
            ->with('success', 'Analiz uğurla silindi');
    }

    /**
     * Show edit form for user.
     */
    public function editUser($id)
    {
        $user = User::with('role')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'hospital' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'username' => strtolower($validated['username']),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'hospital' => $validated['hospital'] ?? null,
            'position' => $validated['position'] ?? null,
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')
            ->with('success', 'İstifadəçi uğurla yeniləndi');
    }

    /**
     * Delete user.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Öz hesabınızı silə bilməzsiniz');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'İstifadəçi uğurla silindi');
    }

    /**
     * Show all doctors' balances.
     */
    public function balances(Request $request)
    {
        $doctorRole = Role::where('name', 'doctor')->first();
        $query = User::where('role_id', $doctorRole->id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('surname', 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(name, ' ', surname)"), 'LIKE', "%{$search}%");
            });
        }

        // Optimize: Load all relationships at once
        $doctors = $query->with([
            'role',
            'paymentsReceived',
            'referrals' => function($q) {
                $q->notCancelled()->select('id', 'doctor_id', 'is_approved', 'is_priced', 'doctor_commission', 'discount_type', 'is_cancelled');
            },
            'referrals.analyses' => function($q) {
                $q->select('analyses.id', 'analyses.name');
            }
        ])->get();

        $balanceData = [];
        foreach ($doctors as $doctor) {
            // Calculate balance from loaded relationships (no extra queries)
            $totalBalance = 0;

            foreach ($doctor->referrals as $referral) {
                if ($referral->is_approved && $referral->is_priced) {
                    if ($referral->discount_type !== 'none') {
                        // Discounted referral
                        $totalBalance += $referral->doctor_commission ?? 0;
                    } else {
                        // Normal referral - calculate from analyses
                        foreach ($referral->analyses as $analysis) {
                            if (!($analysis->pivot->is_cancelled ?? false)) {
                                $snapshotPrice = $analysis->pivot->analysis_price;
                                $commissionRate = $analysis->pivot->commission_percentage ?? 20;
                                $totalBalance += $snapshotPrice * $commissionRate / 100;
                            }
                        }
                    }
                }
            }

            $paidBalance = $doctor->paymentsReceived->sum('amount');
            $remainingBalance = $totalBalance - $paidBalance;

            $approvedReferrals = $doctor->referrals->filter(function($r) {
                return $r->is_approved && $r->is_priced;
            })->count();

            $balanceData[] = [
                'doctor' => $doctor,
                'balance' => $totalBalance,
                'paid_balance' => $paidBalance,
                'remaining_balance' => $remainingBalance,
                'payment_count' => $doctor->paymentsReceived->count(),
                'approved_referrals' => $approvedReferrals,
                'total_referrals' => $doctor->referrals->count(),
            ];
        }

        // Sort by requested column
        $sortBy = $request->get('sort_by', 'balance');
        $sortOrder = $request->get('sort_order', 'desc');

        $balanceData = collect($balanceData)->sortBy($sortBy, SORT_REGULAR, $sortOrder === 'desc')->values()->all();

        return view('admin.balances', compact('balanceData', 'sortBy', 'sortOrder'));
    }

    /**
     * Show doctor balance details.
     */
    public function doctorBalanceDetail($id)
    {
        $doctor = User::with(['role', 'referrals', 'paymentsReceived.admin'])->findOrFail($id);

        // Check if user is doctor
        if (!$doctor->isDoctor()) {
            return redirect()->route('admin.balances')->with('error', 'İstifadəçi doktor deyil');
        }

        // Get balance details with date filtering
        $query = $doctor->referrals()
            ->where('is_approved', true)
            ->where('is_priced', true)
            ->notCancelled()
            ->with(['patient', 'analyses']);

        // Apply date filters
        if (request('start_date')) {
            $query->whereDate('updated_at', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('updated_at', '<=', request('end_date'));
        }

        $approvedReferrals = $query->get();

        $normalReferrals = [];
        $discountedReferrals = [];
        $totalBalance = 0;

        foreach ($approvedReferrals as $referral) {
            // Check if referral has discount
            $hasDiscount = $referral->discount_type !== 'none';

            if ($hasDiscount) {
                // Discounted referral - admin sets commission manually
                $discountedReferrals[] = [
                    'referral_id' => $referral->id,
                    'patient_name' => $referral->patient->full_name,
                    'patient_fin' => $referral->patient->serial_number ?? '-',
                    'final_price' => $referral->final_price,
                    'discount_type' => $referral->discount_type,
                    'discount_value' => $referral->discount_value,
                    'commission_amount' => $referral->doctor_commission,
                    'date' => $referral->updated_at,
                    'status' => $referral->status,
                    'analyses_count' => $referral->analyses->count(),
                ];
                $totalBalance += $referral->doctor_commission;
            } else {
                // Normal referral - calculate commission from percentage
                foreach ($referral->analyses as $analysis) {
                    if (!($analysis->pivot->is_cancelled ?? false)) {
                        $snapshotPrice = $analysis->pivot->analysis_price;
                        $commissionRate = $analysis->pivot->commission_percentage ?? 20;
                        $commissionAmount = $snapshotPrice * $commissionRate / 100;
                        $totalBalance += $commissionAmount;

                        $normalReferrals[] = [
                            'referral_id' => $referral->id,
                            'patient_name' => $referral->patient->full_name,
                            'patient_fin' => $referral->patient->serial_number ?? '-',
                            'analysis_name' => $analysis->name,
                            'analysis_price' => $snapshotPrice,
                            'commission_percentage' => $commissionRate,
                            'commission_amount' => $commissionAmount,
                            'date' => $referral->updated_at,
                            'status' => $referral->status,
                        ];
                    }
                }
            }
        }

        $stats = [
            'total_referrals' => $doctor->referrals()->notCancelled()->count(),
            'approved_referrals' => $approvedReferrals->count(),
            'total_balance' => $totalBalance,
            'normal_count' => count($normalReferrals),
            'discounted_count' => count($discountedReferrals),
        ];

        return view('admin.doctor-balance-detail', compact('doctor', 'normalReferrals', 'discountedReferrals', 'stats'));
    }

    /**
     * Show discounted referrals awaiting commission pricing.
     */
    public function discountedReferrals()
    {
        $referrals = Referral::with(['doctor', 'patient', 'analyses'])
            ->where('is_approved', true)
            ->where('discount_type', '!=', 'none')
            ->where('is_priced', false)
            ->notCancelled()
            ->latest()
            ->paginate(20);

        return view('admin.discounted-referrals', compact('referrals'));
    }

    /**
     * Show all non-discounted referrals.
     */
    public function nonDiscountedReferrals(Request $request)
    {
        $query = Referral::with(['doctor', 'patient', 'analyses'])
            ->where('discount_type', 'none')
            ->notCancelled();

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Doctor filter
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Patient search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $referrals = $query->latest()->paginate(20);

        $doctorRole = Role::where('name', 'doctor')->first();
        $doctors = User::where('role_id', $doctorRole->id)->orderBy('name')->get();

        return view('admin.referrals.non-discounted', compact('referrals', 'doctors'));
    }

    /**
     * Show all discounted referrals (including priced ones).
     */
    public function allDiscountedReferrals(Request $request)
    {
        $query = Referral::with(['doctor', 'patient', 'analyses'])
            ->where('discount_type', '!=', 'none')
            ->notCancelled();

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Doctor filter
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Patient search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $referrals = $query->latest()->paginate(20);

        $doctorRole = Role::where('name', 'doctor')->first();
        $doctors = User::where('role_id', $doctorRole->id)->orderBy('name')->get();

        return view('admin.referrals.discounted', compact('referrals', 'doctors'));
    }

    /**
     * Show referral details.
     */
    public function showReferral($id)
    {
        $referral = Referral::with(['doctor', 'patient', 'analyses', 'approvedBy', 'cancelledBy'])->findOrFail($id);

        return view('admin.referrals.show', compact('referral'));
    }

    /**
     * Set commission for discounted referral.
     */
    public function setCommission(Request $request, $id)
    {
        $validated = $request->validate([
            'doctor_commission' => 'required|numeric|min:0',
        ]);

        $referral = Referral::findOrFail($id);

        $referral->update([
            'doctor_commission' => $validated['doctor_commission'],
            'is_priced' => true,
        ]);

        return back()->with('success', 'Komissiya məbləği təyin edildi');
    }

    /**
     * Show program commission balance and payments.
     */
    public function programCommission(Request $request)
    {
        // Get approved referrals with program commission
        $referralsQuery = Referral::with(['patient', 'doctor'])
            ->where('is_approved', true)
            ->where('total_program_commission', '>', 0)
            ->notCancelled();

        // Date filter
        if ($request->filled('date')) {
            $referralsQuery->whereDate('approved_at', $request->date);
        }

        // Patient search
        if ($request->filled('search')) {
            $search = $request->search;
            $referralsQuery->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('surname', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%");
            });
        }

        $referrals = $referralsQuery->latest('approved_at')->paginate(10);

        // Calculate total program commission from APPROVED referrals only
        $totalCommission = Referral::where('is_approved', true)
            ->notCancelled()
            ->sum('total_program_commission') ?? 0;

        // Get total paid amount
        $totalPaid = ProgramCommissionPayment::sum('amount') ?? 0;

        // Calculate remaining balance
        $remainingBalance = $totalCommission - $totalPaid;

        // Get payment history
        $payments = ProgramCommissionPayment::with('paidBy')
            ->latest()
            ->paginate(20);

        return view('admin.program-commission', [
            'totalCommission' => (float) $totalCommission,
            'totalPaid' => (float) $totalPaid,
            'remainingBalance' => (float) $remainingBalance,
            'payments' => $payments,
            'referrals' => $referrals,
        ]);
    }

    /**
     * Store program commission payment.
     */
    public function storeProgramCommissionPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Məbləğ daxil edilməlidir',
            'amount.min' => 'Məbləğ 0-dan böyük olmalıdır',
        ]);

        ProgramCommissionPayment::create([
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'paid_by' => Auth::id(),
        ]);

        return back()->with('success', 'Ödəniş uğurla qeyd edildi');
    }

    /**
     * Cancel a referral
     */
    public function cancelReferral(Request $request, $id)
    {
        $referral = Referral::findOrFail($id);

        // Check if referral is approved
        if ($referral->is_approved) {
            return back()->with('error', 'Təsdiqlənmiş göndəriş iptal edilə bilməz. Əvvəlcə təsdiq geri alın');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $referral->update([
            'is_cancelled' => true,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return back()->with('success', 'Göndəriş uğurla iptal edildi');
    }

    /**
     * Uncancel a referral
     */
    public function uncancelReferral($id)
    {
        $referral = Referral::findOrFail($id);

        $referral->update([
            'is_cancelled' => false,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ]);

        return back()->with('success', 'İptal geri alındı');
    }

    /**
     * Show cancelled referrals
     */
    public function cancelledReferrals()
    {
        $referrals = Referral::with(['patient', 'doctor', 'cancelledBy'])
            ->cancelled()
            ->latest('cancelled_at')
            ->paginate(20);

        return view('admin.referrals.cancelled', compact('referrals'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $settings = [
            'doctor_can_see_prices' => Setting::get('doctor_can_see_prices', '0'),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'doctor_can_see_prices' => 'required|in:0,1',
        ]);

        Setting::set('doctor_can_see_prices', $validated['doctor_can_see_prices']);

        return back()->with('success', 'Ayarlar uğurla yeniləndi');
    }

    /**
     * Show active sessions
     */
    public function activeSessions()
    {
        // Get all sessions from database
        $sessions = DB::table('sessions')
            ->orderBy('last_activity', 'desc')
            ->get();

        // Collect all user IDs first (avoid N+1)
        $userIds = [];
        $sessionData = [];

        foreach ($sessions as $session) {
            try {
                // Unserialize session data
                $data = unserialize(base64_decode($session->payload));

                // Check if user is logged in
                if (isset($data['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
                    $userId = $data['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'];
                    $userIds[] = $userId;
                    $sessionData[$session->id] = [
                        'user_id' => $userId,
                        'session' => $session,
                    ];
                }
            } catch (\Exception $e) {
                // Skip invalid session data
                continue;
            }
        }

        // Load all users at once (1 query instead of N queries)
        $users = User::with('role')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        $activeSessions = [];

        foreach ($sessionData as $data) {
            $user = $users->get($data['user_id']);
            if (!$user) {
                continue;
            }

            $session = $data['session'];

            // Parse user agent
            $userAgent = $session->user_agent ?? 'Unknown';
            $browser = $this->getBrowser($userAgent);
            $platform = $this->getPlatform($userAgent);

            // Calculate last activity
            $lastActivity = \Carbon\Carbon::createFromTimestamp($session->last_activity);
            $isActive = $lastActivity->diffInMinutes(now()) < 2; // Active if last activity within 2 minutes

            $activeSessions[] = [
                'user' => $user,
                'ip_address' => $session->ip_address,
                'user_agent' => $userAgent,
                'browser' => $browser,
                'platform' => $platform,
                'last_activity' => $lastActivity,
                'is_active' => $isActive,
            ];
        }

        // Sort by last activity
        usort($activeSessions, function($a, $b) {
            return $b['last_activity']->timestamp <=> $a['last_activity']->timestamp;
        });

        return view('admin.active-sessions', compact('activeSessions'));
    }

    /**
     * Get browser name from user agent
     */
    protected function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            return 'Internet Explorer';
        }

        return 'Unknown';
    }

    /**
     * Get platform from user agent
     */
    protected function getPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac OS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false || strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    /**
     * Get active users count for dashboard widget
     */
    public function getActiveUsersCount()
    {
        $activeThreshold = now()->subMinutes(2);

        $activeCount = DB::table('sessions')
            ->where('last_activity', '>=', $activeThreshold->timestamp)
            ->whereNotNull('user_id')
            ->distinct()
            ->count(DB::raw('DISTINCT user_id'));

        return response()->json([
            'active_count' => $activeCount,
            'threshold_minutes' => 2,
            'updated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Show analysis categories
     */
    public function categories()
    {
        $categories = AnalysisCategory::withCount('analyses')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show category creation form
     */
    public function createCategory()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:analysis_categories,name',
            'description' => 'nullable|string',
        ]);

        AnalysisCategory::create($validated);

        return redirect()->route('admin.categories')->with('success', 'Analiz növü uğurla əlavə edildi');
    }

    /**
     * Show category edit form
     */
    public function editCategory($id)
    {
        $category = AnalysisCategory::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = AnalysisCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:analysis_categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', 'Analiz növü uğurla yeniləndi');
    }

    /**
     * Toggle category active status or delete
     */
    public function deleteCategory($id)
    {
        $category = AnalysisCategory::findOrFail($id);

        // Check if category has analyses
        if ($category->analyses()->count() > 0) {
            // Deactivate instead of delete
            $category->update(['is_active' => false]);
            return back()->with('warning', 'Analiz növü analizlərlə əlaqəlidir, deaktiv edildi');
        }

        // Can safely delete
        $category->delete();
        return back()->with('success', 'Analiz növü silindi');
    }

    /**
     * Toggle category active status
     */
    public function toggleCategory($id)
    {
        $category = AnalysisCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'aktivləşdirildi' : 'deaktivləşdirildi';
        return back()->with('success', "Analiz növü {$status}");
    }

    /**
     * Reports index page
     */
    public function reportsIndex()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate report based on type
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string|in:doctor-analysis-category,financial-summary,daily-revenue,monthly-revenue,patient-statistics,repeat-patients,popular-analyses,analysis-revenue,analysis-by-category,doctor-performance,doctor-ranking,discount-report,referral-status',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $validated['report_type'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Call appropriate report method based on type
        switch ($reportType) {
            case 'doctor-analysis-category':
                return $this->doctorAnalysisCategoryReport($startDate, $endDate);
            case 'financial-summary':
                return $this->financialSummaryReport($startDate, $endDate);
            case 'daily-revenue':
                return $this->dailyRevenueReport($startDate, $endDate);
            case 'monthly-revenue':
                return $this->monthlyRevenueReport($startDate, $endDate);
            case 'patient-statistics':
                return $this->patientStatisticsReport($startDate, $endDate);
            case 'repeat-patients':
                return $this->repeatPatientsReport($startDate, $endDate);
            case 'popular-analyses':
                return $this->popularAnalysesReport($startDate, $endDate);
            case 'analysis-revenue':
                return $this->analysisRevenueReport($startDate, $endDate);
            case 'analysis-by-category':
                return $this->analysisByCategoryReport($startDate, $endDate);
            case 'doctor-performance':
                return $this->doctorPerformanceReport($startDate, $endDate);
            case 'doctor-ranking':
                return $this->doctorRankingReport($startDate, $endDate);
            case 'discount-report':
                return $this->discountReport($startDate, $endDate);
            case 'referral-status':
                return $this->referralStatusReport($startDate, $endDate);
            default:
                return back()->with('error', 'Seçilən raport növü mövcud deyil');
        }
    }

    /**
     * Generate doctor analysis category report
     */
    protected function doctorAnalysisCategoryReport($startDate, $endDate)
    {
        $reportType = 'doctor-analysis-category';

        // Get all doctors with role eager loaded
        $doctors = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'doctor'))
            ->get();

        // Get all active categories
        $categories = AnalysisCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Optimize: Get all counts in ONE query using groupBy
        $counts = DB::table('referrals')
            ->join('referral_analyses', 'referrals.id', '=', 'referral_analyses.referral_id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('referral_analyses.is_cancelled', false)
            ->select('referrals.doctor_id', 'analyses.category_id', DB::raw('COUNT(DISTINCT referrals.id) as count'))
            ->groupBy('referrals.doctor_id', 'analyses.category_id')
            ->get()
            ->groupBy('doctor_id');

        // Build report data from single query result
        $reportData = [];

        foreach ($doctors as $doctor) {
            $doctorCounts = $counts->get($doctor->id, collect());

            $doctorData = [
                'doctor' => $doctor,
                'categories' => [],
                'total' => 0,
            ];

            foreach ($categories as $category) {
                $categoryCount = $doctorCounts->firstWhere('category_id', $category->id);
                $count = $categoryCount ? $categoryCount->count : 0;

                $doctorData['categories'][$category->id] = [
                    'name' => $category->name,
                    'count' => $count,
                ];
                $doctorData['total'] += $count;
            }

            if ($doctorData['total'] > 0) {
                $reportData[] = $doctorData;
            }
        }

        return view('admin.reports.index', compact('reportData', 'categories', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Export report to Excel
     */
    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $reportType = $validated['report_type'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Get formatted data for Excel export
        $exportData = $this->getExportData($reportType, $startDate, $endDate);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReportExport($exportData, $reportType, $startDate, $endDate),
            $this->getReportFileName($reportType) . '-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get export data formatted for Excel
     */
    protected function getExportData($reportType, $startDate, $endDate)
    {
        switch ($reportType) {
            case 'financial-summary':
                return $this->getFinancialSummaryExportData($startDate, $endDate);
            case 'daily-revenue':
                return $this->getDailyRevenueExportData($startDate, $endDate);
            case 'monthly-revenue':
                return $this->getMonthlyRevenueExportData($startDate, $endDate);
            case 'patient-statistics':
                return $this->getPatientStatisticsExportData($startDate, $endDate);
            case 'repeat-patients':
                return $this->getRepeatPatientsExportData($startDate, $endDate);
            case 'popular-analyses':
                return $this->getPopularAnalysesExportData($startDate, $endDate);
            case 'analysis-revenue':
                return $this->getAnalysisRevenueExportData($startDate, $endDate);
            case 'analysis-by-category':
                return $this->getAnalysisByCategoryExportData($startDate, $endDate);
            case 'doctor-performance':
                return $this->getDoctorPerformanceExportData($startDate, $endDate);
            case 'doctor-ranking':
                return $this->getDoctorRankingExportData($startDate, $endDate);
            case 'discount-report':
                return $this->getDiscountReportExportData($startDate, $endDate);
            case 'referral-status':
                return $this->getReferralStatusExportData($startDate, $endDate);
            default:
                return [];
        }
    }

    /**
     * Get report file name
     */
    protected function getReportFileName($reportType)
    {
        $names = [
            'financial-summary' => 'umumi-maliyye-hesabati',
            'daily-revenue' => 'gunluk-gelir',
            'monthly-revenue' => 'ayliq-gelir',
            'patient-statistics' => 'xeste-statistikasi',
            'repeat-patients' => 'tekrar-xesteler',
            'popular-analyses' => 'populyar-analizler',
            'analysis-revenue' => 'analiz-geliri',
            'analysis-by-category' => 'nov-uzre-analizler',
            'doctor-performance' => 'hekim-performans',
            'doctor-ranking' => 'hekim-rankinq',
            'discount-report' => 'endirim-statistikasi',
            'referral-status' => 'gonderis-statusu',
        ];

        return $names[$reportType] ?? 'raport';
    }

    protected function getFinancialSummaryExportData($startDate, $endDate)
    {
        $referrals = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['analyses', 'doctor'])
            ->get();

        $totalRevenue = 0;
        $totalCommissions = 0;
        $doctorCommissions = [];

        foreach ($referrals as $referral) {
            $doctorId = $referral->doctor_id;
            if (!isset($doctorCommissions[$doctorId])) {
                $doctorCommissions[$doctorId] = 0;
            }

            $isDiscounted = $referral->discount_type !== 'none';

            if ($isDiscounted) {
                $totalRevenue += $referral->final_price;
                $commission = $referral->doctor_commission ?? 0;
                $totalCommissions += $commission;
                $doctorCommissions[$doctorId] += $commission;
            } else {
                foreach ($referral->analyses as $analysis) {
                    if (!($analysis->pivot->is_cancelled ?? false)) {
                        $price = $analysis->pivot->analysis_price ?? $analysis->price;
                        $totalRevenue += $price;
                        $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                        $commission = ($price * $commissionRate) / 100;
                        $totalCommissions += $commission;
                        $doctorCommissions[$doctorId] += $commission;
                    }
                }
            }
        }

        $paidAmount = 0;
        $unpaidAmount = 0;

        foreach ($doctorCommissions as $doctorId => $earnedCommission) {
            $doctor = User::find($doctorId);
            if ($doctor) {
                $remaining = $doctor->getRemainingBalance();
                if ($earnedCommission <= $remaining) {
                    $unpaidAmount += $earnedCommission;
                } else {
                    $unpaidAmount += $remaining;
                    $paidAmount += ($earnedCommission - $remaining);
                }
            }
        }

        return [
            ['label' => 'Toplam Gəlir', 'value' => $totalRevenue],
            ['label' => 'Toplam Komissiyalar', 'value' => $totalCommissions],
            ['label' => 'Ödənilmiş Komissiyalar', 'value' => $paidAmount],
            ['label' => 'Ödənilməmiş Komissiyalar', 'value' => $unpaidAmount],
            ['label' => 'Xalis Gəlir', 'value' => $totalRevenue - $totalCommissions],
            ['label' => 'Toplam Göndəriş', 'value' => $referrals->count()],
        ];
    }

    protected function getDailyRevenueExportData($startDate, $endDate)
    {
        $dailyData = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('analyses')
            ->get()
            ->groupBy(function($referral) {
                return $referral->created_at->format('Y-m-d');
            });

        $exportData = [];
        foreach ($dailyData as $date => $dayReferrals) {
            $revenue = 0;
            $commissions = 0;
            foreach ($dayReferrals as $referral) {
                $isDiscounted = $referral->discount_type !== 'none';
                if ($isDiscounted) {
                    $revenue += $referral->final_price;
                    $commissions += $referral->doctor_commission ?? 0;
                } else {
                    foreach ($referral->analyses as $analysis) {
                        if (!($analysis->pivot->is_cancelled ?? false)) {
                            $price = $analysis->pivot->analysis_price ?? $analysis->price;
                            $revenue += $price;
                            $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                            $commissions += ($price * $commissionRate) / 100;
                        }
                    }
                }
            }

            $exportData[] = [
                'date' => \Carbon\Carbon::parse($date)->format('d.m.Y'),
                'revenue' => $revenue,
                'commissions' => $commissions,
                'net_profit' => $revenue - $commissions,
                'referral_count' => $dayReferrals->count(),
            ];
        }

        return $exportData;
    }

    protected function getMonthlyRevenueExportData($startDate, $endDate)
    {
        $monthlyData = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('analyses')
            ->get()
            ->groupBy(function($referral) {
                return $referral->created_at->format('Y-m');
            });

        $exportData = [];
        foreach ($monthlyData as $month => $monthReferrals) {
            $revenue = 0;
            $commissions = 0;
            foreach ($monthReferrals as $referral) {
                $isDiscounted = $referral->discount_type !== 'none';
                if ($isDiscounted) {
                    $revenue += $referral->final_price;
                    $commissions += $referral->doctor_commission ?? 0;
                } else {
                    foreach ($referral->analyses as $analysis) {
                        if (!($analysis->pivot->is_cancelled ?? false)) {
                            $price = $analysis->pivot->analysis_price ?? $analysis->price;
                            $revenue += $price;
                            $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                            $commissions += ($price * $commissionRate) / 100;
                        }
                    }
                }
            }

            $exportData[] = [
                'month' => \Carbon\Carbon::parse($month . '-01')->locale('az')->translatedFormat('F Y'),
                'revenue' => $revenue,
                'commissions' => $commissions,
                'net_profit' => $revenue - $commissions,
                'referral_count' => $monthReferrals->count(),
            ];
        }

        return $exportData;
    }

    protected function getPatientStatisticsExportData($startDate, $endDate)
    {
        $patients = \App\Models\Patient::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('registeredBy')
            ->get();

        $doctorStats = $patients->groupBy('registered_by');

        $exportData = [];
        foreach ($doctorStats as $doctorId => $doctorPatients) {
            $doctor = $doctorPatients->first()->registeredBy;
            if ($doctor) {
                $exportData[] = [
                    'doctor_name' => $doctor->name . ' ' . $doctor->surname,
                    'patient_count' => $doctorPatients->count(),
                ];
            }
        }

        return $exportData;
    }

    protected function getRepeatPatientsExportData($startDate, $endDate)
    {
        $patients = \App\Models\Patient::withCount(['referrals' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->having('referrals_count', '>', 1)
            ->with('registeredBy')
            ->get();

        $exportData = [];
        foreach ($patients as $patient) {
            $exportData[] = [
                'patient_name' => $patient->name . ' ' . $patient->surname,
                'fin' => $patient->serial_number ?? '-',
                'doctor_name' => $patient->registeredBy ? $patient->registeredBy->name . ' ' . $patient->registeredBy->surname : 'N/A',
                'referral_count' => $patient->referrals_count,
            ];
        }

        return $exportData;
    }

    protected function getPopularAnalysesExportData($startDate, $endDate)
    {
        $analysisData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('analyses.name', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('analyses.id', 'analyses.name')
            ->orderByDesc('usage_count')
            ->get();

        return $analysisData->toArray();
    }

    protected function getAnalysisRevenueExportData($startDate, $endDate)
    {
        $analysisData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'analyses.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(referral_analyses.analysis_price) as total_revenue')
            )
            ->groupBy('analyses.id', 'analyses.name')
            ->orderByDesc('total_revenue')
            ->get();

        $exportData = [];
        foreach ($analysisData as $analysis) {
            $exportData[] = [
                'name' => $analysis->name,
                'usage_count' => $analysis->usage_count,
                'total_revenue' => $analysis->total_revenue,
                'avg_price' => $analysis->usage_count > 0 ? $analysis->total_revenue / $analysis->usage_count : 0,
            ];
        }

        return $exportData;
    }

    protected function getAnalysisByCategoryExportData($startDate, $endDate)
    {
        $categoryData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->join('analysis_categories', 'analyses.category_id', '=', 'analysis_categories.id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'analysis_categories.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(referral_analyses.analysis_price) as total_revenue')
            )
            ->groupBy('analysis_categories.id', 'analysis_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return $categoryData->toArray();
    }

    protected function getDoctorPerformanceExportData($startDate, $endDate)
    {
        // Optimize: Single query to get all doctor performance data
        $performanceData = DB::table('referrals')
            ->join('users', 'referrals.doctor_id', '=', 'users.id')
            ->leftJoin('referral_analyses', 'referrals.id', '=', 'referral_analyses.referral_id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where(function($q) {
                $q->where('referral_analyses.is_cancelled', false)
                  ->orWhereNull('referral_analyses.is_cancelled');
            })
            ->select(
                'users.id as doctor_id',
                DB::raw("CONCAT(users.name, ' ', users.surname) as doctor_name"),
                DB::raw('COUNT(DISTINCT referrals.id) as referral_count'),
                DB::raw('SUM(CASE
                    WHEN referrals.discount_type != "none" THEN referrals.final_price
                    ELSE COALESCE(referral_analyses.analysis_price, 0)
                END) as total_revenue'),
                DB::raw('SUM(CASE
                    WHEN referrals.discount_type != "none" THEN COALESCE(referrals.doctor_commission, 0)
                    ELSE (COALESCE(referral_analyses.analysis_price, 0) * COALESCE(referral_analyses.commission_percentage, 20) / 100)
                END) as total_commission')
            )
            ->groupBy('users.id', 'users.name', 'users.surname')
            ->having('referral_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();

        $exportData = [];
        foreach ($performanceData as $data) {
            $exportData[] = [
                'doctor_name' => $data->doctor_name,
                'referral_count' => $data->referral_count,
                'total_revenue' => (float) $data->total_revenue,
                'total_commission' => (float) $data->total_commission,
                'avg_referral_value' => $data->referral_count > 0 ? (float) $data->total_revenue / $data->referral_count : 0,
            ];
        }

        return $exportData;
    }

    protected function getDoctorRankingExportData($startDate, $endDate)
    {
        // Optimize: Get all doctor statistics in single queries
        $doctors = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'doctor'))
            ->select('id', 'name', 'surname')
            ->get();

        // Get referral counts in one query
        $referralCounts = DB::table('referrals')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('doctor_id', DB::raw('COUNT(*) as count'))
            ->groupBy('doctor_id')
            ->pluck('count', 'doctor_id');

        // Get patient counts in one query
        $patientCounts = DB::table('patients')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('registered_by', DB::raw('COUNT(*) as count'))
            ->groupBy('registered_by')
            ->pluck('count', 'registered_by');

        // Get commission totals in one query
        $commissionData = DB::table('referrals')
            ->leftJoin('referral_analyses', 'referrals.id', '=', 'referral_analyses.referral_id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where(function($q) {
                $q->where('referral_analyses.is_cancelled', false)
                  ->orWhereNull('referral_analyses.is_cancelled');
            })
            ->select(
                'referrals.doctor_id',
                DB::raw('SUM(CASE
                    WHEN referrals.discount_type != "none" THEN COALESCE(referrals.doctor_commission, 0)
                    ELSE (COALESCE(referral_analyses.analysis_price, 0) * COALESCE(referral_analyses.commission_percentage, 20) / 100)
                END) as total_commission')
            )
            ->groupBy('referrals.doctor_id')
            ->pluck('total_commission', 'doctor_id');

        $rankingData = [];

        foreach ($doctors as $doctor) {
            $referralCount = $referralCounts->get($doctor->id, 0);
            $patientCount = $patientCounts->get($doctor->id, 0);
            $totalCommission = (float) $commissionData->get($doctor->id, 0);

            if ($referralCount > 0 || $patientCount > 0 || $totalCommission > 0) {
                $rankingData[] = [
                    'doctor_name' => $doctor->name . ' ' . $doctor->surname,
                    'referral_count' => $referralCount,
                    'patient_count' => $patientCount,
                    'total_commission' => $totalCommission,
                    'score' => ($referralCount * 3) + ($patientCount * 2) + ($totalCommission / 10),
                ];
            }
        }

        usort($rankingData, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $exportData = [];
        foreach ($rankingData as $index => $data) {
            $exportData[] = [
                'rank' => $index + 1,
                'doctor_name' => $data['doctor_name'],
                'referral_count' => $data['referral_count'],
                'patient_count' => $data['patient_count'],
                'total_commission' => $data['total_commission'],
                'score' => $data['score'],
            ];
        }

        return $exportData;
    }

    protected function getDiscountReportExportData($startDate, $endDate)
    {
        $discountedReferrals = Referral::where('discount_type', '!=', 'none')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['patient', 'doctor', 'analyses'])
            ->get();

        $exportData = [];
        foreach ($discountedReferrals as $referral) {
            $exportData[] = [
                'patient_name' => $referral->patient->name . ' ' . $referral->patient->surname,
                'doctor_name' => $referral->doctor->name . ' ' . $referral->doctor->surname,
                'date' => $referral->created_at->format('d.m.Y'),
                'analysis_count' => $referral->analyses->count(),
                'final_price' => $referral->final_price,
                'commission' => $referral->doctor_commission,
            ];
        }

        return $exportData;
    }

    protected function getReferralStatusExportData($startDate, $endDate)
    {
        $statusData = Referral::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $exportData = [];
        foreach ($statusData as $data) {
            $statusNames = [
                'pending' => 'Gözləyir',
                'approved' => 'Təsdiqləndi',
                'completed' => 'Tamamlandı',
                'cancelled' => 'Ləğv edildi',
            ];

            $exportData[] = [
                'status' => $statusNames[$data->status] ?? $data->status,
                'count' => $data->count,
            ];
        }

        return $exportData;
    }

    /**
     * Export doctor analysis category report to Excel
     */
    public function exportDoctorAnalysisCategoryReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Get all doctors
        $doctors = User::role('doctor')->get();

        // Get all active categories
        $categories = AnalysisCategory::active()->orderBy('name')->get();

        // Build report data
        $reportData = [];

        foreach ($doctors as $doctor) {
            $doctorData = [
                'doctor' => $doctor->name,
                'categories' => [],
                'total' => 0,
            ];

            foreach ($categories as $category) {
                $count = Referral::where('doctor_id', $doctor->id)
                    ->whereHas('analyses', function($query) use ($category) {
                        $query->where('category_id', $category->id);
                    })
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->count();

                $doctorData['categories'][$category->name] = $count;
                $doctorData['total'] += $count;
            }

            if ($doctorData['total'] > 0) {
                $reportData[] = $doctorData;
            }
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DoctorAnalysisCategoryExport($reportData, $categories, $startDate, $endDate),
            'doktor-analiz-nov-raporu-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Financial Summary Report
     */
    protected function financialSummaryReport($startDate, $endDate)
    {
        $reportType = 'financial-summary';

        // Get all approved referrals in date range
        $referrals = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['analyses', 'doctor'])
            ->get();

        // Calculate totals
        $totalRevenue = 0;
        $totalCommissions = 0;

        // Group referrals by doctor to track commissions
        $doctorCommissions = [];

        foreach ($referrals as $referral) {
            $doctorId = $referral->doctor_id;
            if (!isset($doctorCommissions[$doctorId])) {
                $doctorCommissions[$doctorId] = 0;
            }

            // Check if this is a discounted referral
            $isDiscounted = $referral->discount_type !== 'none';

            if ($isDiscounted) {
                // For discounted referrals, use admin-set commission
                $totalRevenue += $referral->final_price;
                $commission = $referral->doctor_commission ?? 0;
                $totalCommissions += $commission;
                $doctorCommissions[$doctorId] += $commission;
            } else {
                // For normal referrals, calculate commission from each analysis
                foreach ($referral->analyses as $analysis) {
                    if (!($analysis->pivot->is_cancelled ?? false)) {
                        $price = $analysis->pivot->analysis_price ?? $analysis->price;
                        $totalRevenue += $price;

                        $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                        $commission = ($price * $commissionRate) / 100;
                        $totalCommissions += $commission;
                        $doctorCommissions[$doctorId] += $commission;
                    }
                }
            }
        }

        // Calculate paid and unpaid amounts correctly
        $paidAmount = 0;
        $unpaidAmount = 0;

        foreach ($doctorCommissions as $doctorId => $earnedCommission) {
            $doctor = User::find($doctorId);
            if ($doctor) {
                // Get doctor's total paid amount (all time)
                $totalPaid = $doctor->getTotalPaidAmount();

                // Get doctor's total balance (all time)
                $totalBalance = $doctor->getBalance();

                // Calculate what portion of this period's commission is paid
                if ($totalBalance > 0) {
                    // Doctor has remaining balance, means not all commissions are paid
                    $remaining = $doctor->getRemainingBalance();

                    // If earned commission in this period is less than remaining balance
                    if ($earnedCommission <= $remaining) {
                        $unpaidAmount += $earnedCommission;
                    } else {
                        $unpaidAmount += $remaining;
                        $paidAmount += ($earnedCommission - $remaining);
                    }
                } else {
                    // All of this doctor's commissions are paid
                    $paidAmount += $earnedCommission;
                }
            }
        }

        $netProfit = $totalRevenue - $totalCommissions;

        $reportData = [
            'total_revenue' => $totalRevenue,
            'total_commissions' => $totalCommissions,
            'paid_commissions' => $paidAmount,
            'unpaid_commissions' => $unpaidAmount,
            'net_profit' => $netProfit,
            'total_referrals' => $referrals->count(),
        ];

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Daily Revenue Report
     */
    protected function dailyRevenueReport($startDate, $endDate)
    {
        $reportType = 'daily-revenue';

        $dailyData = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('analyses')
            ->get()
            ->groupBy(function($referral) {
                return $referral->created_at->format('Y-m-d');
            })
            ->map(function($dayReferrals) {
                $revenue = 0;
                $commissions = 0;
                foreach ($dayReferrals as $referral) {
                    $isDiscounted = $referral->discount_type !== 'none';

                    if ($isDiscounted) {
                        $revenue += $referral->final_price;
                        $commissions += $referral->doctor_commission ?? 0;
                    } else {
                        foreach ($referral->analyses as $analysis) {
                            if (!($analysis->pivot->is_cancelled ?? false)) {
                                $price = $analysis->pivot->analysis_price ?? $analysis->price;
                                $revenue += $price;

                                $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                                $commission = ($price * $commissionRate) / 100;
                                $commissions += $commission;
                            }
                        }
                    }
                }
                return [
                    'revenue' => $revenue,
                    'commissions' => $commissions,
                    'net_profit' => $revenue - $commissions,
                    'referral_count' => $dayReferrals->count(),
                ];
            })
            ->sortKeys();

        $reportData = $dailyData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Monthly Revenue Report
     */
    protected function monthlyRevenueReport($startDate, $endDate)
    {
        $reportType = 'monthly-revenue';

        $monthlyData = Referral::where('is_approved', true)
            ->where('is_priced', true)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('analyses')
            ->get()
            ->groupBy(function($referral) {
                return $referral->created_at->format('Y-m');
            })
            ->map(function($monthReferrals) {
                $revenue = 0;
                $commissions = 0;
                foreach ($monthReferrals as $referral) {
                    $isDiscounted = $referral->discount_type !== 'none';

                    if ($isDiscounted) {
                        $revenue += $referral->final_price;
                        $commissions += $referral->doctor_commission ?? 0;
                    } else {
                        foreach ($referral->analyses as $analysis) {
                            if (!($analysis->pivot->is_cancelled ?? false)) {
                                $price = $analysis->pivot->analysis_price ?? $analysis->price;
                                $revenue += $price;

                                $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                                $commission = ($price * $commissionRate) / 100;
                                $commissions += $commission;
                            }
                        }
                    }
                }
                return [
                    'revenue' => $revenue,
                    'commissions' => $commissions,
                    'net_profit' => $revenue - $commissions,
                    'referral_count' => $monthReferrals->count(),
                ];
            })
            ->sortKeys();

        $reportData = $monthlyData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Patient Statistics Report
     */
    protected function patientStatisticsReport($startDate, $endDate)
    {
        $reportType = 'patient-statistics';

        // Get patients registered in date range
        $patients = Patient::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('registeredBy')
            ->get();

        // Group by doctor
        $doctorStats = $patients->groupBy('registered_by')->map(function($doctorPatients) {
            return [
                'doctor' => $doctorPatients->first()->registeredBy,
                'patient_count' => $doctorPatients->count(),
                'patients' => $doctorPatients,
            ];
        })->sortByDesc('patient_count');

        $reportData = [
            'total_patients' => $patients->count(),
            'doctor_stats' => $doctorStats,
        ];

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Repeat Patients Report
     */
    protected function repeatPatientsReport($startDate, $endDate)
    {
        $reportType = 'repeat-patients';

        // Get patients with multiple referrals
        $patients = Patient::withCount(['referrals' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->having('referrals_count', '>', 1)
            ->with('registeredBy')
            ->orderByDesc('referrals_count')
            ->get();

        $reportData = [
            'repeat_patients' => $patients,
            'total_repeat_patients' => $patients->count(),
        ];

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Popular Analyses Report
     */
    protected function popularAnalysesReport($startDate, $endDate)
    {
        $reportType = 'popular-analyses';

        // Get analysis usage count
        $analysisData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('analyses.id', 'analyses.name', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('analyses.id', 'analyses.name')
            ->orderByDesc('usage_count')
            ->get();

        $reportData = $analysisData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Analysis Revenue Report
     */
    protected function analysisRevenueReport($startDate, $endDate)
    {
        $reportType = 'analysis-revenue';

        $analysisData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'analyses.id',
                'analyses.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(referral_analyses.analysis_price) as total_revenue')
            )
            ->groupBy('analyses.id', 'analyses.name')
            ->orderByDesc('total_revenue')
            ->get();

        $reportData = $analysisData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Analysis By Category Report
     */
    protected function analysisByCategoryReport($startDate, $endDate)
    {
        $reportType = 'analysis-by-category';

        $categoryData = DB::table('referral_analyses')
            ->join('referrals', 'referral_analyses.referral_id', '=', 'referrals.id')
            ->join('analyses', 'referral_analyses.analysis_id', '=', 'analyses.id')
            ->join('analysis_categories', 'analyses.category_id', '=', 'analysis_categories.id')
            ->where('referrals.is_approved', true)
            ->where('referrals.is_priced', true)
            ->whereBetween('referrals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'analysis_categories.id',
                'analysis_categories.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(referral_analyses.analysis_price) as total_revenue')
            )
            ->groupBy('analysis_categories.id', 'analysis_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $reportData = $categoryData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Doctor Performance Report
     */
    protected function doctorPerformanceReport($startDate, $endDate)
    {
        $reportType = 'doctor-performance';

        $doctors = User::role('doctor')->get();
        $performanceData = [];

        foreach ($doctors as $doctor) {
            $referrals = Referral::where('doctor_id', $doctor->id)
                ->where('is_approved', true)
                ->where('is_priced', true)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('analyses')
                ->get();

            $totalRevenue = 0;
            $totalCommission = 0;

            foreach ($referrals as $referral) {
                $isDiscounted = $referral->discount_type !== 'none';

                if ($isDiscounted) {
                    $totalRevenue += $referral->final_price;
                    $totalCommission += $referral->doctor_commission ?? 0;
                } else {
                    foreach ($referral->analyses as $analysis) {
                        if (!($analysis->pivot->is_cancelled ?? false)) {
                            $price = $analysis->pivot->analysis_price ?? $analysis->price;
                            $totalRevenue += $price;

                            $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                            $commission = ($price * $commissionRate) / 100;
                            $totalCommission += $commission;
                        }
                    }
                }
            }

            $avgReferralValue = $referrals->count() > 0 ? $totalRevenue / $referrals->count() : 0;

            $performanceData[] = [
                'doctor' => $doctor,
                'referral_count' => $referrals->count(),
                'total_revenue' => $totalRevenue,
                'total_commission' => $totalCommission,
                'avg_referral_value' => $avgReferralValue,
            ];
        }

        // Sort by referral count
        usort($performanceData, function($a, $b) {
            return $b['referral_count'] <=> $a['referral_count'];
        });

        $reportData = $performanceData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Doctor Ranking Report
     */
    protected function doctorRankingReport($startDate, $endDate)
    {
        $reportType = 'doctor-ranking';

        $doctors = User::role('doctor')->get();
        $rankingData = [];

        foreach ($doctors as $doctor) {
            $referralCount = Referral::where('doctor_id', $doctor->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count();

            $patientCount = Patient::where('registered_by', $doctor->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count();

            $totalCommission = $doctor->referrals()
                ->where('is_approved', true)
                ->where('is_priced', true)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('analyses')
                ->get()
                ->sum(function($referral) {
                    $commission = 0;
                    $isDiscounted = $referral->discount_type !== 'none';

                    if ($isDiscounted) {
                        $commission = $referral->doctor_commission ?? 0;
                    } else {
                        foreach ($referral->analyses as $analysis) {
                            if (!($analysis->pivot->is_cancelled ?? false)) {
                                $price = $analysis->pivot->analysis_price ?? $analysis->price;
                                $commissionRate = $analysis->pivot->commission_percentage ?? $analysis->commission_percentage;
                                $commission += ($price * $commissionRate) / 100;
                            }
                        }
                    }
                    return $commission;
                });

            if ($referralCount > 0 || $patientCount > 0 || $totalCommission > 0) {
                $rankingData[] = [
                    'doctor' => $doctor,
                    'referral_count' => $referralCount,
                    'patient_count' => $patientCount,
                    'total_commission' => $totalCommission,
                    'score' => ($referralCount * 3) + ($patientCount * 2) + ($totalCommission / 10),
                ];
            }
        }

        // Sort by score
        usort($rankingData, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $reportData = $rankingData;

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Discount Report
     */
    protected function discountReport($startDate, $endDate)
    {
        $reportType = 'discount-report';

        $discountedReferrals = Referral::where('discount_type', '!=', 'none')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['patient', 'doctor', 'analyses'])
            ->get();

        $totalDiscountAmount = $discountedReferrals->sum('doctor_commission');

        $reportData = [
            'discounted_referrals' => $discountedReferrals,
            'total_discount_amount' => $totalDiscountAmount,
            'total_count' => $discountedReferrals->count(),
        ];

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Referral Status Report
     */
    protected function referralStatusReport($startDate, $endDate)
    {
        $reportType = 'referral-status';

        $statusData = Referral::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $approvalData = [
            'approved' => Referral::where('is_approved', true)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'pending_approval' => Referral::where('is_approved', false)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'priced' => Referral::where('is_priced', true)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'pending_pricing' => Referral::where('is_approved', true)
                ->where('is_priced', false)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
        ];

        $reportData = [
            'status_data' => $statusData,
            'approval_data' => $approvalData,
        ];

        return view('admin.reports.index', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }
}
