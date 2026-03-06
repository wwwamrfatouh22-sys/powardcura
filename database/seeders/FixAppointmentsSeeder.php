<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Department;

class FixAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $departments = Department::all();

        if ($patients->isEmpty() || $departments->isEmpty()) {
            return;
        }

        $appointments = Appointment::all();

        foreach ($appointments as $appointment) {

            $appointment->update([

                // يملى patient_id لو فاضي بس
                'patient_id' => $appointment->patient_id
                    ?? $patients->random()->id,

                // يملى department_id لو فاضي بس
                'department_id' => $appointment->department_id
                    ?? $departments->random()->id,

                // يملى status لو فاضي بس
                'status' => $appointment->status
                    ?? collect([
                        'Confirmed',
                        'Pending',
                        'Completed'
                    ])->random(),
            ]);
        }
    }
}
