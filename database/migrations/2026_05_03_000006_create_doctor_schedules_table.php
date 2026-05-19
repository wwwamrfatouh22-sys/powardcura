<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_availability_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('location_type', ['hospital', 'private_clinic']);
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_availability_id', 'day_of_week'], 'doctor_schedules_availability_day_index');
            $table->index(['doctor_availability_id', 'day_of_week', 'is_active'], 'doctor_schedules_availability_day_active_index');
        });

        $this->addCheck('doctor_schedules', 'doctor_schedules_day_of_week_range', 'day_of_week BETWEEN 0 AND 6');
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }

    private function addCheck(string $table, string $name, string $expression): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
    }
};
