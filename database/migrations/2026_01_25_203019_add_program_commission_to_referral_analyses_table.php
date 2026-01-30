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
            $table->decimal('program_commission', 10, 2)->default(0)->after('discount_commission_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->dropColumn('program_commission');
        });
    }
};
