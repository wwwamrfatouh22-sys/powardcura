<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_leave_requests', function (Blueprint $table) {
            $table->id();

            $table->enum('type',['doctor','nurse']);

            $table->unsignedBigInteger('staff_id');

            $table->date('start_date');

            $table->date('end_date');

            $table->text('reason')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leave_requests');
    }
};
