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
        // Sessions table indexes for active user tracking (skip if exists)
        if (!$this->indexExists('sessions', 'sessions_user_id_index')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index('user_id');
            });
        }

        if (!$this->indexExists('sessions', 'sessions_last_activity_index')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index('last_activity');
            });
        }

        // Analyses table indexes for fast filtering
        if (!$this->indexExists('analyses', 'analyses_category_id_index')) {
            Schema::table('analyses', function (Blueprint $table) {
                $table->index('category_id');
            });
        }

        if (!$this->indexExists('analyses', 'analyses_is_active_index')) {
            Schema::table('analyses', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        // Referrals table indexes for doctor queries
        if (!$this->indexExists('referrals', 'referrals_doctor_id_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index('doctor_id');
            });
        }

        if (!$this->indexExists('referrals', 'referrals_is_approved_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index('is_approved');
            });
        }

        if (!$this->indexExists('referrals', 'referrals_is_priced_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index('is_priced');
            });
        }

        if (!$this->indexExists('referrals', 'referrals_created_at_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->index('created_at');
            });
        }

        // Referral_analyses pivot table indexes
        if (!$this->indexExists('referral_analyses', 'referral_analyses_referral_id_index')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->index('referral_id');
            });
        }

        if (!$this->indexExists('referral_analyses', 'referral_analyses_analysis_id_index')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->index('analysis_id');
            });
        }

        // Patients table indexes
        if (!$this->indexExists('patients', 'patients_registered_by_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('registered_by');
            });
        }

        if (!$this->indexExists('patients', 'patients_fin_code_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('fin_code');
            });
        }

        if (!$this->indexExists('patients', 'patients_created_at_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('created_at');
            });
        }

        // Analysis categories table indexes
        if (!$this->indexExists('analysis_categories', 'analysis_categories_is_active_index')) {
            Schema::table('analysis_categories', function (Blueprint $table) {
                $table->index('is_active');
            });
        }
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $indexName]
        );

        return $result[0]->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sessions
        if ($this->indexExists('sessions', 'sessions_user_id_index')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
        }

        if ($this->indexExists('sessions', 'sessions_last_activity_index')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex(['last_activity']);
            });
        }

        // Analyses
        if ($this->indexExists('analyses', 'analyses_category_id_index')) {
            Schema::table('analyses', function (Blueprint $table) {
                $table->dropIndex(['category_id']);
            });
        }

        if ($this->indexExists('analyses', 'analyses_is_active_index')) {
            Schema::table('analyses', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }

        // Referrals
        if ($this->indexExists('referrals', 'referrals_doctor_id_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->dropIndex(['doctor_id']);
            });
        }

        if ($this->indexExists('referrals', 'referrals_is_approved_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->dropIndex(['is_approved']);
            });
        }

        if ($this->indexExists('referrals', 'referrals_is_priced_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->dropIndex(['is_priced']);
            });
        }

        if ($this->indexExists('referrals', 'referrals_created_at_index')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        }

        // Referral_analyses
        if ($this->indexExists('referral_analyses', 'referral_analyses_referral_id_index')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->dropIndex(['referral_id']);
            });
        }

        if ($this->indexExists('referral_analyses', 'referral_analyses_analysis_id_index')) {
            Schema::table('referral_analyses', function (Blueprint $table) {
                $table->dropIndex(['analysis_id']);
            });
        }

        // Patients
        if ($this->indexExists('patients', 'patients_registered_by_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropIndex(['registered_by']);
            });
        }

        if ($this->indexExists('patients', 'patients_fin_code_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropIndex(['fin_code']);
            });
        }

        if ($this->indexExists('patients', 'patients_created_at_index')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        }

        // Analysis categories
        if ($this->indexExists('analysis_categories', 'analysis_categories_is_active_index')) {
            Schema::table('analysis_categories', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }
    }
};
