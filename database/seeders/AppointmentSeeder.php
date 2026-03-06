<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // هات قسم General من الداتا
        $general = Department::where('name_en', 'Cardiology')->first();

        if (!$general) {
            $general = Department::create([
                'name_en' => 'Cardiology',
                'name_ar' => 'قسم القلب',
                'image'   => 'cardiology.jpg',
            ]);
        }

        // Doctors
        $smith = Doctor::create([
            'name' => 'Dr. Smith',
            'department_id' => $general->id
        ]);

        $chen = Doctor::create([
            'name' => 'Dr. Chen',
            'department_id' => $general->id
        ]);

        $martinez = Doctor::create([
            'name' => 'Dr. Martinez',
            'department_id' => $general->id
        ]);

        // Patients
        $p1 = Patient::create([
            'name' => 'Sarah Johnson',
            'patient_code' => 'P-2024-001'
        ]);

        $p2 = Patient::create([
            'name' => 'Michael Brown',
            'patient_code' => 'P-2024-002'
        ]);

        $p3 = Patient::create([
            'name' => 'Emma Davis',
            'patient_code' => 'P-2024-003'
        ]);

        $p4 = Patient::create([
            'name' => 'James Wilson',
            'patient_code' => 'P-2024-004'
        ]);

        $p5 = Patient::create([
            'name' => 'Olivia Taylor',
            'patient_code' => 'P-2024-005'
        ]);

        // Appointments (Today's Date)
        Appointment::create([
            'patient_id' => $p1->id,
            'doctor_id' => $smith->id,
            'reason' => 'Regular Checkup',
            'time' => '09:00:00',
            'date' => now()->toDateString(),
            'status' => 'Scheduled',
        ]);

        Appointment::create([
            'patient_id' => $p2->id,
            'doctor_id' => $chen->id,
            'reason' => 'Blood Pressure',
            'time' => '09:30:00',
            'date' => now()->toDateString(),
            'status' => 'In Progress',
        ]);

        Appointment::create([
            'patient_id' => $p3->id,
            'doctor_id' => $smith->id,
            'reason' => 'Follow-up Visit',
            'time' => '10:00:00',
            'date' => now()->toDateString(),
            'status' => 'Scheduled',
        ]);

        Appointment::create([
            'patient_id' => $p4->id,
            'doctor_id' => $martinez->id,
            'reason' => 'Wound Care',
            'time' => '10:30:00',
            'date' => now()->toDateString(),
            'status' => 'Scheduled',
        ]);

        Appointment::create([
            'patient_id' => $p5->id,
            'doctor_id' => $chen->id,
            'reason' => 'Diabetes Checkup',
            'time' => '11:00:00',
            'date' => now()->toDateString(),
            'status' => 'Completed',
        ]);
    }

}
