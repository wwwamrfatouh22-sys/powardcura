<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Support\DepartmentCatalog;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_merge(DepartmentCatalog::requiredPairs(), DepartmentCatalog::legacyAllowed()) as $department) {
            Department::updateOrCreate(
                ['name_en' => $department['name_en']],
                [
                    'name_ar' => $department['name_ar'],
                    'status' => 'active',
                ]
            );
        }
    }
}
