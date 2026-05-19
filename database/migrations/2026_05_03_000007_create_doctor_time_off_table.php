<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_time_off', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->enum('schedule_type', ['hospital', 'private_clinic'])->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('reason')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'starts_at', 'ends_at']);
            $table->index(['doctor_id', 'schedule_type', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_time_off');
    }
};
