<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            [
                'name_en' => 'Cardiology',
                'name_ar' => 'قسم القلب',
                'image'   => 'cardiology.jpg',
            ],
            [
                'name_en' => 'Neurology',
                'name_ar' => 'قسم المخ والأعصاب',
                'image'   => 'neurology.jpg',
            ],
            [
                'name_en' => 'Orthopedics',
                'name_ar' => 'قسم العظام',
                'image'   => 'orthopedics.jpg',
            ],
            [
                'name_en' => 'Pediatrics',
                'name_ar' => 'قسم الأطفال',
                'image'   => 'pediatrics.jpg',
            ],
            [
                'name_en' => 'Radiology',
                'name_ar' => 'قسم الأشعة',
                'image'   => 'radiology.jpg',
            ],
            [
                'name_en' => 'Dermatology',
                'name_ar' => 'قسم الجلدية',
                'image'   => 'dermatology.jpg',
            ],
        ]);
    }
}
