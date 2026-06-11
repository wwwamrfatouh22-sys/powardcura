<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PatientBookingSlotUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $env = $this->readEnv();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $env['DB_HOST'] ?? '127.0.0.1',
            'database.connections.mysql.port' => $env['DB_PORT'] ?? '3306',
            'database.connections.mysql.database' => $env['DB_DATABASE'] ?? 'cura_axis',
            'database.connections.mysql.username' => $env['DB_USERNAME'] ?? 'root',
            'database.connections.mysql.password' => $env['DB_PASSWORD'] ?? '',
        ]);

        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::beginTransaction();
        Carbon::setTestNow(Carbon::parse('2026-06-08 08:00:00', 'Africa/Cairo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    public function test_slot_endpoint_returns_available_and_unavailable_slots(): void
    {
        $doctor = $this->doctorWithSchedule();

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-06-08',
            'time' => '09:00:00',
            'status' => 'Confirmed',
            'first_name' => 'Booked',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $response = $this->getJson(route('doctors.booked-slots', [
            'doctor' => $doctor,
            'date' => '2026-06-08',
            'type' => 'hospital',
        ]));

        $response->assertOk()
            ->assertJsonPath('slots.0.time', '09:00')
            ->assertJsonPath('slots.0.available', false)
            ->assertJsonPath('slots.0.label', 'Slot is already booked')
            ->assertJsonPath('slots.1.time', '09:30')
            ->assertJsonPath('slots.1.available', true);
    }

    public function test_doctor_page_uses_same_origin_booking_urls_and_images(): void
    {
        $doctor = $this->doctorWithSchedule();

        $response = $this->get(route('doctors.show', [
            'doctor' => $doctor,
            'date' => '2026-06-08',
            'type' => 'hospital',
        ]));

        $response->assertOk()
            ->assertSee('const doctorId = '.$doctor->id.';', false)
            ->assertSee('const fetchUrl = `/doctors/${doctorId}/booked-slots?', false)
            ->assertSee('continueBooking.href = `/appointment/${doctorId}/${encodeURIComponent(selectedSlot)}?', false)
            ->assertSee('src="/images/doctor.jpg"', false)
            ->assertDontSee('window.location.origin', false)
            ->assertDontSee('http://', false);
    }

    public function test_canceled_appointment_does_not_block_visible_slot(): void
    {
        $doctor = $this->doctorWithSchedule();

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-06-08',
            'time' => '09:00:00',
            'status' => ' CANCELED ',
            'first_name' => 'Canceled',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $this->getJson(route('doctors.booked-slots', [
            'doctor' => $doctor,
            'date' => '2026-06-08',
            'type' => 'hospital',
        ]))
            ->assertOk()
            ->assertJsonPath('slots.0.time', '09:00')
            ->assertJsonPath('slots.0.available', true)
            ->assertJsonPath('slots.0.label', 'Available');
    }

    public function test_confirmation_details_page_shows_stale_slot_feedback(): void
    {
        $doctor = $this->doctorWithSchedule();
        $patient = $this->patient();

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-06-08',
            'time' => '09:00',
            'status' => 'Confirmed',
            'first_name' => 'Booked',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $this->actingAs($patient, 'patient')
            ->get(route('appointments.create', [
                'doctor' => $doctor,
                'time' => '09:00',
                'date' => '2026-06-08',
                'type' => 'hospital',
            ]))
            ->assertOk()
            ->assertSee('Slot no longer available');
    }

    private function doctorWithSchedule(): Doctor
    {
        $department = Department::query()->create([
            'name_en' => 'Patient Slot UI ' . uniqid(),
            'name_ar' => 'Patient Slot UI',
            'image' => 'test.jpg',
            'status' => 'active',
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Patient Slot Doctor ' . uniqid(),
            'specialization' => 'Scheduling',
            'image' => 'doctor.jpg',
            'experience' => 5,
            'rating' => 4.5,
            'email' => 'patient-slot-doctor-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'status' => 'Available',
        ]);

        $availability = DoctorAvailability::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'appointment_duration_minutes' => 30,
            'break_between_appointments_minutes' => 0,
            'booking_window_days' => 30,
            'min_notice_minutes' => 0,
            'timezone' => 'Africa/Cairo',
            'is_active' => true,
        ]);

        $availability->schedules()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'location_type' => 'hospital',
            'is_active' => true,
        ]);

        return $doctor;
    }

    private function patient(): Patient
    {
        return Patient::query()->create([
            'national_id' => '28' . random_int(100000000000, 999999999999),
            'full_name' => 'Patient Slot Tester',
            'dob' => '1990-01-01',
            'phone' => '01000000000',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function readEnv(): array
    {
        $values = [];

        foreach (file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[$key] = trim($value, "\"'");
        }

        return $values;
    }
}
