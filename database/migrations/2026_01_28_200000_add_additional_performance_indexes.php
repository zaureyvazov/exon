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
        // Add composite indexes for common query patterns

        // Referrals table - for date range queries with filters
        if (!$this->indexExists('referrals', 'referrals_doctor_approved_priced_idx')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index(['doctor_id', 'is_approved', 'is_priced'], 'referrals_doctor_approved_priced_idx');
            });
        }

        if (!$this->indexExists('referrals', 'referrals_created_approved_idx')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index(['created_at', 'is_approved', 'is_priced'], 'referrals_created_approved_idx');
            });
        }

        if (!$this->indexExists('referrals', 'referrals_discount_type_idx')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index('discount_type');
            });
        }

        // Referral analyses table - for aggregation queries
        if (!$this->indexExists('referral_analyses', 'referral_analyses_cancelled_idx')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->index('is_cancelled');
            });
        }

        if (!$this->indexExists('referral_analyses', 'referral_analyses_analysis_cancelled_idx')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->index(['analysis_id', 'is_cancelled'], 'referral_analyses_analysis_cancelled_idx');
            });
        }

        // Patients table - for search queries
        if (!$this->indexExists('patients', 'patients_registered_by_idx')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('registered_by');
            });
        }

        if (!$this->indexExists('patients', 'patients_created_at_idx')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('created_at');
            });
        }

        // Users table - for role filtering
        if (!$this->indexExists('users', 'users_role_id_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role_id');
            });
        }

        // Messages table - for conversation queries
        if (!Schema::hasTable('messages')) {
            return; // Skip if messages table doesn't exist
        }

        if (!$this->indexExists('messages', 'messages_sender_receiver_idx')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['sender_id', 'receiver_id'], 'messages_sender_receiver_idx');
            });
        }

        if (!$this->indexExists('messages', 'messages_receiver_read_idx')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['receiver_id', 'is_read'], 'messages_receiver_read_idx');
            });
        }

        if (!$this->indexExists('messages', 'messages_created_at_idx')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex('referrals_doctor_approved_priced_idx');
            $table->dropIndex('referrals_created_approved_idx');
            $table->dropIndex(['discount_type']);
        });

        Schema::table('referral_analyses', function (Blueprint $table) {
            $table->dropIndex(['is_cancelled']);
            $table->dropIndex('referral_analyses_analysis_cancelled_idx');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['registered_by']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
        });

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex('messages_sender_receiver_idx');
                $table->dropIndex('messages_receiver_read_idx');
                $table->dropIndex(['created_at']);
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    protected function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        $indexExists = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$dbName, $table, $index]
        );

        return $indexExists[0]->count > 0;
    }
};
