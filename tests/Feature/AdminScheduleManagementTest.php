<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\BlockedTime;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorTimeOff;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminScheduleManagementTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    public function test_admin_can_manage_doctor_schedule_records(): void
    {
        $admin = Admin::query()->create([
            'email' => 'schedule-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $doctor = $this->doctor();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.availability', $doctor), [
                'schedule_type' => 'hospital',
                'appointment_duration_minutes' => 25,
                'break_between_appointments_minutes' => 5,
                'booking_window_days' => 21,
                'min_notice_minutes' => 90,
                'timezone' => 'Africa/Cairo',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $availability = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', 'hospital')
            ->firstOrFail();

        $this->assertSame(25, $availability->appointment_duration_minutes);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.shifts.store', $doctor), [
                'schedule_type' => 'hospital',
                'day_of_week' => 1,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_schedules', [
            'doctor_availability_id' => $availability->id,
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'location_type' => 'hospital',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.time-off.store', $doctor), [
                'schedule_type' => null,
                'starts_at' => '2026-06-01 09:00:00',
                'ends_at' => '2026-06-03 17:00:00',
                'reason' => 'Vacation',
            ])
            ->assertRedirect();

        $this->assertSame(1, DoctorTimeOff::query()
            ->where('doctor_id', $doctor->id)
            ->where('reason', 'Vacation')
            ->count());

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.blocked-times.store', $doctor), [
                'schedule_type' => 'hospital',
                'starts_at' => '2026-06-04 10:00:00',
                'ends_at' => '2026-06-04 10:30:00',
                'reason' => 'Manual admin block',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_times', [
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'source' => 'manual',
            'reason' => 'Manual admin block',
            'is_active' => 1,
        ]);

        BlockedTime::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'starts_at' => '2026-06-05 10:00:00',
            'ends_at' => '2026-06-05 10:30:00',
            'reason' => 'Appointment hold',
            'source' => 'appointment',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.doctors.schedule', $doctor))
            ->assertOk()
            ->assertSee('Appointment hold')
            ->assertSee('Read only');
    }

    public function test_admin_schedule_rejects_overlapping_shifts(): void
    {
        $admin = Admin::query()->create([
            'email' => 'schedule-overlap-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $doctor = $this->doctor();
        $availability = $this->availability($doctor);

        $availability->schedules()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'location_type' => 'hospital',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.shifts.store', $doctor), [
                'schedule_type' => 'hospital',
                'day_of_week' => 1,
                'start_time' => '11:00',
                'end_time' => '13:00',
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('start_time');

        $this->assertSame(1, $availability->schedules()->count());
    }

    public function test_admin_sees_impact_warning_before_time_off_save(): void
    {
        $admin = Admin::query()->create([
            'email' => 'schedule-impact-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $doctor = $this->doctor();

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-06-02',
            'time' => '10:00',
            'status' => 'Confirmed',
            'first_name' => 'Impacted',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $payload = [
            'schedule_type' => 'hospital',
            'starts_at' => '2026-06-02 09:00:00',
            'ends_at' => '2026-06-02 11:00:00',
            'reason' => 'Leave',
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.time-off.store', $doctor), $payload)
            ->assertSessionHasErrors(['schedule_impact' => 'This change affects 1 appointments']);

        $this->assertDatabaseMissing('doctor_time_off', [
            'doctor_id' => $doctor->id,
            'reason' => 'Leave',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.doctors.schedule.time-off.store', $doctor), $payload + ['confirm_impact' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_time_off', [
            'doctor_id' => $doctor->id,
            'reason' => 'Leave',
        ]);
    }

    public function test_schedule_page_previews_slots_and_syncs_appointment_blocks(): void
    {
        $admin = Admin::query()->create([
            'email' => 'schedule-preview-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $doctor = $this->doctor();
        $availability = $this->availability($doctor);

        $availability->schedules()->create([
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'location_type' => 'hospital',
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-06-08',
            'time' => '09:00',
            'status' => 'Confirmed',
            'first_name' => 'Preview',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.doctors.schedule', [
                'doctor' => $doctor,
                'preview_schedule_type' => 'hospital',
                'preview_date' => '2026-06-08',
            ]))
            ->assertOk()
            ->assertSee('09:30 - 10:00')
            ->assertSee('Appointment #' . $appointment->id);

        $this->assertDatabaseHas('blocked_times', [
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'source' => 'appointment',
            'schedule_type' => 'hospital',
            'is_active' => 1,
        ]);
    }

    private function doctor(): Doctor
    {
        $department = Department::query()->create([
            'name_en' => 'Schedule Admin Test ' . uniqid(),
            'name_ar' => 'Schedule Admin Test',
            'image' => 'test.jpg',
            'status' => 'active',
        ]);

        return Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Schedule Admin Doctor ' . uniqid(),
            'specialization' => 'Scheduling',
            'image' => 'doctor.jpg',
            'experience' => 5,
            'rating' => 4.5,
            'email' => 'schedule-admin-doctor-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'status' => 'Available',
        ]);
    }

    private function availability(Doctor $doctor): DoctorAvailability
    {
        return DoctorAvailability::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'appointment_duration_minutes' => 30,
            'break_between_appointments_minutes' => 0,
            'booking_window_days' => 60,
            'min_notice_minutes' => 0,
            'timezone' => 'Africa/Cairo',
            'is_active' => true,
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
