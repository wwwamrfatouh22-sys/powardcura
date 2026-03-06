<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        $departments = Department::all();

        foreach (range(1, 30) as $index) {

            Report::create([
                'report_number' => 'RPT-' . str_pad($index, 3, '0', STR_PAD_LEFT),

                'report_type' => collect([
                    'Lab Results',
                    'X-Ray Report',
                    'ECG Report',
                    'MRI Scan',
                    'CT Scan',
                    'Blood Test',
                    'Surgery Report'
                ])->random(),

                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->random()->id,
                'department_id' => $departments->random()->id,

                'priority' => collect(['Normal', 'High', 'Urgent'])->random(),

                'status' => collect([
                    'Pending',
                    'Completed',
                    'In Review'
                ])->random(),
            ]);
        }
    }
}
