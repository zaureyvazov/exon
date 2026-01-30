<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\Analysis;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Doktoru tap
        $doctor = User::whereHas('roles', function($q) {
            $q->where('name', 'doctor');
        })->first();

        $registrar = User::whereHas('roles', function($q) {
            $q->where('name', 'registrar');
        })->first();

        // Test xəstələri
        $patients = [
            [
                'name' => 'Əli',
                'surname' => 'Məmmədov',
                'phone' => '+994501234567',
                'fin_code' => '1AA2BB3',
                'registered_by' => $registrar->id,
            ],
            [
                'name' => 'Leyla',
                'surname' => 'Həsənova',
                'phone' => '+994502345678',
                'fin_code' => '2BB3CC4',
                'registered_by' => $registrar->id,
            ],
            [
                'name' => 'Rəşad',
                'surname' => 'İbrahimov',
                'phone' => '+994503456789',
                'fin_code' => '3CC4DD5',
                'registered_by' => $registrar->id,
            ],
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);

            // Hər xəstə üçün bir neçə göndəriş yarat
            $referralsCount = rand(1, 3);

            for ($i = 0; $i < $referralsCount; $i++) {
                $referral = Referral::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'notes' => 'Test qeydi - ' . $this->getRandomDiagnosis(),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                // Təsadüfi analizlər əlavə et (1-4 arası)
                $analysesCount = rand(1, 4);
                $randomAnalyses = Analysis::inRandomOrder()->limit($analysesCount)->get();

                $referral->analyses()->attach($randomAnalyses->pluck('id'));

                // Bəzi göndərişləri təsdiqlə
                if (rand(0, 1) == 1) {
                    $referral->update([
                        'is_approved' => true,
                        'approved_at' => now()->subDays(rand(0, 15)),
                        'approved_by' => $registrar->id,
                    ]);
                }
            }
        }
    }

    private function getRandomDiagnosis()
    {
        $diagnoses = [
            'Qrip',
            'Bronxit',
            'Hipertoniya',
            'Qastrit',
            'Diabetes mellitus',
            'Ürək-damar xəstəliyi',
            'Allergiya',
            'Migren',
        ];

        return $diagnoses[array_rand($diagnoses)];
    }
}
