<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\Patient;
use App\Models\RadiologyResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // إنشاء مريض
        $patient = Patient::create([
            'national_id' => '44444444444444',
            'full_name'   => 'Test Patient',
            'dob'         => '2000-01-01',
            'phone'       => '01000000000',
            'password'    => bcrypt('12345678'),
        ]);

        // إضافة تحاليل
        LabTest::create([
            'title'       => 'Complete Blood Count',
            'description' => 'Routine blood test',
            'file_name'   => 'lab1.pdf',
            'patient_id'  => $patient->id,
        ]);

        LabTest::create([
            'title'       => 'Liver Function Test',
            'description' => 'LFT report',
            'file_name'   => 'lab2.pdf',
            'patient_id'  => $patient->id,
        ]);

        // إضافة أشعة
        RadiologyResult::create([
            'title'       => 'Chest X-Ray',
            'description' => 'Chest radiology report',
            'file_name'   => 'radio1.pdf',
            'patient_id'  => $patient->id,
        ]);

        RadiologyResult::create([
            'title'       => 'MRI Brain',
            'description' => 'Brain MRI report',
            'file_name'   => 'radio2.pdf',
            'patient_id'  => $patient->id,
        ]);
    }
}
