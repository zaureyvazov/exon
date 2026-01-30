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
        Schema::table('patients', function (Blueprint $table) {
            // FIN kodunu sil
            $table->dropColumn('fin_code');
            
            // Seriya nömrəsi və ata adı əlavə et
            $table->string('serial_number', 20)->nullable()->after('surname');
            $table->string('father_name', 100)->nullable()->after('name');
            
            // Index
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Geri qaytarma
            $table->dropIndex(['serial_number']);
            $table->dropColumn(['serial_number', 'father_name']);
            $table->string('fin_code', 7)->unique();
        });
    }
};
