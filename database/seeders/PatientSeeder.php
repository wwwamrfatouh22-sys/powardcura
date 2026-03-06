<?php

namespace Database\Seeders;

use App\Models\Medication;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {

        $patient = Patient::updateOrCreate(
            ['national_id' => '44444444444444'],
            [
                'full_name'      => 'Tamer Ahmed Ibrahim',
                'file_number'    => '12345',
                'blood_type'     => 'A+',
                'gender'         => 'Male',
                'age'            => 45,
                'dob'            => '2000-01-01',
                'phone'          => '01000000000',
                'address'        => 'Cairo, Egypt',
                'password'       => bcrypt('123456'),
                'blood_pressure' => '120/80',
                'pulse_rate'     => 72,
                'temperature'    => 37,
                'weight'         => 82,
            ]
        );

        Medication::create([
            'patient_id'  => $patient->id,
            'name'        => 'Metformin',
            'dose'        => '500 mg',
            'instructions'=> 'Twice daily with food',
        ]);

        Medication::create([
            'patient_id'  => $patient->id,
            'name'        => 'Amlodipine',
            'dose'        => '5 mg',
            'instructions'=> 'Once daily in the morning',
        ]);

        Medication::create([
            'patient_id'  => $patient->id,
            'name'        => 'Atorvastatin',
            'dose'        => '20 mg',
            'instructions'=> 'Once daily in the evening',
        ]);
    }
}
