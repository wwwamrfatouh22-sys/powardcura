<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->string('phone')->nullable()->index();
            $table->string('request_type');
            $table->text('notes')->nullable();
            $table->enum('priority', ['normal', 'urgent'])->default('normal')->index();
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending')->index();
            $table->string('uploaded_result')->nullable();
            $table->foreignId('completed_by_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'patient_id']);
            $table->index(['appointment_id', 'status']);
        });

        Schema::create('radiology_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->string('phone')->nullable()->index();
            $table->string('request_type');
            $table->text('notes')->nullable();
            $table->enum('priority', ['normal', 'urgent'])->default('normal')->index();
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending')->index();
            $table->string('uploaded_result')->nullable();
            $table->foreignId('completed_by_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'patient_id']);
            $table->index(['appointment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_requests');
        Schema::dropIfExists('lab_requests');
    }
};
