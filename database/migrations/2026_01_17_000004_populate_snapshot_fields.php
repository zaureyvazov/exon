<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate snapshot fields for existing referral_analyses records
        DB::statement("
            UPDATE referral_analyses ra
            INNER JOIN analyses a ON ra.analysis_id = a.id
            SET
                ra.analysis_price = a.price,
                ra.commission_percentage = a.commission_percentage,
                ra.discount_commission_rate = COALESCE(a.discount_commission_rate, 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set snapshot fields back to null
        DB::statement("
            UPDATE referral_analyses
            SET
                analysis_price = NULL,
                commission_percentage = NULL,
                discount_commission_rate = NULL
        ");
    }
};
