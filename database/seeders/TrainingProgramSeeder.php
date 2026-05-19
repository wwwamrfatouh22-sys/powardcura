<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\TrainingProgram;
use Illuminate\Database\Seeder;

class TrainingProgramSeeder extends Seeder
{
    public function run(): void
    {
        $departmentIds = Department::query()->orderBy('name_en')->pluck('id')->values();

        foreach ($this->programs() as $index => $program) {
            TrainingProgram::updateOrCreate(
                ['title' => $program['title']],
                $program + [
                    'department_id' => $departmentIds->isNotEmpty()
                        ? $departmentIds[$index % $departmentIds->count()]
                        : null,
                    'is_active' => true,
                ]
            );
        }
    }

    private function programs(): array
    {
        return [
            [
                'title' => 'Clinical Internship Program',
                'description' => 'Hands-on clinical training for final-year students in core departments.',
                'duration_weeks' => 12,
            ],
            [
                'title' => 'Nursing Residency Program',
                'description' => 'Structured residency with supervised rounds and skill labs.',
                'duration_weeks' => 16,
            ],
            [
                'title' => 'Radiology Observership',
                'description' => 'Department-based radiology observership for recent graduates.',
                'duration_weeks' => 8,
            ],
        ];
    }
}
