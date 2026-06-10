<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            'departments' => DepartmentSeeder::class,
            'rooms' => RoomSeeder::class,
            'doctors' => DoctorSeeder::class,
            'staff' => StaffSeeder::class,
            'users' => PatientSeeder::class,
            'appointments' => AppointmentSeeder::class,
            'lab_tests' => MedicalResultsSeeder::class,
            'reports' => ReportSeeder::class,
            'jobs_training' => JobSeeder::class,
            'training_programs' => TrainingProgramSeeder::class,
            'medical_positions' => MedicalPositionSeeder::class,
            'staff_complaints' => ComplaintSeeder::class,
            'admins' => AdminSeeder::class,
        ];

        foreach ($seeders as $table => $seeder) {
            if (Schema::hasTable($table)) {
                $this->call($seeder);
            }
        }
    }
}
