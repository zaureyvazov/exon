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
            $table->boolean('is_cancelled')->default(false)->after('is_priced');
            $table->timestamp('cancelled_at')->nullable()->after('is_cancelled');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['is_cancelled', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
    }
};
