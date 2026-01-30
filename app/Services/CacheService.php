<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Analysis;
use App\Models\Referral;

class CacheService
{
    /**
     * Cache duration in seconds (5 minutes)
     */
    const CACHE_DURATION = 300;

    /**
     * Get cached active analyses
     */
    public static function getActiveAnalyses()
    {
        return Cache::remember('active_analyses', self::CACHE_DURATION, function () {
            return Analysis::active()
                ->select('id', 'name', 'price', 'commission_percentage')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached doctor stats
     */
    public static function getDoctorStats($doctorId)
    {
        return Cache::remember("doctor_stats_{$doctorId}", self::CACHE_DURATION, function () use ($doctorId) {
            $referralStats = Referral::where('doctor_id', $doctorId)
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
                ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
                ->first();

            return [
                'total_referrals' => $referralStats->total ?? 0,
                'pending_referrals' => $referralStats->pending ?? 0,
                'completed_referrals' => $referralStats->completed ?? 0,
            ];
        });
    }

    /**
     * Clear doctor cache
     */
    public static function clearDoctorCache($doctorId)
    {
        Cache::forget("doctor_stats_{$doctorId}");
    }

    /**
     * Clear analyses cache
     */
    public static function clearAnalysesCache()
    {
        Cache::forget('active_analyses');
    }

    /**
     * Get cached admin dashboard stats
     */
    public static function getAdminDashboardStats()
    {
        return Cache::remember('admin_dashboard_stats', self::CACHE_DURATION, function () {
            return [
                'total_users' => User::count(),
                'total_doctors' => User::role('doctor')->count(),
                'total_registrars' => User::role('registrar')->count(),
                'total_referrals' => Referral::count(),
                'total_analyses' => Analysis::count(),
            ];
        });
    }

    /**
     * Clear admin cache
     */
    public static function clearAdminCache()
    {
        Cache::forget('admin_dashboard_stats');
    }
}
