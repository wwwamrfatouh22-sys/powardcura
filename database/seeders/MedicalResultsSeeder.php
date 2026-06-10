<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\RadiologyResult;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MedicalResultsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('patients')) {
            return;
        }

        $patients = Patient::query()->orderBy('id')->take(12)->get()->values();
        $appointments = Schema::hasTable('appointments')
            ? Appointment::query()->where('reason', 'like', 'DEMO-APPT-%')->orderBy('id')->get()->values()
            : collect();
        $labStaffId = Schema::hasTable('staff') ? Staff::query()->whereIn('role', ['lab', 'laboratory', 'radiology_lab'])->value('id') : null;
        $radiologyStaffId = Schema::hasTable('staff') ? Staff::query()->whereIn('role', ['radiology', 'radiology_lab'])->value('id') : null;

        foreach ($patients as $index => $patient) {
            $appointment = $appointments->isNotEmpty() ? $appointments[$index % $appointments->count()] : null;

            if (Schema::hasTable('lab_tests')) {
                LabTest::updateOrCreate(
                    ['patient_id' => $patient->id, 'title' => 'Demo Complete Blood Count'],
                    [
                        'appointment_id' => $appointment?->id,
                        'patient_phone' => $patient->phone,
                        'uploaded_by_staff_id' => $labStaffId,
                        'result_type' => 'laboratory',
                        'description' => 'CBC values within expected demo ranges.',
                        'notes' => 'Generated demo laboratory result.',
                        'file_name' => 'demo-cbc-'.($index + 1).'.pdf',
                        'test_date' => now()->subDays(($index % 10) + 1)->toDateString(),
                    ]
                );
            }

            if (Schema::hasTable('radiology_results')) {
                RadiologyResult::updateOrCreate(
                    ['patient_id' => $patient->id, 'title' => 'Demo Chest X-Ray'],
                    [
                        'appointment_id' => $appointment?->id,
                        'patient_phone' => $patient->phone,
                        'uploaded_by_staff_id' => $radiologyStaffId,
                        'result_type' => 'radiology',
                        'description' => 'No acute cardiopulmonary abnormality in this demo result.',
                        'notes' => 'Generated demo radiology result.',
                        'file_name' => 'demo-xray-'.($index + 1).'.pdf',
                        'scan_date' => now()->subDays(($index % 10) + 1)->toDateString(),
                    ]
                );
            }
        }
    }
}
