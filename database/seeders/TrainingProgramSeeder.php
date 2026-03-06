<?php

namespace Database\Seeders;

use App\Models\TrainingProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingProgramSeeder extends Seeder
{
    public function run(): void
    {

        TrainingProgram::insert([

            [
                'name' => 'Hana Hegazy',
                'age' => 22,
                'gender' => 'female',
                'phone' => '010000000000',
                'university' => 'NUB',
                'department_id' => 1,
                'cv' => 'cvs/cv1.pdf',
                'gpa' => 3.5,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Ahmed Samir',
                'age' => 21,
                'gender' => 'male',
                'phone' => '010000000001',
                'university' => 'NUB',
                'department_id' => 2,
                'cv' => 'cvs/cv2.pdf',
                'gpa' => 3.2,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Rana Wahed',
                'age' => 20,
                'gender' => 'female',
                'phone' => '010000000002',
                'university' => 'BUC',
                'department_id' => 3,
                'cv' => 'cvs/cv3.pdf',
                'gpa' => 3.0,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Mazen Helmy',
                'age' => 23,
                'gender' => 'male',
                'phone' => '010000000003',
                'university' => 'Cairo University',
                'department_id' => 4,
                'cv' => 'cvs/cv4.pdf',
                'gpa' => 2.9,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Dena Ashraf',
                'age' => 21,
                'gender' => 'female',
                'phone' => '010000000004',
                'university' => 'NUB',
                'department_id' => 1,
                'cv' => 'cvs/cv5.pdf',
                'gpa' => 3.9,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Hassan Zaki',
                'age' => 19,
                'gender' => 'male',
                'phone' => '010000000005',
                'university' => 'Mansoura University',
                'department_id' => 5,
                'cv' => 'cvs/cv6.pdf',
                'gpa' => 3.1,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]

        ]);

    }
}
