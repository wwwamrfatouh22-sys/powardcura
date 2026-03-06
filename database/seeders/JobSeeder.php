<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Job::create([
            'title' => 'Cardiologist',
            'description' => 'Join our cardiovascular team to provide exceptional cardiac care.',
            'requirements' => 'MD degree, Board certification in Cardiology, 3+ years experience',
            'location' => 'NUH',
            'salary' => '-',
            'type' => 'medical',
        ]);

        Job::create([
            'title' => 'Emergency Medicine Physician',
            'description' => 'Provide critical care in our Level 1 trauma center.',
            'requirements' => 'MD/DO degree, Board certified in Emergency Medicine, ACLS & PALS',
            'location' => 'NUH',
            'salary' => '-',
            'type' => 'medical',
        ]);

        Job::create([
            'title' => 'HR Specialist',
            'description' => 'Manage recruitment and employee relations.',
            'requirements' => 'Bachelor degree in HR, 2+ years experience',
            'location' => 'NUH',
            'salary' => '-',
            'type' => 'admin',
        ]);
    }
}
