<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->unsignedInteger('floor')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['type', 'floor', 'capacity', 'patient_id']);
        });
    }
};
