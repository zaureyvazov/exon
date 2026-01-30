<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('referral_analyses', function (Blueprint $table) {
            // Göndəriş yaradılan andaki analiz qiyməti (snapshot)
            $table->decimal('analysis_price', 10, 2)->default(0)->after('analysis_id');

            // Göndəriş yaradılan andaki normal komissiya faizi (snapshot)
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('analysis_price');

            // Göndəriş yaradılan andaki endirimli komissiya faizi (snapshot)
            $table->decimal('discount_commission_rate', 5, 2)->default(0)->after('commission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->dropColumn([
                'analysis_price',
                'commission_percentage',
                'discount_commission_rate'
            ]);
        });
    }
};
