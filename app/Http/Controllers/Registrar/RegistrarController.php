<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CachesAnalyses;
use App\Models\Referral;
use App\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrarController extends Controller
{
    use CachesAnalyses;

    /**
     * Show registrar dashboard.
     */
    public function dashboard()
    {
        // Optimize stats with single query
        $referralStats = Referral::notCancelled()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today')
            ->first();

        $stats = [
            'total_referrals' => $referralStats->total ?? 0,
            'pending_referrals' => $referralStats->pending ?? 0,
            'completed_referrals' => $referralStats->completed ?? 0,
            'today_referrals' => $referralStats->today ?? 0,
        ];

        $recentReferrals = Referral::with(['patient', 'doctor', 'analyses'])
            ->notCancelled()
            ->latest()
            ->limit(10)
            ->get();

        return view('registrar.dashboard', compact('stats', 'recentReferrals'));
    }

    /**
     * Show all referrals.
     */
    public function referrals(Request $request)
    {
        $query = Referral::with(['patient', 'doctor', 'analyses'])->notCancelled();

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search by patient name or FIN code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $referrals = $query->latest()->paginate(15);

        return view('registrar.referrals.index', compact('referrals'));
    }

    /**
     * Show referral details.
     */
    public function showReferral($id)
    {
        $referral = Referral::with(['patient', 'doctor', 'analyses'])->notCancelled()->findOrFail($id);

        return view('registrar.referrals.show', compact('referral'));
    }

    /**
     * Update referral status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $referral = Referral::with('doctor')->findOrFail($id);
        $oldStatus = $referral->status;
        $referral->update(['status' => $validated['status']]);

        // Create notification for doctor when analysis is completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            \App\Models\Notification::create([
                'user_id' => $referral->doctor_id,
                'type' => 'analysis_completed',
                'title' => 'Analiz Tamamlandı',
                'message' => 'Göndəriş #' . $referral->id . ' üçün analiz tamamlandı',
                'data' => [
                    'referral_id' => $referral->id,
                ],
            ]);
        }

        return back()->with('success', 'Status uğurla yeniləndi');
    }

    /**
     * Approve referral with discount.
     */
    public function approveReferral(Request $request, $id)
    {
        $validated = $request->validate([
            'discount_type' => 'required|in:none,percentage,amount',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:500',
            'cancelled_analyses' => 'nullable|array',
            'cancelled_analyses.*' => 'exists:analyses,id',
            'cancellation_reasons' => 'nullable|array',
        ]);

        $referral = Referral::with(['doctor', 'analyses'])->notCancelled()->findOrFail($id);

        // Update cancellation status for analyses
        $cancelledAnalyses = $validated['cancelled_analyses'] ?? [];
        $cancellationReasons = $validated['cancellation_reasons'] ?? [];

        foreach ($referral->analyses as $analysis) {
            $isCancelled = in_array($analysis->id, $cancelledAnalyses);
            $reason = $cancellationReasons[$analysis->id] ?? null;

            $referral->analyses()->updateExistingPivot($analysis->id, [
                'is_cancelled' => $isCancelled,
                'cancellation_reason' => $isCancelled ? $reason : null,
            ]);
        }

        // Reload analyses after update
        $referral->load('analyses');

        // Calculate original price from snapshots (only non-cancelled)
        // Note: This is tax-exclusive price; registrar sees tax-inclusive via accessor
        $originalPrice = $referral->analyses->filter(function($analysis) {
            return !$analysis->pivot->is_cancelled;
        })->sum('pivot.analysis_price');

        $discountValue = $validated['discount_value'] ?? 0;
        $discountType = $validated['discount_type'];

        // Calculate final price after discount (tax-exclusive, stored in DB)
        // Tax will be applied when displayed to registrar via getFinalPriceWithTaxAttribute()
        if ($discountType === 'percentage') {
            $discountAmount = ($originalPrice * $discountValue) / 100;
            $finalPrice = $originalPrice - $discountAmount;
        } elseif ($discountType === 'amount') {
            $finalPrice = max(0, $originalPrice - $discountValue);
        } else {
            $finalPrice = $originalPrice;
        }

        // Calculate doctor commission (always on tax-exclusive price)
        $doctorCommission = 0;
        $isPriced = true; // By default, it's priced

        // If discount is applied (not 'none'), admin must set commission
        if ($discountType !== 'none') {
            $isPriced = false; // Admin will set commission manually
            $doctorCommission = 0;
        } else {
            // No discount - calculate commission automatically
            foreach ($referral->analyses as $analysis) {
                if ($analysis->pivot->is_cancelled) {
                    continue; // Skip cancelled analyses
                }

                $pivot = $analysis->pivot;
                $snapshotPrice = $pivot->analysis_price; // Tax-exclusive
                $normalCommission = $pivot->commission_percentage;

                $doctorCommission += ($snapshotPrice * $normalCommission) / 100;
            }
        }

        $referral->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'final_price' => $finalPrice,
            'doctor_commission' => $doctorCommission,
            'is_priced' => $isPriced,
            'discount_reason' => $validated['discount_reason'],
        ]);

        // Create notification for doctor
        \App\Models\Notification::create([
            'user_id' => $referral->doctor_id,
            'type' => 'referral_approved',
            'title' => 'Göndəriş Təsdiqləndi',
            'message' => 'Göndəriş #' . $referral->id . ' təsdiqləndi',
            'data' => [
                'referral_id' => $referral->id,
            ],
        ]);

        return back()->with('success', 'Göndəriş təsdiqləndi');
    }

    /**
     * Reject referral approval.
     */
    public function rejectReferral($id)
    {
        $referral = Referral::findOrFail($id);

        $referral->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,            'discount_type' => 'none',
            'discount_value' => 0,
            'final_price' => $referral->total_price,
            'doctor_commission' => 0,
            'is_priced' => false,
            'discount_reason' => null,        ]);

        return back()->with('success', 'Təsdiq ləğv edildi');
    }

    /**
     * Show edit referral form for registrar.
     */
    public function editReferral($id)
    {
        $referral = Referral::with(['patient', 'doctor', 'analyses'])
            ->notCancelled()
            ->findOrFail($id);

        // Yoxla redaktə edilə bilərmi
        if (!$referral->canBeEditedByRegistrar()) {
            return redirect()->route('registrar.referrals.show', $id)
                ->with('error', 'Bu göndəriş redaktə edilə bilməz. Artıq təsdiqlənib.');
        }

        // Get analyses by category
        $analyses = $this->getCachedAnalysesByCategory();

        // Currently selected analyses
        $selectedAnalyses = $referral->analyses->pluck('id')->toArray();

        return view('registrar.referrals.edit', compact('referral', 'analyses', 'selectedAnalyses'));
    }

    /**
     * Update referral for registrar.
     */
    public function updateReferral(Request $request, $id)
    {
        $referral = Referral::findOrFail($id);

        // Yoxla redaktə edilə bilərmi
        if (!$referral->canBeEditedByRegistrar()) {
            return redirect()->route('registrar.referrals.show', $id)
                ->with('error', 'Bu göndəriş redaktə edilə bilməz. Artıq təsdiqlənib.');
        }

        $validated = $request->validate([
            'analyses' => 'required|array|min:1',
            'analyses.*' => 'exists:analyses,id',
            'notes' => 'nullable|string',
        ], [
            'analyses.required' => 'Ən azı bir analiz seçilməlidir',
        ]);

        DB::beginTransaction();
        try {
            // Update notes
            $referral->update([
                'notes' => $validated['notes'] ?? null,
            ]);

            // Detach all old analyses
            $referral->analyses()->detach();

            // Attach new analyses with snapshots
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

            // Update total program commission
            $referral->update(['total_program_commission' => $totalProgramCommission]);

            DB::commit();

            return redirect()->route('registrar.referrals.show', $id)
                ->with('success', 'Göndəriş uğurla yeniləndi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Show all patients.
     */
    public function patients()
    {
        $patients = \App\Models\Patient::with('registeredBy')
            ->latest()
            ->paginate(20);

        return view('registrar.patients.index', compact('patients'));
    }
}
