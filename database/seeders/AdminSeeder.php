<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['password' => Hash::make(env('SEED_ADMIN_PASSWORD', '12345678'))]
        );
    }
}
