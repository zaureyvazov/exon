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
        Schema::table('analyses', function (Blueprint $table) {
            // Endirimli xəstələr üçün həkim komissiya faizi
            $table->decimal('discount_commission_rate', 5, 2)
                ->default(0)
                ->after('commission_percentage')
                ->comment('Endirimli xəstələr üçün komissiya faizi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('discount_commission_rate');
        });
    }
};
