<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Legacy entry point retained for old deployment scripts.
 *
 * AppointmentSeeder now creates complete, valid rows deterministically, so
 * this seeder must not randomly rewrite existing production appointments.
 */
class FixAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AppointmentSeeder::class);
    }
}
