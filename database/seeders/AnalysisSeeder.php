<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Analysis;

class AnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $analyses = [
            ['name' => 'Ümumi qan analizi', 'description' => 'Qanda hemoglobin, eritrositlər, leykositlər və s. göstəricilərin müəyyən edilməsi', 'price' => 15.00, 'commission_percentage' => 10.00, 'is_active' => true],
            ['name' => 'Biokimyəvi qan analizi', 'description' => 'Qlükoza, xolesterol, fermentlər və s. göstəricilərin müəyyən edilməsi', 'price' => 25.00, 'commission_percentage' => 12.00, 'is_active' => true],
            ['name' => 'Sidik analizi', 'description' => 'Sidiyin ümumi analizi', 'price' => 10.00, 'commission_percentage' => 8.00, 'is_active' => true],
            ['name' => 'EKQ', 'description' => 'Elektrokardioqrafiya', 'price' => 20.00, 'commission_percentage' => 15.00, 'is_active' => true],
            ['name' => 'Rentgen', 'description' => 'Rentgen müayinəsi', 'price' => 30.00, 'commission_percentage' => 18.00, 'is_active' => true],
            ['name' => 'USM', 'description' => 'Ultrasəs müayinəsi', 'price' => 35.00, 'commission_percentage' => 20.00, 'is_active' => true],
            ['name' => 'MRT', 'description' => 'Maqnit-rezonans tomoqrafiyası', 'price' => 150.00, 'commission_percentage' => 25.00, 'is_active' => true],
            ['name' => 'KT', 'description' => 'Kompüter tomoqrafiyası', 'price' => 120.00, 'commission_percentage' => 22.00, 'is_active' => true],
            ['name' => 'HIV testi', 'description' => 'HIV infeksiyasının müəyyən edilməsi', 'price' => 40.00, 'commission_percentage' => 12.00, 'is_active' => true],
            ['name' => 'Hepatit testləri', 'description' => 'Hepatit B və C viruslarının müəyyən edilməsi', 'price' => 50.00, 'commission_percentage' => 15.00, 'is_active' => true],
        ];

        foreach ($analyses as $analysis) {
            Analysis::create($analysis);
        }
    }
}
