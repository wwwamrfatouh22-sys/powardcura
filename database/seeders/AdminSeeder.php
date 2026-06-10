<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }

        $admin = Admin::firstOrNew(['email' => 'admin@gmail.com']);

        if (! $admin->password) {
            $admin->password = Hash::make(env('SEED_ADMIN_PASSWORD', 'Admin@12345'));
        }

        $admin->save();
    }
}
