vergi daxil qiymet yuvarlak olsun 30.50 kimi maks vergulden sagda iki reqem gorunsun birde 30.498 dise 30.5 olsun numune olaraq verdim bunu sen vergi daxil qiymet sutununu dediyim kimi yuvarlak ele<?php

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
            $table->decimal('price_with_tax', 10, 2)->storedAs('ROUND(price * 1.3, 1)')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('price_with_tax');
        });
    }
};
