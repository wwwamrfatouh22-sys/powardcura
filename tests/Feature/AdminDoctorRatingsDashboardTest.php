<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Rating;
use App\Services\DoctorRatingDashboardService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDoctorRatingsDashboardTest extends TestCase
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

    public function test_unrated_doctors_are_tracked_in_ratings_dashboard(): void
    {
        $admin = Admin::query()->firstOrCreate(
            ['email' => 'ratings-admin@example.com'],
            ['password' => bcrypt('password')]
        );
        $department = Department::query()->firstOrCreate(
            ['name_en' => 'Ratings Probe Department'],
            ['name_ar' => 'Ratings Probe Department', 'status' => 'active']
        );

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Dr. Dashboard Probe',
            'email' => 'dashboard-probe-doctor@example.com',
            'password' => bcrypt('password'),
            'specialization' => 'Analytics Probe',
            'image' => 'logo_Image.png',
            'experience' => 5,
            'rating' => 4.5,
            'status' => 'Available',
        ]);

        $dashboard = app(DoctorRatingDashboardService::class)->build();

        $this->assertSame(Doctor::query()->withoutTrashed()->count(), $dashboard['summary']['trackedDoctors']);
        $this->assertSame(Doctor::query()->withoutTrashed()->count(), $dashboard['performanceRows']->count());
        $this->assertSame(Doctor::query()->withoutTrashed()->count(), $dashboard['ratingsRows']->count());
        $this->assertNotContains($doctor->name, $dashboard['charts']['overview']['labels']);
        $this->assertNotContains($doctor->name, $dashboard['charts']['trend']['labels']);
        $this->assertSame(
            Doctor::query()->withoutTrashed()->count(),
            array_sum($dashboard['charts']['distribution']['counts'])
        );
        $this->assertTrue($dashboard['performanceRows']->contains(fn (array $row) => $row['name'] === $doctor->name
            && $row['avg_rating'] === 0.0
            && $row['reviews_count'] === 0
            && $row['latest_feedback_comment'] === null
            && $row['status']['label'] === 'No Reviews'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Doctor Load Analysis', false);
        $response->assertDontSee('Doctors Performance Table', false);
        $response->assertSee('Doctors Ratings Table', false);
        $response->assertSee('Doctors Rating Overview', false);
        $response->assertSee('Ratings Distribution', false);
        $response->assertSee('Rating Trend', false);
        $response->assertDontSee('topBusyDoctorsChart', false);
        $response->assertSee('Dr. Dashboard Probe', false);
        $response->assertSee('No reviews yet', false);
        $response->assertSee('No Data', false);
        $response->assertSee('No Reviews', false);
        $response->assertSee('doctors-ratings-table-section', false);
    }

    public function test_direct_doctor_ratings_are_counted_without_appointment_inner_join(): void
    {
        $department = Department::query()->firstOrCreate(
            ['name_en' => 'Direct Rating Department'],
            ['name_ar' => 'Direct Rating Department', 'status' => 'active']
        );
        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Dr. Direct Rating Probe',
            'email' => 'direct-rating-probe-doctor@example.com',
            'password' => bcrypt('password'),
            'specialization' => 'Rating Probe',
            'image' => 'logo_Image.png',
            'experience' => 7,
            'rating' => 4.5,
            'status' => 'Available',
        ]);

        Rating::query()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => null,
            'rating' => 4,
            'comment' => 'Clear explanation',
        ]);

        $dashboard = app(DoctorRatingDashboardService::class)->build();
        $row = $dashboard['performanceRows']->firstWhere('name', $doctor->name);

        $this->assertNotNull($row);
        $this->assertSame(4.0, $row['avg_rating']);
        $this->assertSame(1, $row['reviews_count']);
        $this->assertSame('Clear explanation', $row['latest_feedback_comment']);
        $this->assertContains($doctor->name, $dashboard['charts']['overview']['labels']);
        $this->assertContains($doctor->name, $dashboard['charts']['trend']['labels']);
        $this->assertCount(count($dashboard['charts']['overview']['labels']), $dashboard['charts']['overview']['values']);
        $this->assertCount(count($dashboard['charts']['trend']['labels']), $dashboard['charts']['trend']['values']);
        $this->assertNotContains(null, $dashboard['charts']['overview']['labels']);
        $this->assertNotContains(null, $dashboard['charts']['trend']['values']);
    }

    private function readEnv(): array
    {
        $path = base_path('.env');

        if (! is_file($path)) {
            return [];
        }

        return collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->filter(fn (string $line) => ! str_starts_with(trim($line), '#') && str_contains($line, '='))
            ->mapWithKeys(function (string $line) {
                [$key, $value] = explode('=', $line, 2);

                return [trim($key) => trim($value, " \t\n\r\0\x0B\"'")];
            })
            ->all();
    }
}
