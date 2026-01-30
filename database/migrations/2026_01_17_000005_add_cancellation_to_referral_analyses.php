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
            $table->boolean('is_cancelled')->default(false)->after('discount_commission_rate');
            $table->text('cancellation_reason')->nullable()->after('is_cancelled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->dropColumn(['is_cancelled', 'cancellation_reason']);
        });
    }
};
