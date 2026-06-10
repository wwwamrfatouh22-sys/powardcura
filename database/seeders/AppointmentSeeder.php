<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Support\AppointmentSecurity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasTable('patients') || ! Schema::hasTable('doctors')) {
            return;
        }

        $patients = Patient::query()->with('user')->orderBy('id')->get()->values();
        $doctors = Doctor::query()
            ->whereNotNull('department_id')
            ->orderBy('department_id')
            ->orderBy('id')
            ->get()
            ->values();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $reasons = [
            'Initial consultation',
            'Follow-up visit',
            'Medication review',
            'Routine checkup',
            'Lab result discussion',
            'Post-treatment review',
            'Chronic condition monitoring',
        ];
        $statuses = ['Completed', 'Completed', 'Confirmed', 'Confirmed', 'Pending', 'Canceled', 'Confirmed'];
        $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '17:00'];

        foreach ($doctors as $doctorIndex => $doctor) {
            foreach (range(0, 6) as $visitIndex) {
                $sequence = ($doctorIndex * 7) + $visitIndex + 1;
                $marker = sprintf('DEMO-APPT-%04d', $sequence);
                $patient = $patients[($doctorIndex * 3 + $visitIndex) % $patients->count()];
                $status = $statuses[($doctorIndex + $visitIndex) % count($statuses)];
                $date = $this->appointmentDate($doctorIndex, $visitIndex, $status);
                $time = $times[$visitIndex];
                [$date, $time] = $this->availableSlot($doctor->id, $date, $time, $marker);
                [$firstName, $lastName] = $this->patientNameParts((string) $patient->full_name);

                $appointment = Appointment::query()
                    ->where('type', 'hospital')
                    ->where('reason', 'like', $marker.'%')
                    ->firstOrNew();

                $appointment->fill([
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'department_id' => $doctor->department_id,
                    'date' => $date,
                    'time' => $time,
                    'type' => 'hospital',
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $patient->user?->email,
                    'phone' => $patient->phone,
                    'reason' => $marker.' | '.$reasons[($doctorIndex + $visitIndex) % count($reasons)],
                    'status' => $status,
                    'payment_method' => 'pay_at_hospital',
                    'payment_amount' => 350,
                    'payment_status' => $status === 'Completed' ? 'paid' : 'pending',
                    'cancellation_reason' => $status === 'Canceled' ? 'Demo schedule conflict' : null,
                    'canceled_at' => $status === 'Canceled' ? Carbon::parse($date)->subDay() : null,
                ]);
                $appointment->save();

                $this->upsertPayment($appointment, $marker);
            }
        }
    }

    private function appointmentDate(int $doctorIndex, int $visitIndex, string $status): string
    {
        if ($status === 'Completed') {
            return now()->subDays(1 + (($doctorIndex + $visitIndex) % 14))->toDateString();
        }

        return now()->addDays(($doctorIndex + $visitIndex) % 14)->toDateString();
    }

    /**
     * @return array{0:string,1:string}
     */
    private function availableSlot(int $doctorId, string $date, string $time, string $marker): array
    {
        $candidate = Carbon::parse($date.' '.$time);

        for ($attempt = 0; $attempt < 60; $attempt++) {
            $normalizedTime = AppointmentSecurity::normalizeTime($candidate->format('H:i'));
            $occupied = AppointmentSecurity::blockingAppointments($doctorId, $candidate->toDateString())
                ->where(function ($query) use ($normalizedTime): void {
                    $query->where('time', $normalizedTime)
                        ->orWhere('time', $normalizedTime . ':00');
                })
                ->where('type', 'hospital')
                ->where('reason', 'not like', $marker.'%')
                ->exists();

            if (! $occupied) {
                return [$candidate->toDateString(), $candidate->format('H:i')];
            }

            $candidate->addDay();
        }

        return [$candidate->toDateString(), $candidate->format('H:i')];
    }

    /**
     * @return array{0:string,1:string}
     */
    private function patientNameParts(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim(explode('/', $fullName)[0])) ?: [];
        $first = array_shift($parts) ?: 'Patient';

        return [$first, implode(' ', $parts) ?: 'Guest'];
    }

    private function upsertPayment(Appointment $appointment, string $marker): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        $paid = $appointment->status === 'Completed';

        Payment::updateOrCreate(
            ['reference_number' => $marker],
            [
                'appointment_id' => $appointment->id,
                'payment_method' => 'pay_at_hospital',
                'amount' => 350,
                'status' => $paid ? 'paid' : 'pending',
                'paid_at' => $paid ? Carbon::parse($appointment->date)->setTime(12, 0) : null,
            ]
        );
    }
}
