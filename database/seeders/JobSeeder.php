<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('jobs_training')) {
            return;
        }

        $jobs = [
            ['title' => 'Cardiologist', 'description' => 'Provide outpatient and inpatient cardiovascular care.', 'requirements' => 'Medical degree, cardiology board certification, and 3+ years experience.', 'department' => 'Cardiology & Catheterization', 'type' => 'medical'],
            ['title' => 'Emergency Medicine Physician', 'description' => 'Deliver urgent care in the emergency department.', 'requirements' => 'Medical degree, emergency medicine certification, ACLS and PALS.', 'department' => 'Emergency Physicians', 'type' => 'medical'],
            ['title' => 'Radiology Technologist', 'description' => 'Support diagnostic imaging and patient preparation.', 'requirements' => 'Radiology qualification and 2+ years experience.', 'department' => 'Radiology', 'type' => 'medical'],
            ['title' => 'HR Specialist', 'description' => 'Manage recruitment and employee relations.', 'requirements' => 'Bachelor degree in HR and 2+ years experience.', 'department' => 'Administration', 'type' => 'administrative'],
        ];

        foreach ($jobs as $job) {
            Job::updateOrCreate(
                ['title' => $job['title']],
                $job + ['location' => 'NUH', 'salary' => 'Competitive', 'status' => 'active']
            );
        }
    }
}
