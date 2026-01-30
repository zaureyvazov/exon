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
        Schema::table('referrals', function (Blueprint $table) {
            // Endirim növü: none, percentage, amount
            $table->enum('discount_type', ['none', 'percentage', 'amount'])->default('none');

            // Endirim dəyəri (faiz və ya məbləğ)
            $table->decimal('discount_value', 10, 2)->default(0);

            // Endirimdən sonra final qiymət
            $table->decimal('final_price', 10, 2)->default(0);

            // Həkim üçün komissiya məbləği
            $table->decimal('doctor_commission', 10, 2)->default(0);

            // Qiymət təyin edilib/yox
            $table->boolean('is_priced')->default(false);

            // Endirim səbəbi/qeyd (optional)
            $table->text('discount_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'final_price',
                'doctor_commission',
                'is_priced',
                'discount_reason'
            ]);
        });
    }
};
