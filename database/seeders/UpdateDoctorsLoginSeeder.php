<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Backward-compatible entry point for the complete doctor demo seeder.
 */
class UpdateDoctorsLoginSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DoctorSeeder::class);
    }
}
