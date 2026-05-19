<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->deduplicateAppointmentSlots();

        $patients = Patient::query()->orderBy('id')->get();
        $doctors = Doctor::query()
            ->with('department')
            ->whereNotNull('department_id')
            ->orderBy('department_id')
            ->orderBy('id')
            ->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $timeSlots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '17:00', '17:30', '18:00', '18:30'];
        $reasons = [
            'Initial consultation',
            'Follow-up visit',
            'Medication review',
            'Routine checkup',
            'Lab result discussion',
            'Post-treatment review',
            'Specialist referral',
            'Chronic condition monitoring',
        ];
        $statuses = ['Confirmed', 'Completed', 'Canceled', 'Confirmed'];

        $created = 0;

        foreach ($doctors as $doctorIndex => $doctor) {
            foreach (range(0, 1) as $visitIndex) {
                $patient = $patients[($doctorIndex * 2 + $visitIndex) % $patients->count()];
                $status = $statuses[($doctorIndex + $visitIndex) % count($statuses)];
                $date = $this->appointmentDate($doctorIndex, $visitIndex, $status);
                $time = $timeSlots[($doctorIndex + ($visitIndex * 3)) % count($timeSlots)];
                $nameParts = $this->patientNameParts((string) $patient->full_name);

                $appointment = Appointment::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'date' => $date,
                        'time' => $time,
                        'type' => 'hospital',
                    ],
                    [
                        'patient_id' => $patient->id,
                        'department_id' => $doctor->department_id,
                        'first_name' => $nameParts[0],
                        'last_name' => $nameParts[1],
                        'email' => $patient->user?->email,
                        'phone' => $patient->phone,
                        'reason' => $reasons[($doctorIndex + $visitIndex) % count($reasons)],
                        'status' => $status,
                        'payment_method' => 'pay_at_hospital',
                        'payment_amount' => 350,
                        'payment_status' => $status === 'Completed' ? 'paid' : 'pending',
                    ]
                );

                $this->upsertPayment($appointment);
                $created++;
            }
        }

        $this->createQueueDepth($patients, $doctors, $timeSlots, $reasons, $created);
        $this->deduplicateAppointmentSlots();
    }

    private function appointmentDate(int $doctorIndex, int $visitIndex, string $status): string
    {
        if ($status === 'Completed') {
            return now()->subDays(1 + ($doctorIndex % 5))->toDateString();
        }

        if ($status === 'Canceled') {
            return now()->addDays(2 + ($doctorIndex % 6))->toDateString();
        }

        return now()->addDays(($doctorIndex + $visitIndex) % 7)->toDateString();
    }

    private function createQueueDepth($patients, $doctors, array $timeSlots, array $reasons, int $offset): void
    {
        $today = now()->toDateString();
        $queueDoctors = $doctors->take(min(8, $doctors->count()))->values();

        foreach ($queueDoctors as $index => $doctor) {
            $patient = $patients[($offset + $index) % $patients->count()];
            $time = $timeSlots[$index % count($timeSlots)];
            $nameParts = $this->patientNameParts((string) $patient->full_name);

            $appointment = Appointment::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'date' => $today,
                    'time' => $time,
                    'type' => 'hospital',
                ],
                [
                    'patient_id' => $patient->id,
                    'department_id' => $doctor->department_id,
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1],
                    'email' => $patient->user?->email,
                    'phone' => $patient->phone,
                    'reason' => $reasons[$index % count($reasons)],
                    'status' => 'Confirmed',
                    'payment_method' => 'pay_at_hospital',
                    'payment_amount' => 350,
                    'payment_status' => 'pending',
                ]
            );

            $this->upsertPayment($appointment);
        }
    }

    /**
     * @return array{0:string,1:string}
     */
    private function patientNameParts(string $fullName): array
    {
        $englishName = trim(explode('/', $fullName)[0]);
        $parts = preg_split('/\s+/', $englishName) ?: [];
        $first = array_shift($parts) ?: 'Patient';
        $last = implode(' ', $parts) ?: 'Guest';

        return [$first, $last];
    }

    private function upsertPayment(Appointment $appointment): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        $paid = $appointment->status === 'Completed';

        Payment::updateOrCreate(
            ['reference_number' => 'SEED-HOSPITAL-' . $appointment->id],
            [
                'appointment_id' => $appointment->id,
                'payment_method' => $appointment->payment_method ?: 'pay_at_hospital',
                'amount' => $appointment->payment_amount ?: 350,
                'status' => $paid ? 'paid' : 'pending',
                'paid_at' => $paid ? Carbon::parse($appointment->date)->setTime(12, 0) : null,
            ]
        );
    }

    private function deduplicateAppointmentSlots(): void
    {
        Appointment::query()
            ->whereNotNull('doctor_id')
            ->whereNotNull('date')
            ->whereNotNull('time')
            ->get()
            ->groupBy(fn (Appointment $appointment) => implode('|', [
                $appointment->doctor_id,
                $appointment->date,
                substr((string) $appointment->time, 0, 5),
                $appointment->type ?: 'hospital',
            ]))
            ->filter(fn ($appointments) => $appointments->count() > 1)
            ->each(function ($appointments): void {
                $ordered = $appointments
                    ->sortBy([
                        fn (Appointment $appointment) => match ($appointment->status) {
                            'Confirmed' => 0,
                            'Pending' => 1,
                            'Completed' => 2,
                            'Canceled', 'Cancelled' => 3,
                            default => 4,
                        },
                        fn (Appointment $appointment) => $appointment->id,
                    ])
                    ->values();

                $duplicateIds = $ordered->slice(1)->pluck('id');

                if ($duplicateIds->isNotEmpty()) {
                    Appointment::query()->whereKey($duplicateIds)->delete();
                }
            });
    }
}
