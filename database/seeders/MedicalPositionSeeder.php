<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\MedicalPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MedicalPositionSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('medical_positions') || ! Schema::hasTable('departments')) {
            return;
        }

        $departments = Department::query()->orderBy('name_en')->pluck('id')->values();

        if ($departments->isEmpty()) {
            return;
        }

        foreach ($this->positions() as $index => $position) {
            MedicalPosition::updateOrCreate(
                ['phone' => $position['phone']],
                $position + [
                    'department_id' => $departments[$index % $departments->count()],
                ]
            );
        }
    }

    private function positions(): array
    {
        return [
            ['name' => 'Sarah Johnson', 'age' => 34, 'gender' => 'female', 'phone' => '010000000000', 'cv' => 'cv1.pdf', 'status' => 'pending'],
            ['name' => 'Robert Williams', 'age' => 45, 'gender' => 'male', 'phone' => '010000000001', 'cv' => 'cv2.pdf', 'status' => 'pending'],
            ['name' => 'Emma Davis', 'age' => 28, 'gender' => 'female', 'phone' => '010000000002', 'cv' => 'cv3.pdf', 'status' => 'pending'],
            ['name' => 'Michael Brown', 'age' => 52, 'gender' => 'male', 'phone' => '010000000003', 'cv' => 'cv4.pdf', 'status' => 'pending'],
            ['name' => 'Jennifer Taylor', 'age' => 41, 'gender' => 'female', 'phone' => '010000000004', 'cv' => 'cv5.pdf', 'status' => 'approved'],
        ];
    }
}
