<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('doctor_availabilities')) {
            Schema::create('doctor_availabilities', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
                $table->enum('schedule_type', ['hospital', 'private_clinic']);
                $table->unsignedInteger('appointment_duration_minutes');
                $table->unsignedInteger('break_between_appointments_minutes')->default(0);
                $table->unsignedInteger('booking_window_days')->default(30);
                $table->unsignedInteger('min_notice_minutes')->default(0);
                $table->string('timezone')->default('Africa/Cairo');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $this->addIndexIfMissing('doctor_availabilities', 'doctor_availabilities_doctor_id_index', ['doctor_id']);
        $this->addIndexIfMissing('doctor_availabilities', 'doctor_availabilities_doctor_id_schedule_type_index', ['doctor_id', 'schedule_type']);
        $this->addIndexIfMissing('doctor_availabilities', 'doctor_availabilities_doctor_type_active_index', ['doctor_id', 'schedule_type', 'is_active']);

        $this->addCheck('doctor_availabilities', 'doctor_availabilities_duration_positive', 'appointment_duration_minutes > 0');
        $this->addCheck('doctor_availabilities', 'doctor_availabilities_booking_window_positive', 'booking_window_days > 0');
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_availabilities');
    }

    private function addCheck(string $table, string $name, string $expression): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        if ($this->hasCheck($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
    }

    private function addIndexIfMissing(string $table, string $name, array $columns): void
    {
        if (Schema::hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
            $blueprint->index($columns, $name);
        });
    }

    private function hasCheck(string $table, string $name): bool
    {
        $rows = DB::select(
            <<<'SQL'
            SELECT CONSTRAINT_NAME
            FROM information_schema.CHECK_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND CONSTRAINT_NAME = ?
            LIMIT 1
            SQL,
            [$name]
        );

        return $rows !== [];
    }
};
