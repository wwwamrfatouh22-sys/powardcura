<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Doctor::insert([

            [
                'name' => 'Dr. Sarah Johnson',
                'specialization' => 'Cardiologist',
                'image' => 'doctor1.jpg',
                'rating' => 4.8,
                'experience' => 12,
                'department_id' => 1
            ],
            [
                'name' => 'Dr. Michael Chen',
                'specialization' => 'Cardiologist',
                'image' => 'doctor2.jpg',
                'rating' => 4.9,
                'experience' => 8,
                'department_id' => 1
            ],

            // Neurology (department_id = 2)
            [
                'name' => 'Dr. Aisha Patel',
                'specialization' => 'Neurologist',
                'image' => 'doctor3.jpg',
                'rating' => 4.9,
                'experience' => 9,
                'department_id' => 2
            ],

            // Orthopedics (department_id = 3)
            [
                'name' => 'Dr. James Wilson',
                'specialization' => 'Orthopedic Surgeon',
                'image' => 'doctor4.jpg',
                'rating' => 4.6,
                'experience' => 15,
                'department_id' => 3
            ],
        ]);
    }
}
