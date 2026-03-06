<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UpdateDoctorsLoginSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::updateOrInsert(
            ['name' => 'Dr. Sarah Johnson'],
            [
                'email' => 'sarah@nuh.com',
                'password' => Hash::make('12345678'),
                'updated_at' => now(),
            ]
        );
        Doctor::updateOrInsert(
            ['name' => 'Dr. Michael Chen'],
            [
                'email' => 'michael@nuh.com',
                'password' => Hash::make('12345678'),
                'updated_at' => now(),
            ]
        );
    }
}
