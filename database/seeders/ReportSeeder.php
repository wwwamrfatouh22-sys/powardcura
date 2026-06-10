<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('reports') || ! Schema::hasTable('patients') || ! Schema::hasTable('doctors')) {
            return;
        }

        $patients = Patient::query()->orderBy('id')->get()->values();
        $doctors = Doctor::query()->whereNotNull('department_id')->orderBy('id')->get()->values();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $types = ['Lab Results', 'X-Ray Report', 'ECG Report', 'MRI Scan', 'CT Scan', 'Blood Test', 'Surgery Report'];
        $priorities = ['Normal', 'Normal', 'High', 'Urgent'];
        $statuses = ['Pending', 'Completed', 'In Review', 'Completed'];

        foreach (range(1, 30) as $index) {
            $doctor = $doctors[($index - 1) % $doctors->count()];
            $patient = $patients[(($index - 1) * 3) % $patients->count()];

            Report::updateOrCreate(
                ['report_number' => 'DEMO-RPT-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT)],
                [
                    'report_type' => $types[($index - 1) % count($types)],
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'department_id' => $doctor->department_id,
                    'priority' => $priorities[($index - 1) % count($priorities)],
                    'status' => $statuses[($index - 1) % count($statuses)],
                    'is_reviewed' => $index % 3 === 0,
                ]
            );
        }
    }
}
