<?php

namespace Database\Seeders;

use App\Models\Nurse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NurseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nurse::create([
            'name' => 'Nurse Emily',
            'email' => 'emily@nuh.com',
            'password' => Hash::make('12345678'),
        ]);

        Nurse::create([
            'name' => 'Nurse Sarah',
            'email' => 'sarah@nuh.com',
            'password' => Hash::make('12345678'),
        ]);

        Nurse::create([
            'name' => 'Nurse Michael',
            'email' => 'michael@nuh.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
