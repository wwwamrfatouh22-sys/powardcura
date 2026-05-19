<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Staff::create([
            'name' => 'Staff Admin',
            'full_name' => 'Staff Admin',
            'email' => 'staff@nuh.com',
            'role' => 'admin_assistant',
            'status' => 'active',
            'password' => Hash::make(env('SEED_STAFF_PASSWORD', Str::password(32))),
        ]);
    }
}
