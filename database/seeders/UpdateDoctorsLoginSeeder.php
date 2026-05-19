<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdateDoctorsLoginSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::updateOrInsert(
            ['name' => 'Dr. Sarah Johnson'],
            [
                'email' => 'sarah@nuh.com',
                'password' => Hash::make(env('SEED_DOCTOR_PASSWORD', Str::password(32))),
                'updated_at' => now(),
            ]
        );
        Doctor::updateOrInsert(
            ['name' => 'Dr. Michael Chen'],
            [
                'email' => 'michael@nuh.com',
                'password' => Hash::make(env('SEED_DOCTOR_PASSWORD', Str::password(32))),
                'updated_at' => now(),
            ]
        );
    }
}
