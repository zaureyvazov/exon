<?php

namespace App\Http\Controllers\Traits;

use App\Models\Analysis;
use Illuminate\Support\Facades\Cache;

trait CachesAnalyses
{
    /**
     * Get active analyses with caching (1000 analiz üçün optimal)
     * Cache: 1 saat
     */
    protected function getCachedActiveAnalyses()
    {
        return Cache::remember('active_analyses_list', 3600, function () {
            return Analysis::active()
                ->select('id', 'name', 'price', 'commission_percentage', 'category_id')
                ->with('category:id,name')
                ->orderBy('category_id')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear analyses cache when needed
     */
    protected function clearAnalysesCache()
    {
        Cache::forget('active_analyses_list');
    }

    /**
     * Get analyses grouped by category (for better UI with 1000 items)
     */
    protected function getCachedAnalysesByCategory()
    {
        return Cache::remember('analyses_by_category', 3600, function () {
            return Analysis::active()
                ->select('id', 'name', 'price', 'commission_percentage', 'category_id')
                ->with('category:id,name')
                ->orderBy('category_id')
                ->orderBy('name')
                ->get()
                ->groupBy('category.name');
        });
    }
}
