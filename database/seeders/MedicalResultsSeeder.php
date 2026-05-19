<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\Patient;
use App\Models\RadiologyResult;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MedicalResultsSeeder extends Seeder
{
    public function run(): void
    {
        $patient = Patient::updateOrCreate(
            ['national_id' => '44444444444444'],
            [
                'full_name' => 'Test Patient',
                'dob' => '2000-01-01',
                'phone' => '01000000000',
                'password' => Hash::make(env('SEED_PATIENT_PASSWORD', 'Patient@12345')),
            ]
        );

        LabTest::updateOrCreate(
            ['patient_id' => $patient->id, 'title' => 'Complete Blood Count'],
            [
                'description' => 'Routine blood test',
                'file_name' => 'lab1.pdf',
                'test_date' => now()->subDays(3)->toDateString(),
            ]
        );

        LabTest::updateOrCreate(
            ['patient_id' => $patient->id, 'title' => 'Liver Function Test'],
            [
                'description' => 'LFT report',
                'file_name' => 'lab2.pdf',
                'test_date' => now()->subDays(2)->toDateString(),
            ]
        );

        RadiologyResult::updateOrCreate(
            ['patient_id' => $patient->id, 'title' => 'Chest X-Ray'],
            [
                'description' => 'Chest radiology report',
                'file_name' => 'radio1.pdf',
                'scan_date' => now()->subDays(4)->toDateString(),
            ]
        );

        RadiologyResult::updateOrCreate(
            ['patient_id' => $patient->id, 'title' => 'MRI Brain'],
            [
                'description' => 'Brain MRI report',
                'file_name' => 'radio2.pdf',
                'scan_date' => now()->subDay()->toDateString(),
            ]
        );
    }
}
