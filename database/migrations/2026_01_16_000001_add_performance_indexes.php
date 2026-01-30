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
            // Index for status filtering
            $table->index('status');

            // Index for doctor queries
            $table->index(['doctor_id', 'status']);
            $table->index(['doctor_id', 'created_at']);

            // Index for approval queries
            $table->index('is_approved');
            $table->index(['is_approved', 'created_at']);

            // Index for date-based queries
            $table->index('created_at');
        });

        Schema::table('patients', function (Blueprint $table) {
            // Index for doctor queries
            $table->index('registered_by');

            // Index for search queries (name, surname)
            $table->index(['name', 'surname']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Index for user notifications
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            // Index for conversation queries
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'is_read']);
            $table->index('created_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Index for doctor payments
            $table->index(['doctor_id', 'created_at']);
        });

        // Index for pivot table
        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->index('analysis_id');
            $table->index('referral_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['doctor_id', 'status']);
            $table->dropIndex(['doctor_id', 'created_at']);
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['is_approved', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['registered_by']);
            $table->dropIndex(['name', 'surname']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id', 'receiver_id']);
            $table->dropIndex(['receiver_id', 'is_read']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'created_at']);
        });

        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->dropIndex(['analysis_id']);
            $table->dropIndex(['referral_id']);
        });
    }
};
