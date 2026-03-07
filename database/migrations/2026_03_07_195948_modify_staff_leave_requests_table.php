<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_leave_requests', function (Blueprint $table) {

            $table->dropColumn(['type','staff_id']);

            $table->unsignedBigInteger('doctor_id')->nullable();

            $table->unsignedBigInteger('nurse_id')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('staff_leave_requests', function (Blueprint $table) {

            $table->dropColumn(['doctor_id','nurse_id']);

            $table->enum('type',['doctor','nurse']);

            $table->unsignedBigInteger('staff_id');

        });
    }
};
