<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_times', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->enum('schedule_type', ['hospital', 'private_clinic']);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('reason')->nullable();
            $table->enum('source', ['manual', 'appointment', 'system', 'maintenance']);
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'schedule_type', 'starts_at', 'ends_at'], 'blocked_times_doctor_type_range_index');
            $table->index('appointment_id');
            $table->index('source');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_times');
    }
};
