<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;

class FixAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors  = Doctor::all();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $appointments = Appointment::all();

        foreach ($appointments as $appointment) {

            $appointment->update([

                'patient_id' => $appointment->patient_id
                    ?: $patients->random()->id,

                'doctor_id' => $appointment->doctor_id
                    ?: $doctors->random()->id,

                'date' => $appointment->date
                    ?: Carbon::now()->addDays(rand(1,30))->toDateString(),

                'time' => $appointment->time
                    ?: collect([
                        '09:00',
                        '10:00',
                        '11:00',
                        '12:00',
                        '13:00'
                    ])->random(),

                'status' => $appointment->status
                    ?: collect([
                        'Pending',
                        'Confirmed',
                        'Completed'
                    ])->random(),
            ]);
        }
    }
}
