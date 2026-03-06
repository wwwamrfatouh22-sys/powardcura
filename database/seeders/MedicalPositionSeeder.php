<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalPosition;

class MedicalPositionSeeder extends Seeder
{
    public function run()
    {
        MedicalPosition::insert([
            [
                'name' => 'Sarah Johnson',
                'age' => 34,
                'gender' => 'female',
                'phone' => '010000000000',
                'department_id' => 1,
                'cv' => 'cv1.pdf',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Williams',
                'age' => 45,
                'gender' => 'male',
                'phone' => '010000000001',
                'department_id' => 2,
                'cv' => 'cv2.pdf',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emma Davis',
                'age' => 28,
                'gender' => 'female',
                'phone' => '010000000002',
                'department_id' => 3,
                'cv' => 'cv3.pdf',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Brown',
                'age' => 52,
                'gender' => 'male',
                'phone' => '010000000003',
                'department_id' => 4,
                'cv' => 'cv4.pdf',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jennifer Taylor',
                'age' => 41,
                'gender' => 'female',
                'phone' => '010000000004',
                'department_id' => 1,
                'cv' => 'cv5.pdf',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
