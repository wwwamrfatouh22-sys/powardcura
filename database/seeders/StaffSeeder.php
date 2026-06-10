<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('staff')) {
            return;
        }

        $staff = [
            [
                'name' => 'Staff Admin',
                'full_name' => 'Staff Admin',
                'email' => 'staff@nuh.com',
                'role' => 'admin_assistant',
                'status' => 'active',
            ],
            [
                'name' => 'Reception Officer',
                'full_name' => 'Reception Officer',
                'email' => 'reception@nuh.com',
                'role' => 'receptionist',
                'status' => 'active',
                'phone' => '01010000001',
            ],
            [
                'name' => 'Lab Technician',
                'full_name' => 'Lab Technician',
                'email' => 'lab@nuh.com',
                'role' => 'lab',
                'status' => 'active',
                'phone' => '01010000002',
            ],
            [
                'name' => 'Radiology Technician',
                'full_name' => 'Radiology Technician',
                'email' => 'radiology@nuh.com',
                'role' => 'radiology',
                'status' => 'active',
                'phone' => '01010000003',
            ],
            [
                'name' => 'Nurse Supervisor',
                'full_name' => 'Nurse Supervisor',
                'email' => 'nurse@nuh.com',
                'role' => 'nurse',
                'status' => 'active',
                'phone' => '01010000004',
            ],
            [
                'name' => 'Hospital Accountant',
                'full_name' => 'Hospital Accountant',
                'email' => 'accounts@nuh.com',
                'role' => 'accountant',
                'status' => 'active',
                'phone' => '01010000005',
            ],
            [
                'name' => 'Patient Support',
                'full_name' => 'Patient Support',
                'email' => 'support@nuh.com',
                'role' => 'support',
                'status' => 'active',
                'phone' => '01010000006',
            ],
        ];

        foreach ($staff as $member) {
            $record = Staff::withTrashed()->firstOrNew(['email' => $member['email']]);
            $record->fill($member);
            $record->deleted_at = null;

            if (! $record->password) {
                $record->password = Hash::make(env('SEED_STAFF_PASSWORD', 'Staff@12345'));
            }

            $record->save();
        }
    }
}
