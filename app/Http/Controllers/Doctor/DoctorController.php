<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CachesAnalyses;
use App\Models\Patient;
use App\Models\Analysis;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    use CachesAnalyses;
    /**
     * Show doctor dashboard.
     */
    public function dashboard()
    {
        $doctorId = Auth::id();

        // Optimize stats with single query
        $referralStats = Referral::where('doctor_id', $doctorId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            ->selectRaw('SUM(CASE WHEN is_approved = 1 AND is_priced = 0 THEN 1 ELSE 0 END) as awaiting_pricing')
            ->first();

        $stats = [
            'total_patients' => Patient::where('registered_by', $doctorId)->count(),
            'total_referrals' => $referralStats->total ?? 0,
            'pending_referrals' => $referralStats->pending ?? 0,
            'completed_referrals' => $referralStats->completed ?? 0,
            'awaiting_pricing' => $referralStats->awaiting_pricing ?? 0,
        ];

        $recentReferrals = Referral::with(['patient', 'analyses'])
            ->where('doctor_id', $doctorId)
            ->latest()
            ->limit(5)
            ->get();

        return view('doctor.dashboard', compact('stats', 'recentReferrals'));
    }

    /**
     * Show patient registration form.
     */
    public function createPatient()
    {
        return view('doctor.patients.create');
    }

    /**
     * Store a new patient.
     */
    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'serial_prefix' => 'nullable|string|max:10',
            'serial_number' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
        ], [
            'name.required' => 'Ad tələb olunur',
            'father_name.required' => 'Ata adı tələb olunur',
            'surname.required' => 'Soyad tələb olunur',
            'phone.required' => 'Əlaqə nömrəsi tələb olunur',
        ]);

        // Seriya nömrəsini birləşdir
        if ($request->filled('serial_prefix') && $request->filled('serial_number')) {
            $validated['serial_number'] = $request->serial_prefix . $request->serial_number;
        } else {
            $validated['serial_number'] = null;
        }

        unset($validated['serial_prefix']);
        $validated['registered_by'] = Auth::id();

        $patient = Patient::create($validated);

        return redirect()->route('doctor.referrals.create', $patient->id)
            ->with('success', 'Xəstə uğurla qeydiyyatdan keçdi');
    }

    /**
     * Show all patients.
     */
    public function patients()
    {
        $patients = Patient::where('registered_by', Auth::id())
            ->withCount('referrals')
            ->latest()
            ->paginate(15);

        return view('doctor.patients.index', compact('patients'));
    }

    /**
     * Show referral creation form.
     */
    public function createReferral($patientId = null)
    {
        $doctorId = Auth::id();
        $patient = null;
        if ($patientId) {
            $patient = Patient::where('registered_by', $doctorId)->findOrFail($patientId);
        }

        // Only select necessary columns
        $patients = Patient::where('registered_by', $doctorId)
            ->select('id', 'name', 'father_name', 'surname', 'serial_number')
            ->orderBy('name')
            ->get();

        // Use cached analyses - 1000 analiz üçün optimal
        $analyses = $this->getCachedAnalysesByCategory();

        // Get doctor's most popular analyses
        $popularAnalyses = Analysis::getPopularForDoctor($doctorId);

        return view('doctor.referrals.create', compact('patients', 'analyses', 'patient', 'popularAnalyses'));
    }

    /**
     * Store a new referral.
     */
    public function storeReferral(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'analyses' => 'required|array|min:1',
            'analyses.*' => 'exists:analyses,id',
            'notes' => 'nullable|string',
        ], [
            'patient_id.required' => 'Xəstə seçilməlidir',
            'analyses.required' => 'Ən azı bir analiz seçilməlidir',
        ]);

        DB::beginTransaction();
        try {
            $referral = Referral::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => Auth::id(),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'sent_at' => now(),
            ]);

            // Attach analyses with price and commission snapshots
            $syncData = [];
            $totalProgramCommission = 0;

            foreach ($validated['analyses'] as $analysisId) {
                $analysis = Analysis::findOrFail($analysisId);

                // Calculate program commission (2% of price without tax)
                $programCommission = round($analysis->price * 0.02, 2);
                $totalProgramCommission += $programCommission;

                // Save current price and commission rates as snapshot
                $syncData[$analysisId] = [
                    'analysis_price' => $analysis->price,
                    'commission_percentage' => $analysis->commission_percentage,
                    'discount_commission_rate' => $analysis->discount_commission_rate ?? 0,
                    'program_commission' => $programCommission,
                ];
            }

            $referral->analyses()->attach($syncData);

            // Update total program commission on referral
            $referral->update(['total_program_commission' => $totalProgramCommission]);

            DB::commit();

            return redirect()->route('doctor.dashboard')
                ->with('success', 'Göndəriş uğurla yaradıldı');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Show all referrals.
     */
    public function referrals()
    {
        $referrals = Referral::with(['patient', 'analyses'])
            ->where('doctor_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('doctor.referrals.index', compact('referrals'));
    }

    /**
     * Show referral details.
     */
    public function showReferral($id)
    {
        $referral = Referral::with(['patient', 'analyses', 'doctor'])
            ->where('doctor_id', Auth::id())
            ->findOrFail($id);

        return view('doctor.referrals.show', compact('referral'));
    }

    /**
     * Show doctor's balance.
     */
    public function balance()
    {
        $user = Auth::user();
        $totalBalance = $user->getBalance();
        $paidBalance = $user->getTotalPaidAmount();
        $remainingBalance = $user->getRemainingBalance();
        $balanceDetails = $user->getBalanceDetails();

        // Get payment history
        $payments = $user->paymentsReceived()->with('admin')->latest()->get();

        $stats = [
            'total_balance' => $totalBalance,
            'paid_balance' => $paidBalance,
            'remaining_balance' => $remainingBalance,
            'total_approved_referrals' => $user->referrals()->where('is_approved', true)->count(),
            'total_referrals' => $user->referrals()->count(),
            'pending_referrals' => $user->referrals()->where('is_approved', false)->count(),
        ];

        return view('doctor.balance', compact('totalBalance', 'paidBalance', 'remainingBalance', 'balanceDetails', 'stats', 'payments'));
    }
}
