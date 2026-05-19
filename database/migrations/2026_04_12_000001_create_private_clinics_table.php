<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_clinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('clinic_name');
            $table->string('clinic_address');
            $table->string('clinic_phone');
            $table->decimal('clinic_fee', 10, 2)->nullable();
            $table->json('available_days')->nullable();
            $table->json('available_times')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_clinics');
    }
};
