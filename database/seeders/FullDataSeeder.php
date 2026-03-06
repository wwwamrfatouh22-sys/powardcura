<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FullDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appointment::create([
            'doctor_id'  => 1,
            'first_name' => 'Francesca',
            'last_name'  => 'Lee',
            'email'      => 'francesca@mail.com',
            'phone'      => '01111111111',
            'reason'     => 'General Checkup',
            'time'       => '10:00',
            'date'       => now()->toDateString(),
        ]);

        Appointment::create([
            'doctor_id'  => 1,
            'first_name' => 'Barrett',
            'last_name'  => 'Hansen',
            'email'      => 'barrett@mail.com',
            'phone'      => '01022222222',
            'reason'     => 'Blood Pressure',
            'time'       => '11:00',
            'date'       => now()->toDateString(),
        ]);

        Appointment::create([
            'doctor_id'  => 1,
            'first_name' => 'Fatima',
            'last_name'  => 'Garcia',
            'email'      => 'fatima@mail.com',
            'phone'      => '01033333333',
            'reason'     => 'Follow-up Visit',
            'time'       => '12:00',
            'date'       => now()->toDateString(),
        ]);
    }

}
