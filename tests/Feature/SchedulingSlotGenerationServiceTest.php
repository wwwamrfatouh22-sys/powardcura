<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\BlockedTime;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorTimeOff;
use App\Services\Scheduling\SlotGenerationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchedulingSlotGenerationServiceTest extends TestCase
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
        Carbon::setTestNow(Carbon::parse('2026-05-04 08:00:00', 'Africa/Cairo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    public function test_normal_slot_generation(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability();
        $this->addSchedule($availability, 1, '09:00', '11:00');

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame([
            ['date' => '2026-05-04', 'start_time' => '09:00', 'end_time' => '09:30', 'schedule_type' => 'hospital'],
            ['date' => '2026-05-04', 'start_time' => '09:40', 'end_time' => '10:10', 'schedule_type' => 'hospital'],
            ['date' => '2026-05-04', 'start_time' => '10:20', 'end_time' => '10:50', 'schedule_type' => 'hospital'],
        ], $slots);
    }

    public function test_multiple_shifts_are_supported(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 60, break: 0);
        $this->addSchedule($availability, 1, '09:00', '11:00');
        $this->addSchedule($availability, 1, '14:00', '16:00');

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame(['09:00', '10:00', '14:00', '15:00'], array_column($slots, 'start_time'));
    }

    public function test_time_off_removes_overlapping_slots(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 30, break: 0);
        $this->addSchedule($availability, 1, '09:00', '11:00');

        DoctorTimeOff::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => null,
            'starts_at' => '2026-05-04 09:30:00',
            'ends_at' => '2026-05-04 10:30:00',
            'reason' => 'Vacation window',
        ]);

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame(['09:00', '10:30'], array_column($slots, 'start_time'));
    }

    public function test_blocked_time_removes_overlapping_slots(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 30, break: 0);
        $this->addSchedule($availability, 1, '09:00', '11:00');

        BlockedTime::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'starts_at' => '2026-05-04 10:00:00',
            'ends_at' => '2026-05-04 10:30:00',
            'reason' => 'Manual block',
            'source' => 'manual',
            'is_active' => true,
        ]);

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame(['09:00', '09:30', '10:30'], array_column($slots, 'start_time'));
    }

    public function test_existing_appointment_removes_slot(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 30, break: 0);
        $this->addSchedule($availability, 1, '09:00', '11:00');

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $doctor->department_id,
            'date' => '2026-05-04',
            'time' => '09:30',
            'status' => 'Confirmed',
            'first_name' => 'Booked',
            'last_name' => 'Patient',
            'phone' => '01000000000',
            'type' => 'hospital',
        ]);

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame(['09:00', '10:00', '10:30'], array_column($slots, 'start_time'));
    }

    public function test_min_notice_blocks_near_slots(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 30, break: 0, minNotice: 90);
        $this->addSchedule($availability, 1, '09:00', '11:00');

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-04');

        $this->assertSame(['09:30', '10:00', '10:30'], array_column($slots, 'start_time'));
    }

    public function test_booking_window_prevents_far_future_slots(): void
    {
        [$doctor, $availability] = $this->doctorWithAvailability(duration: 30, break: 0, bookingWindow: 3);
        $this->addSchedule($availability, 5, '09:00', '10:00');

        $slots = $this->service()->generate($doctor->id, 'hospital', '2026-05-08');

        $this->assertSame([], $slots);
    }

    private function service(): SlotGenerationService
    {
        return app(SlotGenerationService::class);
    }

    /**
     * @return array{0:Doctor,1:DoctorAvailability}
     */
    private function doctorWithAvailability(
        int $duration = 30,
        int $break = 10,
        int $bookingWindow = 30,
        int $minNotice = 0
    ): array {
        $department = Department::query()->create([
            'name_en' => 'Scheduling Test ' . uniqid(),
            'name_ar' => 'Scheduling Test',
            'image' => 'test.jpg',
            'status' => 'active',
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Scheduling Doctor ' . uniqid(),
            'specialization' => 'Scheduling',
            'image' => 'doctor.jpg',
            'experience' => 5,
            'rating' => 4.5,
            'email' => 'scheduling-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'status' => 'Available',
        ]);

        $availability = DoctorAvailability::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => 'hospital',
            'appointment_duration_minutes' => $duration,
            'break_between_appointments_minutes' => $break,
            'booking_window_days' => $bookingWindow,
            'min_notice_minutes' => $minNotice,
            'timezone' => 'Africa/Cairo',
            'is_active' => true,
        ]);

        return [$doctor, $availability];
    }

    private function addSchedule(DoctorAvailability $availability, int $dayOfWeek, string $start, string $end): void
    {
        $availability->schedules()->create([
            'day_of_week' => $dayOfWeek,
            'start_time' => $start,
            'end_time' => $end,
            'location_type' => $availability->schedule_type,
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
